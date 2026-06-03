<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\WebhookEvent as WebhookEventModel;
use App\Payments\Exceptions\PaymentException;
use App\Payments\PaymentManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebhookController extends Controller
{
    public function __invoke(Request $request, PaymentManager $manager, string $gateway): JsonResponse
    {
        try {
            $driver = $manager->driver($gateway);
        } catch (PaymentException) {
            return response()->json(['error' => 'Unknown gateway'], 404);
        }

        try {
            $event = $driver->verifyWebhook($request);
        } catch (PaymentException $e) {
            Log::warning('Rejected '.$gateway.' webhook: '.$e->getMessage());

            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $record = WebhookEventModel::firstOrCreate(
            [
                'gateway' => $gateway,
                'event_id' => $event->id,
            ],
            [
                'type' => $event->type,
                'payload' => $event->payload,
            ],
        );

        if (! $record->wasRecentlyCreated) {
            return response()->json(['status' => 'duplicate']);
        }

        try {
            $driver->handleEvent($event);
            $record->forceFill(['processed_at' => now()])->save();
        } catch (Throwable $e) {
            Log::error('Failed to process '.$gateway.' webhook: '.$e->getMessage(), ['event_id' => $event->id]);

            return response()->json(['error' => 'Processing failed'], 500);
        }

        return response()->json(['status' => 'ok']);
    }
}
