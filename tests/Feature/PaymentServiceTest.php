<?php

namespace Tests\Feature;

use App\Models\Orang;
use App\Models\Pembayaran;
use App\Models\PesertaDidik;
use App\Models\Tagihan;
use App\Services\MidtransService;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    public function test_it_includes_email_in_customer_details_for_snap_request(): void
    {
        $orang = new Orang([
            'nama_lengkap' => 'Budi Wali',
            'email' => 'wali@example.com',
        ]);

        $pesertaDidik = new PesertaDidik();
        $pesertaDidik->setRelation('orang', $orang);

        $tagihan = new Tagihan();
        $tagihan->setRelation('pesertaDidik', $pesertaDidik);

        $service = new MidtransService();
        $customerDetails = $service->buildCustomerDetails($tagihan);

        $this->assertSame('wali@example.com', $customerDetails['email']);
        $this->assertSame('Budi Wali', $customerDetails['first_name']);
    }

    public function test_it_refreshes_tagihan_status_when_payments_cover_total(): void
    {
        $tagihan = new Tagihan([
            'total' => 100000,
            'status' => 'BELUM_BAYAR',
        ]);

        $tagihan->setRelation('pembayaran', collect([
            new Pembayaran(['jumlah' => 100000]),
        ]));

        $tagihan->refreshPaymentStatus();

        $this->assertSame('LUNAS', $tagihan->status);
    }
}
