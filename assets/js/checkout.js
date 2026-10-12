(() => {
  const panel=document.getElementById('checkout-content'),empty=document.getElementById('checkout-empty');
  let booking;
  try{booking=JSON.parse(localStorage.getItem('xyvora-demo-booking')||'null');}
  catch(error){console.error('Unable to read demo booking summary.',error);}
  if(!booking){empty.classList.remove('d-none');return;}
  const stays=[
    {id:'stay-101',name:'Casa do Mar',location:'Lisbon, Portugal'},
    {id:'stay-102',name:'The Aoyama House',location:'Kyoto, Japan'},
    {id:'stay-103',name:'Villa Sorella',location:'Amalfi Coast, Italy'},
    {id:'stay-104',name:'Sundara Garden Stay',location:'Ubud, Bali'},
    {id:'stay-105',name:'Harbour House',location:'Copenhagen, Denmark'},
    {id:'stay-106',name:'Blue Hour Lodge',location:'Cape Town, South Africa'},
  ];
  const property=stays.find((stay)=>stay.id===booking.propertyId);
  if(!property||!booking.checkin||!booking.checkout){empty.classList.remove('d-none');return;}
  const nights=Math.round((new Date(`${booking.checkout}T00:00:00`)-new Date(`${booking.checkin}T00:00:00`))/86400000);
  if(nights<1){empty.classList.remove('d-none');return;}
  const subtotal=nights*booking.rate*booking.rooms,discount=Math.round(subtotal*(booking.discount||0)*100)/100,fee=Math.round(subtotal*.12*100)/100,total=subtotal-discount+fee;
  document.getElementById('checkout-property').textContent=property.name;
  document.getElementById('checkout-place').textContent=property.location;
  document.getElementById('checkout-in').textContent=booking.checkin;
  document.getElementById('checkout-out').textContent=booking.checkout;
  document.getElementById('checkout-guests').textContent=`${booking.guests} guest${booking.guests===1?'':'s'} · ${booking.rooms} room${booking.rooms===1?'':'s'}`;
  document.getElementById('checkout-guest').textContent=`${booking.firstName} ${booking.lastName}`;
  document.getElementById('checkout-email').textContent=booking.email;
  document.getElementById('checkout-lines').innerHTML=`<div class="d-flex justify-content-between py-1"><span>$${Number(booking.rate).toFixed(2)} × ${nights} nights × ${booking.rooms} room${booking.rooms===1?'':'s'}</span><span>$${subtotal.toFixed(2)}</span></div>${discount?`<div class="d-flex justify-content-between py-1 text-success"><span>Demo discount</span><span>−$${discount.toFixed(2)}</span></div>`:''}<div class="d-flex justify-content-between py-1"><span>Estimated service fee</span><span>$${fee.toFixed(2)}</span></div>`;
  document.getElementById('checkout-total').textContent=`$${total.toFixed(2)}`;
  panel.classList.remove('d-none');
  document.getElementById('confirm-demo').addEventListener('click',()=>{
    const confirmation={...booking,total,reference:`DEMO-${Date.now().toString().slice(-8)}`,createdAt:new Date().toISOString()};
    try{localStorage.setItem('xyvora-demo-confirmation',JSON.stringify(confirmation));location.href='booking-confirmation.php';}
    catch(error){const feedback=document.getElementById('checkout-feedback');feedback.textContent='This browser could not save the demo confirmation. Check browser storage settings and try again.';feedback.classList.remove('d-none');feedback.classList.add('error');console.error('Unable to store demo confirmation.',error);}
  });
})();
