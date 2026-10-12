(() => {
  const read = (key, fallback) => {
    try { return JSON.parse(localStorage.getItem(key) || JSON.stringify(fallback)); }
    catch (error) { console.warn(`Unable to read demo ${key} state.`, error); return fallback; }
  };
  const write = (key, value) => {
    try { localStorage.setItem(key, JSON.stringify(value)); return true; }
    catch (error) { console.error(`Unable to save demo ${key} state.`, error); return false; }
  };

  document.querySelectorAll('[data-wishlist]').forEach((button) => {
    const id = button.dataset.wishlist;
    const saved = read('xyvora-wishlist', []);
    const paint = (active) => {
      button.classList.toggle('is-saved', active);
      button.setAttribute('aria-pressed', String(active));
      button.innerHTML = `<i class="bi ${active ? 'bi-heart-fill' : 'bi-heart'}"></i>`;
    };
    paint(saved.includes(id));
    button.addEventListener('click', () => {
      const current = read('xyvora-wishlist', []);
      const next = current.includes(id) ? current.filter((item) => item !== id) : [...current, id];
      if (write('xyvora-wishlist', next)) paint(next.includes(id));
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
