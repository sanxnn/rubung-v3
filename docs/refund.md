# Refund pesanan

Satu pengajuan mencakup refund penuh untuk satu pesanan. Customer dapat mengajukan saat pembayaran berhasil dan pesanan berstatus `paid`, `processing`, `packing`, `shipped`, atau `completed`. Alasan minimal 20 karakter dan satu foto bukti JPEG/PNG/WebP maksimal 5 MB wajib dilampirkan. Foto disimpan pada disk `local` privat dan hanya dapat dibuka oleh pemilik melalui API atau admin melalui website.

## API customer

Semua endpoint memakai `auth:sanctum`.

- `POST /api/orders/{order}/refund`: multipart `reason` dan `evidence`; menghasilkan status `pending`.
- `GET /api/refunds`: daftar pengajuan customer.
- `GET /api/refunds/{refundRequest}`: detail dan keputusan admin.
- `POST /api/refunds/{refundRequest}/cancel`: tanpa body, hanya oleh pemilik saat `pending`. Mengembalikan `data.status: "cancelled"`. Pengulangan pada pengajuan yang dibatalkan mengembalikan status yang sama.
- `GET /api/refunds/{refundRequest}/evidence`: foto bukti privat.
- `GET /api/orders/{order}` dan daftar pesanan memuat ringkasan pengajuan terakhir pada field `refund`.

Pengajuan kedua ditolak selama ada refund aktif atau selesai. Setelah penolakan atau pembatalan, customer boleh mengajukan ulang dengan bukti baru.

## Website admin

Menu **Refund** menampilkan daftar, filter status, detail, dan bukti. Admin dapat menolak dengan alasan, menyetujui jalur manual atau Midtrans, mencatat referensi transfer manual, dan memeriksa/ mengulang status Midtrans. Status yang dipakai: `pending`, `approved`, `processing`, `completed`, `rejected`, `cancelled`.

Jalur manual baru selesai setelah admin mencatat referensi transfer. Jalur Midtrans memakai `refund_key` tetap agar permintaan ulang tidak menciptakan refund baru. Respons sukses API berarti permintaan diterima; status `completed` baru dicatat setelah API status Midtrans mengonfirmasi `bank_confirmed_at` untuk kunci dan nominal refund yang sesuai. Webhook refund memicu pemeriksaan tersebut. Jika respons pengiriman tidak pasti, status tetap `processing` dan pengalihan manual diblokir.

Refund Midtrans hanya dipilih untuk metode yang didukung API dan transaksi berstatus `settlement`. Kemampuan refund juga harus aktif di akun Midtrans. Untuk metode lain, pilih jalur manual. Pesanan, pembayaran, stok, dan pemakaian voucher tetap menyimpan riwayat aslinya; omzet bersih pada dashboard dan laporan mengurangi refund yang telah `completed`.

## Pengujian terpisah

Gunakan SQLite baru di luar database production:

```bash
touch /tmp/rubung_refund_test.sqlite
APP_ENV=testing DB_CONNECTION=sqlite DB_DATABASE=/tmp/rubung_refund_test.sqlite DB_URL= php artisan migrate --force
./vendor/bin/phpunit tests/Feature/RefundFlowTest.php
```

Tes PHP memakai SQLite di memori sesuai `phpunit.xml` dan HTTP Midtrans palsu; tidak mengirim transaksi nyata. Pengujian manual Midtrans harus memakai akun sandbox, kunci sandbox di `.env`, dan `MIDTRANS_IS_PRODUCTION=false`. Aplikasi Flutter harus menunjuk ke backend pengujian melalui `API_BASE_URL` saat menguji alur dari perangkat.
