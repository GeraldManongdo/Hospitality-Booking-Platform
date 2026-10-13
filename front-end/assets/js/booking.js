(() => {
  const form=document.getElementById('booking-form');
  if(!form)return;
  const checkin=document.getElementById('checkin'), checkout=document.getElementById('checkout'), guests=document.getElementById('guests'), rooms=document.getElementById('rooms'), promo=document.getElementById('promo-code');
  let discount=0;
  const stayNights=()=>checkin.value&&checkout.value?Math.max(0,Math.round((new Date(`${checkout.value}T00:00:00`)-new Date(`${checkin.value}T00:00:00`))/86400000)):0;
  const totals=()=>{
    const nights=stayNights(),subtotal=nights*Number(window.XyvoraBookingRate||0)*Number(rooms.value||1),discountAmount=Math.round(subtotal*discount*100)/100,fee=Math.round(subtotal*.12*100)/100,total=subtotal-discountAmount+fee;
    document.getElementById('booking-price-lines').innerHTML=nights?`<div class="d-flex justify-content-between py-1"><span>$${Number(window.XyvoraBookingRate).toFixed(2)} × ${nights} night${nights===1?'':'s'} · ${guests.value} guest${guests.value==='1'?'':'s'}, ${rooms.value} room${rooms.value==='1'?'':'s'}</span><span>$${subtotal.toFixed(2)}</span></div>${discountAmount?`<div class="d-flex justify-content-between py-1 text-success"><span>Demo discount (10%)</span><span>−$${discountAmount.toFixed(2)}</span></div>`:''}<div class="d-flex justify-content-between py-1"><span>Estimated service fee</span><span>$${fee.toFixed(2)}</span></div>`:'<div class="d-flex justify-content-between py-1"><span>Choose dates for an estimate</span><span>—</span></div>';
    document.getElementById('booking-total').textContent=nights?`$${total.toFixed(2)}`:'—';
  };
  checkin.addEventListener('change',()=>{checkout.min=checkin.value;checkout.setCustomValidity(checkout.value&&checkout.value<=checkin.value?'Check-out must be after check-in.':'');totals();});
  checkout.addEventListener('change',()=>{checkout.setCustomValidity(checkout.value&&checkout.value<=checkin.value?'Check-out must be after check-in.':'');totals();});
  [guests,rooms].forEach((field)=>field.addEventListener('change',totals));
  document.getElementById('apply-promo').addEventListener('click',()=>{
    const message=document.getElementById('promo-message');
    if(promo.value.trim().toUpperCase()==='XYVORA10'){discount=.1;message.textContent='Demo code applied: a sample 10% discount is reflected in the estimate.';message.classList.remove('d-none','error');}
    else{discount=0;message.textContent='That demo code is not recognized. Try XYVORA10.';message.classList.remove('d-none');message.classList.add('error');}
    totals();
  });
  form.addEventListener('submit',(event)=>{
    event.preventDefault();event.stopPropagation();
    checkout.setCustomValidity(checkout.value&&checkout.value<=checkin.value?'Check-out must be after check-in.':'');
    form.classList.add('was-validated');
    if(!form.checkValidity())return;
    if(stayNights()<1){checkout.setCustomValidity('Check-out must be after check-in.');form.classList.add('was-validated');return;}
    const payload={propertyId:document.getElementById('booking-property-id').value,firstName:document.getElementById('first-name').value,lastName:document.getElementById('last-name').value,email:document.getElementById('email').value,phone:document.getElementById('phone').value,checkin:checkin.value,checkout:checkout.value,guests:Number(guests.value),rooms:Number(rooms.value),rate:Number(window.XyvoraBookingRate),discount};
    try{localStorage.setItem('xyvora-demo-booking',JSON.stringify(payload));location.href='checkout.php';}
    catch(error){const message=document.getElementById('promo-message');message.textContent='Your browser could not store this demo state. Please review browser storage settings and try again.';message.classList.remove('d-none');message.classList.add('error');console.error('Unable to store demo booking.',error);}
  });
  totals();
})();
