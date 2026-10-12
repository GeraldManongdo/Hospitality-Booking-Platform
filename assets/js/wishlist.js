(() => {
  const grid=document.getElementById('wishlist-grid'),empty=document.getElementById('wishlist-empty');
  if(!grid)return;
  const update=()=>{
    let saved=[];
    try{saved=JSON.parse(localStorage.getItem('xyvora-wishlist')||'[]');}
    catch(error){console.error('Unable to load saved demo stays.',error);}
    let visible=0;
    grid.querySelectorAll('[data-wishlist]').forEach((button)=>{
      const card=button.closest('.property-result');
      if(!card)return;
      card.hidden=!saved.includes(button.dataset.wishlist);
      if(!card.hidden)visible+=1;
    });
    empty.classList.toggle('d-none',visible>0);
  };
  grid.addEventListener('click',(event)=>{if(event.target.closest('[data-wishlist]'))setTimeout(update,0);});
  update();
})();
