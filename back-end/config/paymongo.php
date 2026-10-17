<?php
declare(strict_types=1);

function paymongoConfiguration(): array
{
    $secretKey = trim((string) getenv('PAYMONGO_SECRET_KEY'));
    $webhookSecret = trim((string) getenv('PAYMONGO_WEBHOOK_SECRET'));
    $baseUrl = rtrim(trim((string) getenv('APP_BASE_URL')), '/');
    $baseUrlParts = parse_url($baseUrl);

    if (
        $secretKey === ''
        || $webhookSecret === ''
        || !is_array($baseUrlParts)
        || !in_array($baseUrlParts['scheme'] ?? '', ['http', 'https'], true)
        || empty($baseUrlParts['host'])
        || isset($baseUrlParts['user'])
        || isset($baseUrlParts['pass'])
        || isset($baseUrlParts['query'])
        || isset($baseUrlParts['fragment'])
    ) {
        throw new RuntimeException('PayMongo QRPH is not configured.');
    }

    return [
        'secret_key' => $secretKey,
        'webhook_secret' => $webhookSecret,
        'base_url' => $baseUrl,
    ];
}

function paymongoRequest(string $method, string $path, ?array $payload = null, ?string $idempotencyKey = null): array
{
    $configuration = paymongoConfiguration();
    $headers = ['Accept: application/json', 'Content-Type: application/json'];
    if ($idempotencyKey !== null) {
        $headers[] = 'Idempotency-Key: ' . $idempotencyKey;
    }

    $curl = curl_init('https://api.paymongo.com/v1/' . ltrim($path, '/'));
    if ($curl === false) {
        throw new RuntimeException('Unable to initialize the PayMongo request.');
    }

    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_USERPWD => $configuration['secret_key'] . ':',
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 20,
    ]);
    if ($payload !== null) {
        try {
            $requestBody = json_encode($payload, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            curl_close($curl);
            throw new RuntimeException('The payment request could not be encoded.');
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestBody);
    }

    $responseBody = curl_exec($curl);
    $statusCode = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $curlError = curl_error($curl);
    curl_close($curl);

    if ($responseBody === false) {
        error_log('PayMongo API request failed: ' . $curlError);
        throw new RuntimeException('The payment provider could not be reached.');
    }

    try {
        $response = json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
    } catch (JsonException $exception) {
        error_log('PayMongo API returned invalid JSON (HTTP ' . $statusCode . ').');
        throw new RuntimeException('The payment provider returned an invalid response.');
    }

    if ($statusCode < 200 || $statusCode >= 300 || !is_array($response) || isset($response['errors'])) {
        error_log('PayMongo API request failed with HTTP ' . $statusCode . '.');
        throw new RuntimeException('The payment provider could not process this booking.');
    }

    return $response;
}

function paymongoCreateCheckoutSession(
    int $bookingId,
    string $bookingReference,
    string $description,
    int $amountInCentavos,
    string $currency
): array {
    $configuration = paymongoConfiguration();
    $confirmationUrl = $configuration['base_url']
        . '/front-end/pages/public/booking-confirmation.php?booking_id=' . $bookingId;
    $response = paymongoRequest('POST', 'checkout_sessions', [
        'data' => [
            'attributes' => [
                'description' => $description,
                'line_items' => [[
                    'currency' => strtoupper($currency),
                    'amount' => $amountInCentavos,
                    'name' => $description,
                    'quantity' => 1,
                ]],
                'payment_method_types' => ['qrph'],
                'send_email_receipt' => false,
                'show_description' => true,
                'show_line_items' => true,
                'success_url' => $confirmationUrl . '&payment=success',
                'cancel_url' => $confirmationUrl . '&payment=cancelled',
                'metadata' => [
                    'booking_id' => (string) $bookingId,
                    'booking_reference' => $bookingReference,
                ],
            ],
        ],
    ], 'booking-' . $bookingReference);

    $session = $response['data'] ?? null;
    $attributes = is_array($session) ? ($session['attributes'] ?? null) : null;
    $checkoutUrl = is_array($attributes) ? ($attributes['checkout_url'] ?? null) : null;
    $sessionId = is_array($session) ? ($session['id'] ?? null) : null;
    $urlParts = is_string($checkoutUrl) ? parse_url($checkoutUrl) : false;

    if (
        !is_string($sessionId) || $sessionId === ''
        || !is_array($urlParts)
        || ($urlParts['scheme'] ?? '') !== 'https'
        || ($urlParts['host'] ?? '') !== 'checkout.paymongo.com'
    ) {
        error_log('PayMongo created a checkout session without a valid checkout URL.');
        throw new RuntimeException('The payment provider returned an invalid checkout session.');
    }

    return [
        'id' => $sessionId,
        'checkout_url' => $checkoutUrl,
    ];
}

function paymongoVerifyWebhookSignature(string $payload, string $signatureHeader): bool
{
    $configuration = paymongoConfiguration();
    $parts = [];
    foreach (explode(',', $signatureHeader) as $segment) {
        $pair = explode('=', trim($segment), 2);
        if (count($pair) === 2) {
            $parts[$pair[0]] = $pair[1];
        }
    }

    $timestamp = $parts['t'] ?? '';
    $signature = str_starts_with($configuration['secret_key'], 'sk_live_')
        ? ($parts['li'] ?? '')
        : ($parts['te'] ?? '');
    if ($timestamp === '' || $signature === '') {
        return false;
    }

    $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $configuration['webhook_secret']);
    return hash_equals($expected, $signature);
}
