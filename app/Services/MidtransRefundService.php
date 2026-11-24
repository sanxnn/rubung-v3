<?php

namespace App\Services;

use App\Models\RefundRequest;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransRefundService
{
    private const SUPPORTED_TYPES = ['credit_card', 'gopay', 'shopeepay', 'dana', 'ovo', 'qris', 'kredivo', 'akulaku'];

    public function supports(string $paymentType): bool
    {
        return in_array(strtolower($paymentType), self::SUPPORTED_TYPES, true);
    }

    public function assertNoRefundRecorded(RefundRequest $refund): void
    {
        $serverKey = config('midtrans.server_key');
        if (! $serverKey) {
            throw new RuntimeException('Kunci server Midtrans belum tersedia.');
        }

        $baseUrl = config('midtrans.is_production')
            ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
        $orderId = rawurlencode($refund->order->order_number);
        $status = Http::withBasicAuth($serverKey, '')->acceptJson()->timeout(15)
            ->get($baseUrl.'/v2/'.$orderId.'/status');

        if (! $status->successful() || $status->json('status_code') !== '200'
            || $status->json('transaction_status') !== 'settlement') {
            throw new RuntimeException('Status Midtrans tidak membuktikan refund belum diproses. Periksa transaksi di Midtrans.');
        }
    }

    public function process(RefundRequest $refund): string
    {
        $serverKey = config('midtrans.server_key');
        if (! $serverKey) {
            throw new RuntimeException('Kunci server Midtrans belum tersedia.');
        }

        $orderId = rawurlencode($refund->order->order_number);
        $baseUrl = config('midtrans.is_production')
            ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
        $url = $baseUrl.'/v2/'.$orderId;
        $client = Http::withBasicAuth($serverKey, '')->acceptJson()->timeout(15);

        $status = $client->get($url.'/status');
        if (! $status->successful() || $status->json('status_code') !== '200') {
            throw new RuntimeException('Status transaksi Midtrans tidak dapat diverifikasi.');
        }

        $transactionStatus = $status->json('transaction_status');
        if ($transactionStatus === 'refund') {
            $refunds = $status->json('refunds', []);
            foreach ($refunds as $entry) {
                if (($entry['refund_key'] ?? null) === $refund->refund_key) {
                    return empty($entry['bank_confirmed_at']) ? 'processing' : 'completed';
                }
            }

            if ($status->json('refund_key') === $refund->refund_key) {
                return empty($status->json('bank_confirmed_at')) ? 'processing' : 'completed';
            }

            throw new RuntimeException('Transaksi sudah direfund di Midtrans; periksa transaksi secara manual.');
        }

        if ($transactionStatus !== 'settlement') {
            throw new RuntimeException('Transaksi Midtrans belum berstatus settlement.');
        }

        $paymentType = strtolower($refund->order->payment->payment_type ?? '');
        if (! $this->supports($paymentType)) {
            throw new RuntimeException('Metode pembayaran ini tidak mendukung refund otomatis Midtrans.');
        }

        try {
            $response = $client->post($url.'/refund', [
                'refund_key' => $refund->refund_key,
                'amount' => $refund->amount,
                'reason' => mb_substr($refund->reason, 0, 255),
            ]);
        } catch (ConnectionException $error) {
            throw new MidtransRefundUncertainException('Koneksi terputus saat mengirim refund. Periksa status Midtrans sebelum tindakan manual.', 0, $error);
        }

        if ($response->serverError() || $response->json('status_code') === '406') {
            throw new MidtransRefundUncertainException('Status refund Midtrans belum pasti. Periksa ulang menggunakan kunci refund yang sama.');
        }

        if ($response->clientError()) {
            throw new RuntimeException('Midtrans menolak refund: '.($response->json('status_message') ?: 'permintaan tidak dapat diproses.'));
        }

        if (! $response->successful() || $response->json('status_code') !== '200'
            || $response->json('transaction_status') !== 'refund'
            || $response->json('refund_key') !== $refund->refund_key) {
            throw new MidtransRefundUncertainException('Respons refund Midtrans tidak jelas. Periksa ulang menggunakan kunci refund yang sama.');
        }

        return 'processing';
    }
}
