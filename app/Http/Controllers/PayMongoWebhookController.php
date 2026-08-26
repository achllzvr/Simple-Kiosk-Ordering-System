<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\PayMongoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayMongoWebhookController extends Controller
{
    public function __construct(
        private PayMongoService $payMongoService,
        private OrderService $orderService,
    ) {}

    public function __invoke(Request $request)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('Paymongo-Signature');
        $valid = $this->payMongoService->verifyWebhookSignature($rawBody, $signature);

        if (! $valid) {
            Log::warning('PayMongo webhook signature invalid');

            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        $event = $request->all();
        $eventId = $event['data']['id'] ?? $event['id'] ?? null;
        $eventType = $event['data']['attributes']['type'] ?? $event['type'] ?? null;

        if ($this->orderService->webhookEventExists($eventId)) {
            return response()->json(['status' => 'success', 'message' => 'Duplicate event ignored']);
        }

        $paid = $this->payMongoService->parsePaidWebhook($event);
        if (! $paid) {
            $this->orderService->logWebhookEvent([
                'event_id' => $eventId,
                'event_type' => $eventType,
                'signature_valid' => true,
                'fulfilled' => false,
                'payload_summary' => mb_substr($rawBody, 0, 1000),
                'fulfill_message' => 'Ignored non-paid or unparsable event',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Event ignored']);
        }

        $result = $this->orderService->fulfillOnlinePayment(
            $paid['order_id'],
            $paid['payment_id'] ?: null,
            $paid['session_id'],
            $paid['payment_id'] ?: null,
        );

        $this->orderService->logWebhookEvent([
            'event_id' => $eventId,
            'event_type' => $eventType,
            'order_id' => (int) $paid['order_id'],
            'signature_valid' => true,
            'fulfilled' => (bool) ($result['success'] ?? false),
            'payload_summary' => mb_substr($rawBody, 0, 1000),
            'fulfill_message' => $result['message'] ?? $result['error'] ?? null,
        ]);

        return response()->json(['status' => 'success', 'message' => $result['message'] ?? 'ok']);
    }
}
