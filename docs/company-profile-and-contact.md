# Profil perusahaan dan kontak

Halaman publik `GET /` menampilkan profil Batik Rubung Kuning. Form kontak mengirim pesan melalui `POST /contact`. Form membutuhkan `name` (maksimal 100 karakter), `email` (alamat valid, maksimal 100 karakter), dan `message` (10–5.000 karakter). Backend membatasi pengiriman hingga tiga request per menit.

Alamat penerima mengikuti `CONTACT_EMAIL`. Konfigurasi `MAIL_*` menentukan pengiriman email. Pada lingkungan lokal dengan `MAIL_MAILER=log`, pesan tercatat di log. [Konfigurasi lokal](../README.md#menjalankan-lokal)

## Katalog publik

Beranda menampilkan hingga tiga produk kategori Batik, diurutkan menurut jumlah unit pada pesanan dengan pembayaran sukses. Pesanan batal dan pesanan dengan refund selesai tidak ikut dihitung. Produk tanpa penjualan dapat mengisi kartu tersisa tanpa label terjual. Halaman `/katalog` memuat semua kategori dengan sidebar pencarian nama, kategori, subkategori, dan rentang harga rupiah. Rentang memakai harga varian termurah yang ditampilkan pada kartu. Batas minimum dan maksimum bersifat opsional dan inklusif. Filter tetap aktif saat berpindah halaman hasil.

Atur `STOREFRONT_GOOGLE_PLAY_URL` dan `STOREFRONT_APP_STORE_URL` dengan URL listing resmi untuk mengaktifkan badge unduh. Jika kosong, badge menampilkan status tautan belum tersedia. `STOREFRONT_EMAIL` menampilkan alamat email publik secara opsional. Penerima formulir tetap diatur secara terpisah melalui `CONTACT_EMAIL`.


## Tema dan bahasa

Company profile dan katalog berbagi pilihan tema. Secara default keduanya mengikuti tema perangkat. Tombol mode gelap menyimpan pilihan di browser. Pilihan bahasa ID/EN disimpan pada sesi dan berlaku untuk teks antarmuka serta pesan formulir kontak. Nama, deskripsi, dan kategori produk menggunakan data yang dimasukkan di katalog. Halaman login, admin, dan API memakai pengaturan masing-masing.
