(() => {
  const read = (key, fallback) => {
    try { return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback)); }
    catch (error) { console.warn(`Unable to read demo ${key} state.`, error); return fallback; }
  };
  const write = (key, value) => {
    try { localStorage.setItem(key, JSON.stringify(value)); return true; }
    catch (error) { console.error(`Unable to save demo ${key} state.`, error); return false; }
  };

  const wishlistButtons = [...document.querySelectorAll('[data-wishlist]')];
  const feedback = (message) => {
    let notice = document.getElementById('wishlist-feedback');
    if (!notice) {
      notice = document.createElement('div');
      notice.id = 'wishlist-feedback';
      notice.className = 'alert alert-warning container mt-3';
      notice.setAttribute('role', 'status');
      document.querySelector('main')?.prepend(notice);
    }
    if (notice) {
      notice.textContent = message;
      notice.hidden = false;
    }
  };
  const paint = (button, active) => {
    const savedLabel = active ? 'Remove from wishlist' : 'Add to wishlist';
    button.classList.toggle('is-saved', active);
    button.setAttribute('aria-pressed', String(active));
    button.setAttribute('aria-label', savedLabel);
    const icon = button.querySelector('i');
    if (icon) {
      icon.classList.toggle('bi-heart-fill', active);
      icon.classList.toggle('bi-heart', !active);
    }
    const label = button.querySelector('[data-wishlist-label]');
    if (label) label.textContent = active ? 'Saved' : 'Save';
  };
  const savedValues = read('xyvora-wishlist', []);
  const savedGuestIds = Array.isArray(savedValues) ? savedValues.map(String) : [];
  wishlistButtons.forEach((button) => paint(button, savedGuestIds.includes(button.dataset.wishlist)));

  if (window.XyvoraWishlist?.authenticated) {
    const body = new URLSearchParams({
      action: 'sync',
      csrf_token: window.XyvoraWishlist.csrfToken,
    });
    savedGuestIds.forEach((id) => body.append('property_ids[]', id));
    fetch(window.XyvoraWishlist.endpoint, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded', Accept: 'application/json' },
      body,
    })
      .then(async (response) => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.error || 'Saved stays could not be loaded.');
        const clearedGuestWishlist = write('xyvora-wishlist', []);
        const savedIds = data.property_ids.map(String);
        wishlistButtons.forEach((button) => paint(button, savedIds.includes(button.dataset.wishlist)));
        if (clearedGuestWishlist && savedGuestIds.length && document.getElementById('wishlist-grid')) {
          window.location.reload();
        }
      })
      .catch((error) => feedback(error.message));
  }

  wishlistButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const propertyId = button.dataset.wishlist;
      if (window.XyvoraWishlist?.authenticated) {
        button.disabled = true;
        const body = new URLSearchParams({
          csrf_token: window.XyvoraWishlist.csrfToken,
          property_id: propertyId,
        });
        fetch(window.XyvoraWishlist.endpoint, {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded', Accept: 'application/json' },
          body,
        })
          .then(async (response) => {
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Wishlist could not be updated.');
            paint(button, data.saved);
            const card = button.closest('.property-result');
            if (card && document.getElementById('wishlist-grid')) {
              card.hidden = !data.saved;
              const grid = document.getElementById('wishlist-grid');
              const empty = document.getElementById('wishlist-empty');
              if (empty) empty.classList.toggle('d-none', Boolean(grid.querySelector('.property-result:not([hidden])')));
            }
            const notice = document.getElementById('wishlist-feedback');
            if (notice) notice.hidden = true;
          })
          .catch((error) => feedback(error.message))
          .finally(() => { button.disabled = false; });
        return;
      }

      const stored = read('xyvora-wishlist', []);
      const current = Array.isArray(stored) ? stored.map(String) : [];
      const next = current.includes(propertyId)
        ? current.filter((item) => item !== propertyId)
        : [...current, propertyId];
      if (write('xyvora-wishlist', next)) {
        paint(button, next.includes(propertyId));
        const card = button.closest('.property-result');
        if (card && document.getElementById('wishlist-grid')) {
          card.hidden = !next.includes(propertyId);
          const grid = document.getElementById('wishlist-grid');
          const empty = document.getElementById('wishlist-empty');
          if (empty) empty.classList.toggle('d-none', Boolean(grid.querySelector('.property-result:not([hidden])')));
        }
      }
    });
  });

  document.querySelectorAll('form[data-demo-form]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      event.preventDefault();
      event.stopPropagation();
      form.classList.add('was-validated');
      if (!form.checkValidity()) return;
      const feedback = form.querySelector('[data-form-feedback]');
      if (feedback) {
        feedback.classList.remove('d-none', 'error');
        feedback.textContent = form.dataset.successMessage || 'Demo action complete. No information was sent or saved.';
      }
    });
  });

  document.querySelectorAll('[data-toggle-password]').forEach((button) => button.addEventListener('click', () => {
    const field = document.getElementById(button.dataset.togglePassword);
    if (!field) return;
    field.type = field.type === 'password' ? 'text' : 'password';
    button.setAttribute('aria-label', field.type === 'password' ? 'Show password' : 'Hide password');
  }));

  document.querySelectorAll('[data-password-confirm]').forEach((confirm) => {
    const password = document.getElementById(confirm.dataset.passwordConfirm);
    if (!password) return;
    const validate = () => { confirm.setCustomValidity(confirm.value && confirm.value !== password.value ? 'Passwords do not match.' : ''); };
    confirm.addEventListener('input', validate);
    password.addEventListener('input', validate);
  });

  document.querySelectorAll('[data-date-min-today]').forEach((date) => {
    const today = new Date();
    date.min = new Date(today.getTime() - today.getTimezoneOffset() * 60000).toISOString().slice(0, 10);
  });
})();
