# Profil perusahaan dan kontak

Halaman publik `GET /` menampilkan profil Batik Rubung Kuning. Form kontak mengirim pesan melalui `POST /contact`. Form membutuhkan `name` (maksimal 100 karakter), `email` (alamat valid, maksimal 100 karakter), dan `message` (10–5.000 karakter). Backend membatasi pengiriman hingga tiga request per menit.

Alamat penerima mengikuti `CONTACT_EMAIL`. Konfigurasi `MAIL_*` menentukan pengiriman email. Pada lingkungan lokal dengan `MAIL_MAILER=log`, pesan tercatat di log. [Konfigurasi lokal](../README.md#menjalankan-lokal)
