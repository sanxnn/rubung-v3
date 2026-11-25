# Arsitektur backend Batik Rubung Kuning

Aplikasi Laravel menyediakan API JSON untuk pelanggan, panel web admin, dan halaman profil perusahaan. Aplikasi Flutter ada di repositori [batik_app](https://github.com/sanxnn/batik_app) dan menggunakan [referensi API](api-reference.md) ini sebagai kontrak integrasi.

## Teknologi dan versi

| Komponen | Versi atau persyaratan | Kegunaan |
| --- | --- | --- |
| PHP | `^8.2` pada manifest, minimal 8.4.1 untuk lockfile saat ini | Runtime backend. Paket Symfony yang terkunci mensyaratkan PHP 8.4.1 atau lebih baru. |
| Laravel | 12.69.2 | Routing, validasi, Eloquent, Blade, sesi, mail, dan penyimpanan |
| Laravel Sanctum | 4.3.3 | Token Bearer API pelanggan |
| MySQL | MySQL 8+ direkomendasikan; README juga mencantumkan MariaDB dengan dukungan full-text | Database aplikasi |
| SQLite | Dalam memori pada suite tes | Database pengujian, dengan indeks full-text MySQL dilewati |
| Midtrans PHP SDK | 2.6.2 | Pembayaran Snap dan operasi transaksi |
| Laravel Excel | 4.0.3 | Ekspor laporan penjualan |
| PHPUnit | 11.5.56 | Tes unit dan fitur |
| Vite dan Tailwind CSS | Vite `^7.0.7`, Tailwind CSS `^4.0.0` | Build aset dan styling halaman Blade |

Versi paket PHP di atas diambil dari [composer.lock](../composer.lock). Rentang paket frontend mengikuti [package.json](../package.json). Nilai ini menggambarkan konfigurasi repositori, sedangkan versi runtime di deployment mengikuti server yang digunakan.

## Struktur aplikasi

| Lokasi | Tanggung jawab |
| --- | --- |
| [routes/api.php](../routes/api.php) | Katalog publik, akun pelanggan, rute privat Sanctum, dan webhook Midtrans |
| [routes/web.php](../routes/web.php) | Halaman profil, form kontak, autentikasi admin, dan operasi dashboard |
| [app/Http/Controllers](../app/Http/Controllers) | Validasi, otorisasi, dan koordinasi proses API maupun web |
| [app/Http/Resources](../app/Http/Resources) | Bentuk JSON untuk alamat, keranjang, pesanan, custom order, dan refund |
| [app/Models](../app/Models) dan [database/migrations](../database/migrations) | Relasi, aturan data, tabel, indeks, dan foreign key |
| [app/Services](../app/Services) dan [app/Exports](../app/Exports) | Integrasi layanan eksternal dan ekspor penjualan |
| [resources/views](../resources/views) | Blade untuk panel admin dan halaman publik |
| [tests](../tests) | Pengujian unit dan fitur |

Klien Flutter memakai Bearer token. Panel admin memakai sesi web dengan middleware autentikasi dan role, serta CSRF pada form. API yang mengakses pesanan, alamat, atau pengajuan pelanggan memeriksa kepemilikan data pada alur terkait.

## Database dan relasi

Konfigurasi aplikasi menggunakan `DB_CONNECTION=mysql` pada [contoh environment](../.env.example). Migrasi adalah sumber skema. Contoh environment memakai database untuk sesi, cache, dan antrean.

| Domain | Tabel utama dan relasi |
| --- | --- |
| Akun | `users` menyimpan pelanggan dan admin. `email_otps` menyimpan OTP verifikasi dan reset. `personal_access_tokens` menyimpan token Sanctum. |
| Alamat | `addresses` terkait ke pengguna; menyimpan penerima, alamat utama, nama dan kode wilayah, serta kode pos. |
| Produk | `categories` memiliki `subcategories` dan `products`. Produk memiliki `product_variants`. `product_bundle_items` menghubungkan varian paket dengan varian komponen. |
| Keranjang | `carts` memiliki satu baris per pengguna. `cart_items` unik untuk pasangan keranjang dan varian. |
| Promosi | `date_promos` menyimpan periode promo tanggal. `vouchers` menyimpan kode, aturan diskon, periode, dan pemakaian kuota. |
| Pesanan | `orders` menyimpan status, rincian total, salinan alamat, resi, dan pencatatan stok. `order_items` menyimpan rincian barang. `order_status_histories` mencatat status dan pelaku. |
| Pembayaran | `payments` terkait satu-ke-satu dengan pesanan, menyimpan status, nominal, metode, identitas transaksi, dan token Snap. |
| Custom order | `custom_orders` menyimpan permintaan dan penawaran. Setelah disetujui, pengajuan terhubung ke pesanan. Gambar terkait disimpan melalui `custom_order_attachments`. |
| Refund | `refund_requests` menyimpan pengajuan, bukti, nominal, metode, status, dan admin peninjau. Satu pesanan dapat memiliki riwayat beberapa pengajuan. Pengajuan aktif atau selesai membatasi pengajuan baru. |
| Infrastruktur | Migrasi bawaan menyediakan tabel sesi, cache, antrean, dan catatan job. |

Checkout mencatat pesanan dan pemakaian stok serta voucher dalam transaksi database. Aksi pesanan dan refund yang bersaing memakai penguncian baris pada bagian terkait untuk menjaga konsistensi transisi. Refund selesai tercatat terpisah dari pembayaran dan digunakan untuk mengurangi omzet bersih.

## Penyimpanan dan integrasi

| Layanan | Penggunaan dan konfigurasi |
| --- | --- |
| Disk publik | Gambar produk dan lampiran custom order. `storage:link` menyediakan jalur publik Laravel. |
| Disk lokal privat | Bukti refund berada di `storage/app/private` dan disajikan lewat rute berautentikasi. |
| Midtrans | Snap, webhook pembayaran, dan refund. Konfigurasi `MIDTRANS_*` menentukan kunci dan mode sandbox atau produksi. |
| RajaOngkir | Pencarian lokasi dan ongkir. Konfigurasi `RAJAONGKIR_*` menentukan kunci, asal toko, kurir, dan berat. |
| wilayah.id | Daftar dan validasi hierarki kode wilayah. Backend menyimpan hasil di cache. |
| Email | Konfigurasi `MAIL_*` untuk OTP dan pesan kontak. `CONTACT_EMAIL` menentukan penerima pesan kontak. |

Lihat [README proyek](../README.md#menjalankan-lokal) untuk pemasangan dan [panduan refund](refund.md) untuk pengujian Midtrans di sandbox.
