# Pengujian API Batik Rubung Kuning

Koleksi ini menyediakan 52 request yang mencakup 51 rute API, dengan contoh tambahan untuk membuat custom order menggunakan lampiran. Path, aturan validasi, dan bentuk respons mengikuti [referensi API](../api-reference.md). Gunakan akun khusus tes. Checkout, pembayaran, refund, perubahan akun, dan penghapusan data sebaiknya dijalankan di lokal dengan layanan sandbox sesuai kebutuhan.

## File impor

| File | Penggunaan |
| --- | --- |
| [Koleksi API](rubung-v3.postman_collection.json) | Impor ke Postman atau HTTPie Desktop/Web. Format Postman Collection v2.1 diterima oleh keduanya. |
| [Environment Local](rubung-v3-local.postman_environment.json) | `base_url=http://localhost:8000/api`. Gunakan untuk alur uji lengkap. |
| [Environment Production](rubung-v3-production.postman_environment.json) | `base_url=https://batikrubungkuning.sanxnn.my.id/api`. Gunakan akun yang berwenang dan utamakan request baca. |
| [Gambar dummy](fixtures/bukti-uji.png) | Pilih ulang pada field file saat menguji refund dan lampiran custom order. |

`base_url` sudah mencakup `/api` dan tidak diakhiri garis miring. Ubah nilainya jika Laravel berjalan di host atau port lain. Callback Midtrans contoh memakai `local_base_url` tersendiri yang tetap menunjuk ke localhost pada kedua environment.

## Postman

1. Klik **Import**, lalu pilih koleksi JSON dan kedua file environment.
2. Pilih **Batik Rubung Kuning — Local** pada pemilih environment. Untuk localhost, gunakan aplikasi desktop atau Postman Web dengan Desktop Agent.
3. Buka request yang ingin diuji. Ganti variabel dummy dengan data dari respons server dan klik **Send**.
4. Pada request multipart, buka **Body → form-data** dan pilih ulang gambar pada field bertipe **File**. Biarkan klien menentukan header multipart beserta boundary.

## HTTPie Desktop atau Web

1. Buka **Library → + → Import**, lalu impor koleksi dan kedua file environment Postman. Pilih space tujuan yang sama.
2. Pilih environment **Local** atau **Production** dan periksa nilai `base_url`. HTTPie dapat mengganti nama variabel saat impor untuk menghindari benturan dengan variabel di space.
3. Jalankan request. Untuk localhost gunakan HTTPie Desktop, atau klien yang dapat menjangkau server lokal.
4. HTTPie mengimpor field form, tetapi tidak membawa file unggahan dari Postman. Pada request multipart, pilih tipe **File** dan pasang ulang `evidence` atau `attachments[]` jika field file hilang. Pertahankan nama field dan gunakan gambar dummy. [Kompatibilitas impor HTTPie](https://httpie.io/docs/desktop#import-compatibility)

## Urutan pengujian

1. **Siapkan server.** Ikuti [instalasi lokal](../../README.md#menjalankan-lokal). Untuk katalog contoh, jalankan seeder hanya di database lokal. Untuk checkout lokal, siapkan RajaOngkir dan Midtrans sandbox. Untuk OTP, siapkan SMTP atau baca OTP dari log lokal jika `MAIL_MAILER=log`.
2. **Akun.** Ganti `email` dengan alamat tes yang dapat diakses. Jalankan **Registrasi**, isi `otp` dari pesan verifikasi, lalu **Verifikasi email** dan **Login**. Salin `data.token` ke `access_token` pada environment yang aktif. Untuk simulasi reset password, jalankan **Minta OTP reset** dan **Verifikasi OTP reset**, salin `data.reset_token`, lalu **Reset password** dan login ulang dengan password baru. Nilai `000000` hanya contoh format.
3. **Katalog dan alamat.** Ambil ID kategori, subkategori, produk, paket, dan varian dari respons. Gunakan ID varian sebagai `variant_id`. Cocokkan `province_code`, `city_code`, `district_code`, dan `village_code` lewat request wilayah berurutan. Setelah **Buat alamat**, simpan `data.id` sebagai `address_id`.
4. **Keranjang dan checkout.** Tambah item lalu isi `cart_item_id` dari `data.items[].id`. Gunakan **Validasi voucher** jika voucher aktif dan subtotal cukup. Kode `BATIKHEMAT` tersedia melalui seeder, dengan minimum Rp100.000 dan tanggal berlaku yang mengikuti waktu seeding. Hapus field `voucher_code` dari body checkout jika menguji tanpa voucher. Simpan `data.order.id` sebagai `order_id`. Laravel menerima ID berupa string numerik pada body JSON contoh.
5. **Pembayaran dan pesanan.** Gunakan token Snap bila pesanan masih `pending_payment`. Muat ulang **Detail pesanan** untuk melihat status terbaru. Uji pembatalan dengan pesanan pending terpisah. Uji **Konfirmasi penerimaan** pada pesanan `shipped` dengan pembayaran `success`. Admin harus mengubah status pengiriman terlebih dahulu.
6. **Refund.** Gunakan pesanan dengan pembayaran `success`, kirim alasan dan gambar, lalu simpan `data.id` sebagai `refund_id`. Ambil gambar bukti lewat request berautentikasi. Uji pembatalan refund saat status `pending`. Peninjauan admin dilakukan lewat [panel refund](../refund.md).
7. **Custom order.** Simpan `data.id` sebagai `custom_order_id`. Tambah lampiran ketika masih `pending_review`. Admin perlu memberi penawaran hingga `quoted` sebelum **Setujui penawaran**. Respons persetujuan memuat ID pesanan pada `data.id`, yang dipakai untuk Snap.
8. **Callback lokal.** Request webhook berisi nomor pesanan dan signature dummy. Untuk nomor dummy yang tidak ada, backend dapat mengembalikan `200` dengan pesan `Order tidak ditemukan.` Itu bukan konfirmasi pembayaran. Untuk skenario positif, gunakan notifikasi Midtrans sandbox yang sah atau suite tes backend.

## Membaca hasil

- `401`: periksa `access_token`, environment aktif, dan apakah token masih berlaku.
- `404`: cocokkan ID dengan data server dan akun yang sedang digunakan.
- `409` saat pembatalan: muat ulang detail pesanan dan periksa status sebelum mencoba lagi.
- `422`: periksa `message` dan `errors`, termasuk status pesanan, stok dan kelayakan voucher.
- `429`: tunggu sesuai pembatasan endpoint OTP atau login.

Simpan token dan OTP hanya di environment lokal klien. File impor berisi placeholder dan data dummy. Setiap langkah dijalankan terpisah karena pembatalan, konfirmasi, refund, reset password, dan penghapusan memerlukan kondisi awal yang berbeda.
