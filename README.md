# Batik Rubung Kuning

Backend toko batik berbasis Laravel 12. API pelanggan menyediakan katalog, keranjang, alamat, pesanan biasa dan custom, OTP email, serta pembayaran Midtrans. Panel web admin mengelola katalog, promo, pesanan, pelanggan, dan laporan. Ongkir dihitung melalui RajaOngkir.

## Kebutuhan

- PHP 8.2+ dengan ekstensi MySQL
- MySQL 8+ atau MariaDB yang mendukung indeks full-text
- Composer dan Node.js
- Akun Midtrans dan RajaOngkir untuk menguji pembayaran dan ongkir nyata

## Menjalankan lokal

1. Buat database MySQL kosong dan salin `.env.example` menjadi `.env`.
2. Isi `DB_*`, `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY`, dan `RAJAONGKIR_API_KEY`. Isi `MAIL_*` dengan layanan SMTP agar OTP dan pesan dari formulir kontak dapat dikirim; atur `CONTACT_EMAIL` sebagai alamat penerima pesan. `MAIL_MAILER=log` hanya menulis email ke log lokal.
3. Jalankan:

   ```bash
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan storage:link
   npm install
   npm run build
   php artisan serve
   ```

   Gunakan `npm run dev` saat mengembangkan antarmuka admin.

4. Untuk data contoh lokal, jalankan `php artisan db:seed`. Seeder katalog, voucher, dan promo dapat dijalankan ulang tanpa menduplikasi entri yang dibuatnya. Seeder pengguna contoh hanya aktif pada lingkungan `local` dan `testing`; jangan gunakan akun contoh untuk produksi.

Webhook Midtrans: `POST /api/payments/midtrans/webhook`. Atur URL publik endpoint ini di dashboard Midtrans. Proyek memverifikasi signature dan nominal sebelum menerima notifikasi. Gunakan kunci dan mode sandbox untuk pengembangan.

Admin mengubah status pesanan sesuai urutan pembayaran dan pengiriman. Pembatalan pesanan menunggu pembayaran dilakukan melalui alur pelanggan yang mengonfirmasi pembatalan Midtrans. Pesanan yang sudah dibayar memerlukan proses pengembalian dana terpisah sebelum dapat dianggap dibatalkan.

## Pengujian

```bash
php artisan test
npm run build
```

Tes otomatis memakai SQLite dalam memori. MySQL tetap database aplikasi sesuai `.env.example`; migrasi mengabaikan indeks full-text produk hanya saat memakai SQLite untuk tes.

Jangan jalankan `migrate:fresh` pada database berisi data penting. Untuk produksi, jalankan migrasi biasa setelah pencadangan database dan atur `APP_ENV=production`, `APP_DEBUG=false`, SMTP, serta kunci layanan yang sesuai.
