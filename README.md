# Batik Rubung Kuning

Backend toko batik berbasis Laravel 12. API pelanggan menyediakan katalog, keranjang, alamat, pesanan biasa dan custom, OTP email, pembayaran Midtrans, serta pengajuan refund. Panel web admin mengelola katalog, promo, pesanan, pelanggan, refund, dan laporan. Ongkir dihitung melalui RajaOngkir.

Baca [kontrak API dan alur sistem](docs/api-reference.md) untuk endpoint, contoh body request, dan proses admin. Lihat [indeks dokumentasi](docs/README.md) untuk arsitektur dan panduan setiap fitur.

## Kebutuhan

- PHP 8.4.1+ untuk dependensi yang terkunci, dengan ekstensi MySQL
- MySQL 8+ atau MariaDB yang mendukung indeks full-text
- Composer dan Node.js yang kompatibel dengan Vite 7 (20.19+ pada seri 20, atau 22.12+)
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

Admin mengubah status pesanan sesuai urutan pembayaran dan pengiriman. Pembatalan pesanan menunggu pembayaran memerlukan konfirmasi Midtrans jika token Snap atau ID transaksi sudah tercatat. Untuk pesanan yang sudah dibayar, pelanggan dapat mengajukan refund melalui alur terpisah. Refund tercatat pada pengajuan dan tidak mengubah status pesanan menjadi dibatalkan.

## Pengujian

```bash
php artisan test
npm run build
```

Tes otomatis memakai SQLite dalam memori secara default. Untuk verifikasi perubahan checkout dan laporan, jalankan juga suite di MySQL dengan strict mode dan `ONLY_FULL_GROUP_BY`. Gunakan database MySQL khusus tes karena suite dapat membuat ulang tabel. Lihat [cara menjalankan tes MySQL](docs/backend-architecture.md#pengujian-database).

Jangan jalankan `migrate:fresh` pada database berisi data penting. Untuk produksi, jalankan migrasi biasa setelah pencadangan database dan atur `APP_ENV=production`, `APP_DEBUG=false`, SMTP, serta kunci layanan yang sesuai.
