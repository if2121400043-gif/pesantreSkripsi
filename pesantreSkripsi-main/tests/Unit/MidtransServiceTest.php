<?php

namespace Tests\Unit;

use App\Services\MidtransService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MidtransServiceTest extends TestCase
{
    public function test_it_fetches_transaction_status_from_midtrans(): void
    {
        config()->set('midtrans.server_key', 'Mid-server-test');
        config()->set('midtrans.is_production', false);

        Http::fake([
            'https://api.sandbox.midtrans.com/v2/SIMPP-10-123/status' => Http::response([
                'transaction_status' => 'settlement',
                'transaction_id' => 'txn_123',
                'order_id' => 'SIMPP-10-123',
                'gross_amount' => '100000',
                'payment_type' => 'bank_transfer',
                'fraud_status' => 'accept',
            ], 200),
        ]);

        $service = new MidtransService();
        $payload = $service->getTransactionStatus('SIMPP-10-123');

        $this->assertSame('settlement', $payload['transaction_status']);
        $this->assertSame('txn_123', $payload['transaction_id']);
    }
}
