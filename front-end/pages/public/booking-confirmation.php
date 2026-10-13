<?php
$pageTitle='Demo confirmation';
require __DIR__ . '/includes/customer-header.php';
?>
<main class="container py-5"><div class="row justify-content-center"><div class="col-lg-8"><div class="booking-card text-center p-4 p-md-5"><div class="feature-icon mx-auto mb-3"><i class="bi bi-check2-circle"></i></div><span class="eyebrow">DEMO COMPLETE</span><h1 class="h2 fw-bold mt-2">Your sample journey is ready.</h1><p class="text-secondary">This is a local demonstration confirmation only. No reservation was created, no property was contacted, and no payment was processed.</p><div class="alert-inline text-start my-4"><strong>Frontend demo status</strong><div id="confirmation-summary" class="small mt-1">Loading your sample summary…</div><div class="small mt-2">Reference <code id="confirmation-reference">—</code> · not a real booking number.</div></div><div class="d-flex flex-wrap justify-content-center gap-2"><a class="btn btn-primary" href="customer/bookings.php">View demo trips</a><a class="btn btn-outline-primary" href="properties.php">Explore more stays</a></div></div></div></div></main>
<script>
(() => {
  const output=document.getElementById('confirmation-summary');
  try{
    const confirmation=JSON.parse(localStorage.getItem('xyvora-demo-confirmation')||'null');
    if(!confirmation){output.textContent='There is no saved demo confirmation in this browser. No booking has been made.';return;}
    document.getElementById('confirmation-reference').textContent=confirmation.reference;
    output.textContent=`${confirmation.firstName} ${confirmation.lastName} · ${confirmation.checkin} to ${confirmation.checkout} · Illustrative total $${Number(confirmation.total).toFixed(2)}.`;
  }catch(error){output.textContent='The sample confirmation could not be read from this browser.';console.error('Unable to read demo confirmation.',error);}
})();
</script>
<?php require __DIR__ . '/includes/customer-footer.php'; ?>
