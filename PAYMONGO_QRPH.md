# PayMongo QR Ph setup

Online bookings use PayMongo Checkout Sessions with QR Ph as the only enabled
payment method. A booking is confirmed only after the webhook verifies the
associated PaymentIntent as successful.

Apply `database/migrations/20261017_paymongo_qrph.sql` after importing the
database schema, including on a new installation. Configure these server-side environment variables; do not
commit secret values:

- `PAYMONGO_SECRET_KEY`: PayMongo secret API key (`sk_test_...` for testing or
  `sk_live_...` for production).
- `PAYMONGO_WEBHOOK_SECRET`: signing secret for the webhook endpoint.
- `APP_BASE_URL`: public base URL of this application, including any project
  subdirectory, for example `https://example.com/Hospitality-Booking-Platform`.

In the PayMongo dashboard, register
`<APP_BASE_URL>/back-end/controllers/paymongo-webhook.php` and enable the
`checkout_session.payment.paid` event. Use PayMongo test credentials and a
public HTTPS webhook URL when testing; localhost is not reachable by PayMongo.
The PHP cURL extension must be enabled.
