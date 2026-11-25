# Operasional refund

Gunakan [kontrak refund pelanggan](api-reference.md#refund-pelanggan) dan [alur panel admin](api-reference.md#refund-pada-panel-admin) untuk endpoint, field, dan transisi status.

## Alur pelanggan dan admin

1. Pelanggan memilih pesanan yang memenuhi syarat, lalu mengirim alasan dan satu foto bukti. Backend membuat pengajuan `pending` berdasarkan nominal pembayaran.
2. Admin memeriksa bukti dan memilih penolakan, refund manual, atau refund Midtrans. Pelanggan dapat membatalkan pengajuan selama masih `pending`.
3. Pada jalur manual, admin mencatat referensi setelah melakukan transfer. Pada jalur Midtrans, backend menunggu konfirmasi dari API status sebelum mencatat `completed`.
4. Pelanggan melihat keputusan di detail pengajuan. Refund `completed` mengurangi omzet bersih di [laporan penjualan](sales-reports.md).

## Pembayaran yang masuk setelah pembatalan

Jika Midtrans mengonfirmasi pembayaran setelah pesanan dibatalkan, pesanan tetap `cancelled` dan pembayaran tercatat `success`. Pelanggan dapat mengajukan refund melalui endpoint yang sama. Admin melihat penanda di detail pesanan dan meninjau pengajuan melalui menu Refund. Status pembayaran yang belum sukses tetap tidak memenuhi syarat. Jalur ini menghindari pengaktifan kembali pesanan yang cadangan stoknya sudah dilepas.

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
