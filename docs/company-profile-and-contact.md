# Profil perusahaan dan kontak

Halaman publik `GET /` menampilkan profil Batik Rubung Kuning. Form kontak mengirim pesan melalui `POST /contact`. Form membutuhkan `name` (maksimal 100 karakter), `email` (alamat valid, maksimal 100 karakter), dan `message` (10–5.000 karakter). Backend membatasi pengiriman hingga tiga request per menit.

Alamat penerima mengikuti `CONTACT_EMAIL`. Konfigurasi `MAIL_*` menentukan pengiriman email. Pada lingkungan lokal dengan `MAIL_MAILER=log`, pesan tercatat di log. [Konfigurasi lokal](../README.md#menjalankan-lokal)

## Katalog publik

Beranda menampilkan hingga tiga produk kategori Batik, diurutkan menurut jumlah unit pada pesanan dengan pembayaran sukses. Pesanan batal dan pesanan dengan refund selesai tidak ikut dihitung. Produk tanpa penjualan dapat mengisi kartu tersisa tanpa label terjual. Halaman `/katalog` memuat semua kategori dengan pencarian nama, filter kategori, dan pagination.

Atur `STOREFRONT_GOOGLE_PLAY_URL` dan `STOREFRONT_APP_STORE_URL` dengan URL listing resmi untuk mengaktifkan badge unduh. Jika kosong, badge menampilkan status tautan belum tersedia. `STOREFRONT_EMAIL` menampilkan alamat email publik secara opsional. Penerima formulir tetap diatur secara terpisah melalui `CONTACT_EMAIL`.
