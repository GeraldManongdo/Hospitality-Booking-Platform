<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/paymongo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$payload = file_get_contents('php://input');
if ($payload === false) {
    http_response_code(400);
    exit('Webhook payload could not be read.');
}
$signature = (string) ($_SERVER['HTTP_PAYMONGO_SIGNATURE'] ?? '');
try {
    if (!paymongoVerifyWebhookSignature($payload, $signature)) {
        http_response_code(401);
        exit('Invalid webhook signature.');
    }
    $event = json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($event)) {
        http_response_code(400);
        exit('Invalid webhook payload.');
    }
} catch (JsonException $exception) {
    http_response_code(400);
    exit('Invalid webhook payload.');
} catch (RuntimeException $exception) {
    error_log('PayMongo webhook configuration error: ' . $exception->getMessage());
    http_response_code(500);
    exit('Payment processing is not configured.');
}

$eventAttributes = $event['data']['attributes'] ?? null;
$eventType = is_array($eventAttributes) ? ($eventAttributes['type'] ?? '') : '';
if ($eventType !== 'checkout_session.payment.paid') {
    http_response_code(200);
    exit('Event ignored.');
}

$eventData = is_array($eventAttributes) ? ($eventAttributes['data'] ?? null) : null;
$sessionId = is_array($eventData) ? ($eventData['id'] ?? null) : null;
if (!is_string($sessionId) || $sessionId === '') {
    http_response_code(400);
    exit('Checkout session ID is missing.');
}

try {
    $sessionResponse = paymongoRequest('GET', 'checkout_sessions/' . rawurlencode($sessionId));
    $sessionAttributes = $sessionResponse['data']['attributes'] ?? null;
    $paymentIntent = is_array($sessionAttributes) ? ($sessionAttributes['payment_intent'] ?? null) : null;
    $metadata = is_array($sessionAttributes) && is_array($sessionAttributes['metadata'] ?? null)
        ? $sessionAttributes['metadata']
        : [];
    $paymentIntentId = is_array($paymentIntent)
        ? ($paymentIntent['id'] ?? null)
        : (is_string($paymentIntent) ? $paymentIntent : null);
    if (!is_string($paymentIntentId) || $paymentIntentId === '') {
        throw new RuntimeException('PayMongo checkout session has no payment intent.');
    }
    $intentResponse = paymongoRequest('GET', 'payment_intents/' . rawurlencode($paymentIntentId));
    $intentAttributes = $intentResponse['data']['attributes'] ?? null;
    if (
        !is_array($sessionAttributes)
        || !is_array($intentAttributes)
        || ($intentAttributes['status'] ?? '') !== 'succeeded'
        || !isset($intentAttributes['amount'], $intentAttributes['currency'])
    ) {
        throw new RuntimeException('PayMongo payment intent is not successful.');
    }

    $database = getDatabaseConnection();
    $database->beginTransaction();
    $paymentQuery = $database->prepare(
        'SELECT payments.id,payments.booking_id,payments.provider_transaction_id,
                payments.amount,payments.currency,payments.status
         FROM payments INNER JOIN bookings ON bookings.id=payments.booking_id
         WHERE payments.provider=\'PayMongo QRPH\' AND payments.payment_type=\'charge\'
           AND (payments.provider_transaction_id=:session_id OR bookings.reference=:booking_reference)
         ORDER BY payments.id DESC LIMIT 1 FOR UPDATE'
    );
    $paymentQuery->execute([
        'session_id' => $sessionId,
        'booking_reference' => $metadata['booking_reference'] ?? null,
    ]);
    $payment = $paymentQuery->fetch();
    if (!$payment || (
        $payment['provider_transaction_id'] !== null
        && $payment['provider_transaction_id'] !== $sessionId
    )) {
        throw new RuntimeException('No local payment matches this PayMongo checkout session.');
    }
    if (!in_array($payment['status'], ['pending', 'completed'], true)) {
        throw new RuntimeException('The local payment is not awaiting confirmation.');
    }
    if (
        (int) $intentAttributes['amount'] !== (int) round((float) $payment['amount'] * 100)
        || strtoupper((string) $intentAttributes['currency']) !== strtoupper((string) $payment['currency'])
    ) {
        throw new RuntimeException('PayMongo checkout amount does not match the booking payment.');
    }

    if ($payment['status'] !== 'completed') {
        $updatePayment = $database->prepare(
            'UPDATE payments SET provider_transaction_id=COALESCE(provider_transaction_id,:session_id),
                status=\'completed\',processed_at=NOW() WHERE id=:id'
        );
        $updatePayment->execute([
            'session_id' => $sessionId,
            'id' => (int) $payment['id'],
        ]);
        $confirmBooking = $database->prepare(
            'UPDATE bookings SET status=\'confirmed\' WHERE id=:id AND status=\'pending\''
        );
        $confirmBooking->execute(['id' => (int) $payment['booking_id']]);
    }
    $database->commit();
    http_response_code(200);
    exit('Payment verified.');
} catch (RuntimeException | PDOException $exception) {
    if (isset($database) && $database instanceof PDO && $database->inTransaction()) {
        $database->rollBack();
    }
    error_log('PayMongo webhook processing failed: ' . $exception->getMessage());
    http_response_code(500);
    exit('Payment could not be verified.');
}
