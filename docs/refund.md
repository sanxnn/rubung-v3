# Operasional refund

Gunakan [kontrak refund pelanggan](../roadmap.md#refund-pelanggan) dan [alur panel admin](../roadmap.md#refund-pada-panel-admin) untuk endpoint, field, dan transisi status.

## Konfigurasi Midtrans

Aktifkan refund pada akun Midtrans untuk metode pembayaran yang akan digunakan. Untuk metode yang tidak tersedia, gunakan jalur manual. Saat respons pengiriman tidak pasti, periksa ulang melalui aksi retry di panel admin. Backend memakai kunci refund yang sama pada percobaan ulang.

## Pengujian terpisah

Gunakan SQLite baru di luar database production:

```bash
touch /tmp/rubung_refund_test.sqlite
APP_ENV=testing DB_CONNECTION=sqlite DB_DATABASE=/tmp/rubung_refund_test.sqlite DB_URL= php artisan migrate --force
./vendor/bin/phpunit tests/Feature/RefundFlowTest.php
```

Tes PHP memakai SQLite di memori sesuai `phpunit.xml` dan HTTP Midtrans palsu; tidak mengirim transaksi nyata. Pengujian manual Midtrans harus memakai akun sandbox, kunci sandbox di `.env`, dan `MIDTRANS_IS_PRODUCTION=false`. Aplikasi Flutter harus menunjuk ke backend pengujian melalui `API_BASE_URL` saat menguji alur dari perangkat.
