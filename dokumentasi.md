# Dokumentasi API backend Batik Rubung Kuning

Panduan integrasi untuk aplikasi pelanggan dan alur operasional admin, berdasarkan backend `main` pada commit `245586d`. Rute didefinisikan di [routes/api.php](routes/api.php) dan [routes/web.php](routes/web.php). Contoh respons katalog, akun, alamat, dan checkout yang lebih lengkap tersedia di [roadmap.md](roadmap.md).

## Mengirim request

- Base URL produksi yang tercantum di proyek adalah `https://batikrubungkuning.sanxnn.my.id/api`. Untuk Android emulator, gunakan `http://10.0.2.2:8000/api` jika Laravel berjalan pada port 8000 di komputer lokal.
- Semua path pada bagian API berikut relatif terhadap base URL tersebut. Pada contoh, `{id}` adalah ID numerik entitas yang bersangkutan. ID pesanan berbeda dari nomor pesanan dan ID pengajuan refund.
- Kirim `Accept: application/json`. Endpoint privat juga membutuhkan `Authorization: Bearer <token>`. Gunakan `Content-Type: application/json` untuk body JSON dan biarkan HTTP client membentuk boundary untuk multipart.
- `?` setelah field berarti opsional. GET dan DELETE di bawah tanpa body. Nominal uang menggunakan rupiah.
- Tangani `401` untuk autentikasi, `403` untuk akses ditolak seperti login sebelum verifikasi email, `404` untuk data yang tidak ditemukan atau bukan milik pelanggan, `422` untuk validasi atau aturan bisnis, dan `429` untuk pembatasan percobaan. Baca `message` dan, jika tersedia, `errors.<field>`. Endpoint file mengembalikan data biner saat sukses.

## Katalog dan akun

Mulai katalog dari `GET /categories`, `GET /products`, dan `GET /bundles`. Gunakan detail produk atau bundling untuk memilih ID varian sebelum menambahkan item ke keranjang.

| Endpoint publik | Query atau parameter |
| --- | --- |
| `GET /categories`, `GET /categories/{id}` | Daftar kategori dengan subkategori, atau detail kategori |
| `GET /products` | `search`, `category_id`, `subcategory_id`, `page` opsional |
| `GET /products/{id}` | ID produk reguler |
| `GET /bundles`, `GET /bundles/{id}` | Daftar dengan `search` dan `page` opsional, atau detail bundling |
| `GET /storage-image/{path}` | Path relatif gambar publik dari respons API |

Untuk akun baru, registrasi, verifikasi OTP email, lalu login. Gunakan `data.token` dari login untuk request privat. Registrasi juga mengembalikan token, tetapi login berikutnya membutuhkan email terverifikasi. Pada reset password, tukarkan OTP dengan `data.reset_token`, pakai token tersebut untuk reset, lalu login kembali.

| Endpoint | Akses | Body JSON |
| --- | --- | --- |
| `POST /auth/register` | Publik | `name`, `email`, `phone?`, `gender`, `birth_date`, `password`, `password_confirmation` |
| `POST /auth/email/verify` | Publik | `email`, `otp` |
| `POST /auth/email/resend` | Publik | `email` |
| `POST /auth/login` | Publik | `email`, `password` |
| `POST /auth/password/forgot` | Publik | `email` |
| `POST /auth/password/verify-otp` | Publik | `email`, `otp` |
| `POST /auth/password/reset` | Publik | `email`, `reset_token`, `password`, `password_confirmation` |
| `GET /auth/me` | Privat | Tanpa body |
| `PUT /auth/profile` | Privat | `name`, `email`, `phone?`, `gender`, `birth_date` |
| `POST /auth/logout` | Privat | Tanpa body |

`gender` memakai `Male` atau `Female`. Gunakan tanggal lahir sebelum hari ini dan password minimal delapan karakter saat registrasi atau reset. Pada perubahan email profil, ikuti alur verifikasi lagi.

## Alamat dan keranjang

Ambil kode wilayah dari rute publik `GET /regions/provinces`, `GET /regions/regencies/{provinceCode}`, `GET /regions/districts/{cityCode}`, lalu `GET /regions/villages/{districtCode}`. Pertahankan kode sebagai string, termasuk titik. Endpoint alamat dan keranjang berikut semuanya privat.

| Endpoint | Body |
| --- | --- |
| `GET /addresses`, `GET /addresses/{id}`, `DELETE /addresses/{id}` | Tanpa body |
| `POST /addresses`, `PUT /addresses/{id}` | `label`, `recipient_name`, `phone`, `postal_code` lima digit, `detail`, `is_primary?`, dan data wilayah |
| `PATCH /addresses/{id}/primary` | Tanpa body |
| `GET /cart`, `DELETE /cart` | Tanpa body |
| `POST /cart/items` | `{"product_variant_id": 41, "quantity": 2}` |
| `PUT /cart/items/{cartItemId}` | `{"quantity": 3}` |
| `DELETE /cart/items/{cartItemId}` | Tanpa body |

Kirim keempat field wilayah jika memakai kode, yaitu `province_code`, `city_code`, `district_code`, dan `village_code`. Backend memvalidasi hierarki dan mengisi nama wilayah. Payload lama menerima `province`, `city`, dan `district?`. Jumlah item minimal 1. POST menambah ke kuantitas varian yang sama, sedangkan PUT mengganti kuantitas akhir. Pakai `items[].id` dari keranjang sebagai `cartItemId`.

## Voucher, checkout, dan pembayaran

1. Panggil `POST /vouchers/validate` untuk mempratinjau voucher pada keranjang aktif. Body JSON:

   ```json
   {"code": "HEMAT10"}
   ```

   `code` wajib, berupa string maksimal 50 karakter. Server menghitung subtotal dari harga varian dan kuantitas keranjang, lalu memeriksa periode, status aktif, kuota, dan minimal belanja. Respons `200` memuat `data.code`, `data.subtotal`, `data.discount`, dan `data.subtotal_after_voucher`. Kode tidak ditemukan menghasilkan `404`. Keranjang kosong dan voucher yang tidak memenuhi syarat menghasilkan `422`. Pratinjau tidak memakai kuota. `data.final_amount` pada endpoint ini sama dengan `data.subtotal_after_voucher`. Keduanya belum memasukkan promo tanggal dan ongkir.
2. Panggil `POST /orders` dengan alamat milik pelanggan. `voucher_code` dan `notes` opsional. Body JSON:

   ```json
   {"address_id": 3, "voucher_code": "HEMAT10", "notes": "Tolong bungkus rapi"}
   ```

   Server memakai isi keranjang, memvalidasi stok dan voucher kembali, menghitung promo serta ongkir RajaOngkir, lalu membuat pesanan. Ambil total bayar dari `data.order.pricing.final_amount` dan token Snap dari `data.payment.snap_token` pada respons checkout.
3. Jika token Snap belum tersedia atau pelanggan melanjutkan pembayaran, panggil `POST /payments/{orderId}/snap-token` tanpa body. Jalankan pembayaran lewat Snap, kemudian muat ulang `GET /orders/{orderId}`. Callback dari aplikasi hanya memicu refresh. Status pembayaran ditetapkan di backend melalui webhook Midtrans.
4. `GET /orders?page=1` mengembalikan daftar berhalaman. Detail ada di `GET /orders/{id}`. Jika pelanggan membatalkan ketika `pending_payment`, panggil `POST /orders/{id}/cancel` tanpa body. Backend memeriksa status pembayaran dan menangani pembatalan Midtrans bila diperlukan. Pesanan yang sudah dibayar mengikuti alur refund.

Semua endpoint pelanggan dalam bagian ini privat. Webhook `POST /payments/midtrans/webhook` menerima notifikasi JSON dari Midtrans. Field yang diperiksa mencakup `order_id`, `transaction_status`, `status_code`, `gross_amount`, dan `signature_key`. Integrasikan webhook melalui dashboard Midtrans. Aplikasi Flutter membaca status dari API detail pesanan.

## Pengiriman dan konfirmasi penerimaan

Setelah pembayaran berhasil, admin memproses pesanan melalui `paid → processing → packing → shipped`. Admin mengisi resi ketika mengubah status ke `shipped`. Untuk tombol **Pesanan Diterima**, tampilkan aksi ketika status `shipped` dan `payment.payment_status == "success"`, lalu minta konfirmasi pelanggan.

Kirim `POST /orders/{id}/complete` dengan Bearer token dan **body kosong**. Respons `200` berisi `message` dan `data.status: "completed"`. Mengulang aksi pada pesanan yang telah selesai dan pembayarannya berhasil tetap sukses tanpa menambah riwayat baru. Pesanan milik akun lain menghasilkan `404`, dan status yang tidak memenuhi syarat menghasilkan `422`.

Muat ulang `GET /orders/{id}` setelah sukses untuk memperoleh detail termasuk ringkasan refund. Respons aksi complete tidak memuat ringkasan itu. Admin juga bisa menyelesaikan pesanan yang sudah dikirim. Riwayat mencatat siapa yang mengubah status, sehingga `completed` sendiri tidak membuktikan konfirmasi dari pelanggan. Refund `pending` tidak menghalangi penyelesaian pesanan pada implementasi saat ini.

## Refund pelanggan

Untuk pembayaran berstatus `success`, pemilik pesanan dapat mengajukan refund penuh saat pesanan berstatus `paid`, `processing`, `packing`, `shipped`, atau `completed`. Nominal diambil dari pembayaran oleh server. Pelanggan dapat mengajukan ulang setelah `rejected` atau `cancelled`. Selama masih ada pengajuan aktif atau selesai, server menolak pengajuan baru.

| Endpoint privat | Body atau query | Hasil sukses |
| --- | --- | --- |
| `POST /orders/{orderId}/refund` | Multipart `reason` (20–2.000 karakter) dan `evidence` (satu JPEG/PNG/WebP, maksimal 5 MB) | `201`, `data.status: "pending"` |
| `GET /refunds` | Query `page?` | Daftar berhalaman, 10 per halaman |
| `GET /refunds/{refundId}` | Tanpa body | Detail termasuk nominal, status, dan keputusan admin |
| `GET /refunds/{refundId}/evidence` | Tanpa body | File privat. Kirim token juga saat mengambil gambar. |
| `POST /refunds/{refundId}/cancel` | Body kosong | Status `cancelled` dari `pending`. Pengulangan pada status `cancelled` tetap sukses. |

Contoh request pada server lokal:

```bash
curl -X POST 'http://localhost:8000/api/orders/25/refund' \
  -H "Authorization: Bearer ${API_TOKEN}" \
  -H 'Accept: application/json' \
  -F 'reason=Produk rusak ketika diterima dan perlu pengembalian dana.' \
  -F 'evidence=@bukti.jpg'
```

Setelah sukses, tampilkan status pengajuan dan muat ulang detail pesanan. Daftar dan detail pesanan memuat ringkasan refund terbaru sebagai `refund: {id, status, amount}` atau `refund: null`. Detail refund juga memuat `order_id`, `order_number`, `reason`, `evidence_url`, `refund_method`, `admin_notes`, `manual_reference`, dan waktu pemrosesan. Lihat [resource refund](app/Http/Resources/RefundRequestResource.php) untuk daftar field.

## Refund pada panel admin

Admin membuka `/dashboard/refunds`, memeriksa bukti, lalu menyetujui atau menolak permintaan. Aksi berikut memakai sesi admin dan CSRF dari form web. Path berikut relatif terhadap `/dashboard/refunds/{refundId}`.

| Aksi POST | Field form | Aturan |
| --- | --- | --- |
| `/approve` | `method` wajib (`manual` atau `midtrans`), `admin_notes?` maksimal 2.000 karakter | Persetujuan dari `pending` |
| `/reject` | `admin_notes` wajib, 10–2.000 karakter | Penolakan dari `pending` |
| `/complete` | `manual_reference` wajib, maksimal 100 karakter | Selesaikan refund manual setelah transfer dilakukan |
| `/retry` | Tanpa field bisnis | Cek atau ulangi refund Midtrans dari `approved` atau `processing` |
| `/manual` | Tanpa field bisnis | Ubah jalur Midtrans yang masih `approved` setelah pemeriksaan status provider |

Jalur manual berjalan dari `pending → approved → completed`. Jalur Midtrans memerlukan metode yang didukung dan transaksi `settlement`. Permintaan yang diterima provider masuk `processing`. Backend menetapkan `completed` setelah API status Midtrans menunjukkan konfirmasi bank untuk kunci dan nominal yang sesuai. Status hasil pengiriman yang belum pasti tetap `processing` dan memblokir pengalihan ke manual. Rekonsiliasi berjalan melalui webhook atau retry admin. Refund selesai mengurangi omzet bersih pada dashboard dan laporan. Riwayat pesanan, pembayaran, stok, dan pemakaian voucher tetap tercatat. [Detail operasional dan pengujian refund](docs/refund.md)

## Custom order

Pelanggan mengirim deskripsi dan lampiran opsional. Admin meninjau dan memberi penawaran (`quoted`) atau menolak. Pelanggan yang menyetujui penawaran memilih alamat, lalu backend membuat pesanan reguler berstatus `pending_payment` yang terhubung ke custom order. Gunakan ID pesanan hasil approve untuk endpoint Snap.

| Endpoint privat | Body atau query |
| --- | --- |
| `GET /custom-orders`, `GET /custom-orders/{id}` | Tanpa body. Query `page?` untuk daftar. |
| `POST /custom-orders` | JSON `{"description": "Batik tulis biru untuk acara keluarga"}` atau multipart `description` dan `attachments[]?` |
| `POST /custom-orders/{id}/attachments` | Multipart `attachments[]`, selama `pending_review` |
| `POST /custom-orders/{id}/approve` | JSON `{"address_id": 3, "notes": "Warna biru tua"}`. `notes` opsional. Hanya saat `quoted`. |
| `POST /custom-orders/{id}/cancel` | Body kosong. Hanya saat `pending_review` atau `quoted`. |

Deskripsi minimal 10 karakter. Batas lampiran lima gambar per custom order, masing-masing JPEG/PNG/WebP maksimal 2 MB. Setelah approve, pantau pesanan terkait melalui API pesanan dan ikuti alur pembayaran serta pengiriman yang sama.
