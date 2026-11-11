# Roadmap dan kontrak API Flutter — Batik Rubung Kuning

Dokumen ini menjelaskan backend yang **sudah tersedia** untuk aplikasi Flutter pelanggan, contoh request/response, dan pekerjaan API yang masih perlu direncanakan. Sumber kontrak saat ini: `routes/api.php`, controller di `app/Http/Controllers/Api`, dan resource di `app/Http/Resources`. Contoh ID, tanggal, harga, dan token di bawah adalah data ilustrasi; field tambahan dari model Laravel dapat muncul pada endpoint yang mengembalikan model langsung.

## Gambaran backend

- Laravel 12, MySQL, autentikasi token Laravel Sanctum.
- Katalog produk, kategori/subkategori, dan paket bundling dapat dibaca tanpa login.
- Pelanggan yang login dapat mengelola profil, alamat, keranjang, pesanan reguler, pembayaran Midtrans, dan custom order.
- Ongkir dihitung backend melalui RajaOngkir ketika pesanan dibuat. Promo tanggal dan voucher dihitung backend saat checkout.
- Panel admin adalah aplikasi web terpisah pada `/dashboard`; endpoint admin tidak dimaksudkan untuk Flutter pelanggan.

## Aturan integrasi

| Hal | Nilai saat ini |
| --- | --- |
| Base URL production (aktif) | `https://batikrubungkuning.sanxnn.my.id/api` — gunakan ini untuk build Flutter production. |
| Base URL lokal | `http://10.0.2.2:8000/api` untuk Android emulator; `http://localhost:8000/api` untuk Flutter desktop/web di mesin yang sama. Perangkat fisik memakai IP LAN server. |
| Header JSON | `Accept: application/json` dan `Content-Type: application/json` untuk request berbadan JSON. |
| Endpoint privat | `Authorization: Bearer <token>` dari login atau registrasi. Simpan token di penyimpanan aman perangkat. |
| ID path | Angka database, misalnya `/products/12`; slug belum diterima sebagai route key. |
| Harga | Rupiah tanpa desimal; gunakan nilai dari server sebagai sumber harga akhir. |
| Gambar | `image` adalah path relatif, misalnya `products/batik.jpg`. Tampilkan melalui `<BASE_URL>/storage-image/products/batik.jpg` atau URL `/storage/...` setelah `php artisan storage:link`. Endpoint gambar mengembalikan file, bukan JSON. |
| Pagination | `page` mulai dari 1. Format pagination katalog produk berbeda dari resource collection; lihat contoh di bawah. |
| Error umum | `401` token tidak ada/tidak valid, `403` akses ditolak atau email belum diverifikasi, `404` ID tidak ditemukan/bukan milik pelanggan, `422` validasi atau aturan bisnis, `429` batas percobaan. |

Contoh URL lengkap production: `GET https://batikrubungkuning.sanxnn.my.id/api/products?category_id=1&page=1`. Semua path di tabel endpoint di bawah digabungkan dengan salah satu base URL di atas. URL pada contoh pagination memakai domain production.

Contoh error validasi Laravel (`422`):

```json
{
  "message": "The quantity field is required.",
  "errors": { "quantity": ["The quantity field is required."] }
}
```

Error aturan bisnis biasanya hanya memuat `message`, kadang disertai `available_stock` atau `current_quantity`. Tampilkan `message` dari server dan gunakan `errors` untuk field form. Semua contoh response berikut menampilkan field yang relevan untuk Flutter; objek model langsung dapat memiliki `created_at`, `updated_at`, dan field model lain.

## Alur layar Flutter

1. **Beranda/katalog:** panggil `GET /categories`, `GET /products`, dan `GET /bundles`. Gunakan ID kategori/subkategori untuk filter produk dan `page` untuk memuat halaman berikutnya.
2. **Detail:** panggil `GET /products/{id}` atau `GET /bundles/{id}`. Pengguna memilih `variant.id` sebelum menambah keranjang.
3. **Akun:** registrasi → verifikasi OTP email → login; atau login langsung untuk akun yang sudah diverifikasi. Simpan token Sanctum.
4. **Keranjang:** panggil `GET /cart`, tambah/ubah/hapus item. `cart.items[].id` adalah ID item keranjang, berbeda dari `variant.id`.
5. **Checkout:** pilih alamat dari `GET /addresses`, lalu `POST /orders` dengan `address_id` dan voucher opsional. Backend mengambil isi keranjang dan menghitung stok, diskon, ongkir, serta total.
6. **Pembayaran:** gunakan `data.payment.snap_token` dari checkout. Jika kosong atau ingin melanjutkan pembayaran, panggil `POST /payments/{orderId}/snap-token`. Setelah proses Midtrans, muat ulang `GET /orders/{id}`; status final ditetapkan webhook server, bukan callback Flutter.
7. **Riwayat:** `GET /orders` dan `GET /orders/{id}`. Pesanan custom juga muncul di riwayat pesanan setelah quotation disetujui.

## Ringkasan endpoint

`Publik` tidak memerlukan token. `Privat` memerlukan Bearer token. Semua path di bawah relatif terhadap `/api`.

| Metode dan path | Akses | Fungsi / body |
| --- | --- | --- |
| `GET /categories` | Publik | Semua kategori beserta subkategori. |
| `GET /categories/{id}` | Publik | Detail kategori. |
| `GET /products` | Publik | Query: `search`, `category_id`, `subcategory_id`, `page`. |
| `GET /products/{id}` | Publik | Detail produk reguler dan variannya. |
| `GET /bundles` | Publik | Query: `search`, `page`. |
| `GET /bundles/{id}` | Publik | Detail paket dan komponen. |
| `GET /storage-image/{path}` | Publik | File gambar produk; response biner. |
| `POST /auth/register` | Publik | `name`, `email`, `phone?`, `gender`, `birth_date`, `password`, `password_confirmation`. |
| `POST /auth/email/verify` | Publik | `email`, `otp`. |
| `POST /auth/email/resend` | Publik | `email`. |
| `POST /auth/login` | Publik | `email`, `password`. |
| `POST /auth/password/forgot` | Publik | `email`. |
| `POST /auth/password/verify-otp` | Publik | `email`, `otp`. |
| `POST /auth/password/reset` | Publik | `email`, `reset_token`, `password`, `password_confirmation`. |
| `GET /auth/me` | Privat | Profil pelanggan. |
| `PUT /auth/profile` | Privat | `name`, `email`, `phone?`, `gender`, `birth_date`. |
| `POST /auth/logout` | Privat | Cabut token saat ini. |
| `GET /addresses` | Privat | Daftar alamat. |
| `POST /addresses` | Privat | Buat alamat; field di bagian Alamat. |
| `GET /addresses/{id}` | Privat | Detail alamat milik pelanggan. |
| `PUT /addresses/{id}` | Privat | Ubah alamat; field sama dengan pembuatan. |
| `PATCH /addresses/{id}/primary` | Privat | Jadikan alamat utama; tanpa body. |
| `DELETE /addresses/{id}` | Privat | Hapus alamat. |
| `GET /cart` | Privat | Isi dan total keranjang. |
| `POST /cart/items` | Privat | `product_variant_id`, `quantity`. |
| `PUT /cart/items/{cartItemId}` | Privat | `quantity` baru. |
| `DELETE /cart/items/{cartItemId}` | Privat | Hapus satu item. |
| `DELETE /cart` | Privat | Kosongkan keranjang. |
| `GET /orders` | Privat | Riwayat pesanan, query `page`. |
| `POST /orders` | Privat | Checkout keranjang: `address_id`, `voucher_code?`, `notes?`. |
| `GET /orders/{id}` | Privat | Detail dan status pembayaran terbaru. |
| `POST /orders/{id}/cancel` | Privat | Batalkan pesanan `pending_payment`; tanpa body. |
| `POST /payments/{orderId}/snap-token` | Privat | Buat/ambil token pembayaran; tanpa body. |
| `GET /custom-orders` | Privat | Riwayat custom order, query `page`. |
| `POST /custom-orders` | Privat | `description` minimal 10 karakter. |
| `GET /custom-orders/{id}` | Privat | Detail custom order. |
| `POST /custom-orders/{id}/approve` | Privat | Setujui quotation: `address_id`, `notes?`. |
| `POST /custom-orders/{id}/cancel` | Privat | Batalkan saat `pending_review`/`quoted`; tanpa body. |

`POST /payments/midtrans/webhook` adalah callback server Midtrans, **bukan** endpoint untuk dipanggil Flutter.

## Katalog: kategori, produk, dan bundling

### Kategori

`GET /categories` mengembalikan array tanpa pagination. Gunakan `subcategories[].id` sebagai `subcategory_id` saat memfilter produk. Contoh:

```json
{
  "message": "Data kategori berhasil diambil.",
  "data": [
    {
      "id": 1,
      "name": "Batik",
      "slug": "batik",
      "description": "Koleksi batik",
      "products_count": 8,
      "subcategories": [
        { "id": 3, "category_id": 1, "name": "Batik Tulis", "slug": "batik-tulis" }
      ]
    }
  ]
}
```

`GET /categories/1` mengembalikan `data` berupa satu objek kategori, termasuk `subcategories` dan field model seperti `has_subcategories`, `created_at`, `updated_at` (tanpa `products_count` yang hanya dihitung pada list).

### Produk reguler dan filter

Contoh: `GET /products?search=batik&category_id=1&subcategory_id=3&page=1`. Semua filter opsional dan digabung. Pencarian saat ini memakai nama/deskripsi; belum ada filter harga, stok, sorting, atau endpoint subkategori tersendiri. Produk tanpa varian reguler tidak tampil.

```json
{
  "message": "Data produk berhasil diambil.",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 12,
        "category_id": 1,
        "subcategory_id": 3,
        "name": "Batik Tulis Melati",
        "slug": "batik-tulis-melati",
        "description": "Batik tulis motif melati",
        "image": "products/melati.jpg",
        "category": { "id": 1, "name": "Batik", "slug": "batik" },
        "subcategory": { "id": 3, "name": "Batik Tulis", "slug": "batik-tulis" },
        "variants": [
          { "id": 41, "product_id": 12, "name": "2 Meter", "sku": "BT-MEL-2M", "price": 250000, "stock": 5 }
        ]
      }
    ],
    "first_page_url": "https://batikrubungkuning.sanxnn.my.id/api/products?page=1",
    "from": 1,
    "last_page": 1,
    "last_page_url": "https://batikrubungkuning.sanxnn.my.id/api/products?page=1",
    "links": [],
    "next_page_url": null,
    "path": "https://batikrubungkuning.sanxnn.my.id/api/products",
    "per_page": 12,
    "prev_page_url": null,
    "to": 1,
    "total": 1
  }
}
```

`links` pada contoh disingkat; Laravel mengirim daftar tautan pagination. Flutter membaca item dari **`data.data`**, lalu `data.current_page` dan `data.last_page`. `GET /products/12` mengembalikan bentuk produk yang sama pada `data`, tanpa objek pagination:

```json
{
  "message": "Detail produk berhasil diambil.",
  "data": {
    "id": 12,
    "name": "Batik Tulis Melati",
    "slug": "batik-tulis-melati",
    "description": "Batik tulis motif melati",
    "image": "products/melati.jpg",
    "category": { "id": 1, "name": "Batik", "slug": "batik" },
    "subcategory": { "id": 3, "name": "Batik Tulis", "slug": "batik-tulis" },
    "variants": [
      { "id": 41, "product_id": 12, "name": "2 Meter", "sku": "BT-MEL-2M", "price": 250000, "stock": 5 }
    ]
  }
}
```

### Paket bundling

`GET /bundles?search=paket&page=1` memakai resource collection: item berada pada **`data`** (array), sedangkan pagination ada di `links` dan `meta`. `GET /bundles/{id}` memakai `data` satu objek dengan bentuk yang sama, serta `message: "Detail bundling berhasil diambil."`. `variants[].stock` adalah stok paket yang dihitung dari komponen, bukan kolom stok varian paket.

```json
{
  "data": [
    {
      "id": 22,
      "name": "Paket Batik Melati",
      "slug": "paket-batik-melati",
      "description": "Paket kain dan aksesori",
      "image": "products/paket-melati.jpg",
      "category": { "id": 4, "name": "Bundling", "slug": "bundling" },
      "variants": [
        {
          "id": 55,
          "name": "Paket A",
          "sku": "BND-MEL-A",
          "price": 300000,
          "stock": 3,
          "components": [
            {
              "variant_id": 41,
              "name": "2 Meter",
              "sku": "BT-MEL-2M",
              "quantity": 1,
              "stock": 5,
              "product": { "id": 12, "name": "Batik Tulis Melati", "slug": "batik-tulis-melati" }
            }
          ]
        }
      ]
    }
  ],
  "links": { "first": "https://batikrubungkuning.sanxnn.my.id/api/bundles?page=1", "last": "https://batikrubungkuning.sanxnn.my.id/api/bundles?page=1", "prev": null, "next": null },
  "meta": { "current_page": 1, "from": 1, "last_page": 1, "links": [], "path": "https://batikrubungkuning.sanxnn.my.id/api/bundles", "per_page": 12, "to": 1, "total": 1 },
  "message": "Data bundling berhasil diambil."
}
```

## Akun dan OTP

`POST /auth/register` memerlukan `gender` persis `Male` atau `Female`, `birth_date` tanggal sebelum hari ini, password minimal 8 karakter, dan konfirmasi yang sama. `phone` opsional.

```json
{
  "name": "Siti Aminah",
  "email": "siti@example.com",
  "phone": "081234567890",
  "gender": "Female",
  "birth_date": "1998-05-17",
  "password": "rahasia123",
  "password_confirmation": "rahasia123"
}
```

Response `201` registrasi:

```json
{
  "message": "Registrasi berhasil. Silakan verifikasi email menggunakan OTP yang dikirim ke email Anda.",
  "data": {
    "user": { "id": 7, "name": "Siti Aminah", "email": "siti@example.com", "role": "customer", "email_verified_at": null },
    "token": "7|contoh-token-sanctum",
    "email_verified": false
  }
}
```

Registrasi langsung mengembalikan token, tetapi login berikutnya ditolak (`403`) sampai email diverifikasi. OTP dikirim via email, berlaku 10 menit, dan maksimal 5 percobaan per kode. Alur request/response lainnya:

| Endpoint | Contoh body JSON | Contoh response JSON sukses (`200`) |
| --- | --- | --- |
| `POST /auth/email/verify` | `{"email":"siti@example.com","otp":"123456"}` | `{"message":"Email berhasil diverifikasi.","data":{"email_verified":true,"user":{"id":7,"email":"siti@example.com","email_verified_at":"2026-11-11T08:00:00.000000Z"}}}` |
| `POST /auth/email/resend` | `{"email":"siti@example.com"}` | `{"message":"OTP verifikasi baru telah dikirim ke email Anda."}` |
| `POST /auth/login` | `{"email":"siti@example.com","password":"rahasia123"}` | `{"message":"Login berhasil.","data":{"user":{"id":7,"name":"Siti Aminah","email":"siti@example.com","role":"customer"},"token":"8|contoh-token-sanctum","email_verified":true}}` |
| `POST /auth/password/forgot` | `{"email":"siti@example.com"}` | `{"message":"Jika email terdaftar, OTP reset password akan dikirim ke email tersebut."}` |
| `POST /auth/password/verify-otp` | `{"email":"siti@example.com","otp":"123456"}` | `{"message":"OTP berhasil diverifikasi. Silakan buat password baru.","data":{"email":"siti@example.com","reset_token":"0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef","expires_in":600}}` |
| `POST /auth/password/reset` | `{"email":"siti@example.com","reset_token":"<64 karakter dari verify-otp>","password":"baru12345","password_confirmation":"baru12345"}` | `{"message":"Password berhasil diubah. Silakan login kembali."}` |
| `GET /auth/me` | Tidak ada | `{"message":"Data customer berhasil diambil.","data":{"id":7,"name":"Siti Aminah","email":"siti@example.com","role":"customer"}}` |
| `POST /auth/logout` | Tidak ada | `{"message":"Logout berhasil."}` |

`PUT /auth/profile` mewajibkan semua field identitas berikut walaupun hanya satu yang berubah:

```json
{
  "name": "Siti Aminah",
  "email": "siti.baru@example.com",
  "phone": "081234567890",
  "gender": "Female",
  "birth_date": "1998-05-17"
}
```

Response:

```json
{
  "message": "Profile berhasil diperbarui. OTP verifikasi telah dikirim ke email baru.",
  "data": {
    "user": { "id": 7, "name": "Siti Aminah", "email": "siti.baru@example.com", "email_verified_at": null },
    "email_verified": false
  }
}
```

Pesan berbeda jika email tidak berubah. Saat email berubah, tampilkan layar verifikasi OTP lagi. Reset password mencabut semua token login lama.

## Alamat

`POST /addresses` dan `PUT /addresses/{id}` memakai field wajib yang sama. `district` dan `is_primary` opsional; alamat pertama otomatis menjadi utama.

```json
{
  "label": "Rumah",
  "recipient_name": "Siti Aminah",
  "phone": "081234567890",
  "province": "Jawa Timur",
  "city": "Jember",
  "district": "Sumbersari",
  "postal_code": "68121",
  "detail": "Jl. Mawar No. 10",
  "is_primary": true
}
```

`postal_code` harus lima digit. Contoh `POST /addresses` (response resource pada `data`; endpoint `GET`, `PUT`, dan `PATCH .../primary` memakai objek yang sama):

```json
{
  "data": {
    "id": 3,
    "label": "Rumah",
    "recipient_name": "Siti Aminah",
    "phone": "081234567890",
    "province": "Jawa Timur",
    "city": "Jember",
    "district": "Sumbersari",
    "postal_code": "68121",
    "detail": "Jl. Mawar No. 10",
    "full_address": "Jl. Mawar No. 10, Kec. Sumbersari, Jember, Jawa Timur, 68121",
    "is_primary": true,
    "created_at": "2026-11-11T08:00:00.000000Z",
    "updated_at": "2026-11-11T08:00:00.000000Z"
  },
  "message": "Alamat berhasil ditambahkan."
}
```

`GET /addresses` membungkus alamat-alamat di `data: [...]` dan `message: "Data alamat berhasil diambil."`. `DELETE /addresses/{id}` mengembalikan `{"message":"Alamat berhasil dihapus."}`. Menghapus alamat utama akan memilih alamat terbaru yang tersisa sebagai alamat utama.

## Keranjang

Tambahkan varian produk **atau varian bundling** memakai `POST /cart/items`:

```json
{ "product_variant_id": 41, "quantity": 2 }
```

`PUT /cart/items/9` memakai `{ "quantity": 3 }`. `GET /cart`, `POST`, `PUT`, `DELETE /cart/items/9`, dan `DELETE /cart` mengembalikan bentuk `data` yang sama; `message` mengikuti aksi.

```json
{
  "data": {
    "id": 2,
    "items": [
      {
        "id": 9,
        "quantity": 2,
        "variant": {
          "id": 41,
          "name": "2 Meter",
          "sku": "BT-MEL-2M",
          "price": 250000,
          "is_bundle": false,
          "stock": 5,
          "product": { "id": 12, "name": "Batik Tulis Melati", "slug": "batik-tulis-melati", "image": "products/melati.jpg" }
        },
        "subtotal": 500000
      }
    ],
    "total_items": 2,
    "total_amount": 500000
  },
  "message": "Keranjang berhasil diambil."
}
```

Jumlah tambah minimal 1; menambah varian yang sama menambah jumlah lama, sedangkan `PUT` menetapkan jumlah akhir. Respons stok berlebih: `{"message":"Jumlah produk melebihi stok yang tersedia.","available_stock":5}` dengan status `422`. Stok 0 saat ini dapat mewakili produk pre-order; keputusan akhir dilakukan saat checkout. Total keranjang belum termasuk diskon dan ongkir.

## Checkout, pembayaran, dan riwayat

`POST /orders` mengambil semua item keranjang pelanggan; Flutter **tidak** mengirim daftar item, harga, total, ongkir, atau `courier`. Alamat harus milik pelanggan. Contoh request:

```json
{ "address_id": 3, "voucher_code": "HEMAT10", "notes": "Tolong bungkus rapi" }
```

`voucher_code` dan `notes` opsional. Voucher dan promo tanggal diterapkan saat checkout, kemudian stok dicek/dicadangkan, ongkir RajaOngkir dihitung, dan Midtrans dicoba. Contoh response `201`:

```json
{
  "message": "Pesanan berhasil dibuat.",
  "data": {
    "order": {
      "id": 25,
      "order_number": "BRK-20261111080000-ABCDE",
      "status": "pending_payment",
      "is_custom_order": false,
      "pricing": {
        "subtotal": 500000,
        "discount_date_cantik": 0,
        "discount_voucher": 50000,
        "total_discount": 50000,
        "shipping_cost": 20000,
        "final_amount": 470000
      },
      "promo": { "date_promo": null, "voucher": { "id": 2, "code": "HEMAT10" } },
      "shipping": { "courier": "jnt", "resi_number": null, "cost": 20000, "weight": 2000 },
      "address": { "recipient_name": "Siti Aminah", "phone": "081234567890", "province": "Jawa Timur", "city": "Jember", "postal_code": "68121", "detail": "Jl. Mawar No. 10" },
      "notes": "Tolong bungkus rapi",
      "items": [
        { "id": 30, "product_variant_id": 41, "product_name": "Batik Tulis Melati", "variant_name": "2 Meter", "quantity": 2, "unit_price": 250000, "subtotal": 500000 }
      ],
      "payment": { "payment_type": "pending", "payment_status": "pending", "amount": 470000, "snap_token": "contoh-snap-token", "paid_at": null },
      "created_at": "2026-11-11T08:00:00.000000Z",
      "updated_at": "2026-11-11T08:00:00.000000Z"
    },
    "payment": { "payment_status": "pending", "amount": 470000, "snap_token": "contoh-snap-token" }
  }
}
```

`data.payment.snap_token` bisa `null` jika pembuatan pembayaran gagal setelah order tersimpan. Simpan `order.id` dan panggil `POST /payments/25/snap-token` tanpa body untuk mencoba lagi. Response saat token baru berhasil dibuat:

```json
{
  "message": "Pembayaran berhasil dibuat.",
  "data": { "order_number": "BRK-20261111080000-ABCDE", "amount": 470000, "snap_token": "contoh-snap-token" }
}
```

Jika token pending sudah ada, endpoint yang sama mengembalikan `{"message":"Pembayaran sudah dibuat.","data":{"payment_status":"pending","amount":470000,"snap_token":"contoh-snap-token"}}`. Perhatikan dua bentuk response sukses ini. Flutter menyerahkan Snap token ke integrasi pembayaran, lalu memuat ulang detail order untuk mengetahui status dari backend.

`GET /orders` mengembalikan resource collection berhalaman: `data: [<OrderResource>, ...]`, `links`, dan `meta`; `GET /orders/25` mengembalikan `{"data": <OrderResource>}`. Detail memuat `items`, `payment`, `promo`, dan `custom_order` (jika ada). Objek `address` adalah snapshot pada saat checkout sehingga tidak berubah ketika alamat akun diedit. Contoh ringkas riwayat:

```json
{
  "data": [
    { "id": 25, "order_number": "BRK-20261111080000-ABCDE", "status": "paid", "is_custom_order": false, "pricing": { "subtotal": 500000, "discount_date_cantik": 0, "discount_voucher": 50000, "total_discount": 50000, "shipping_cost": 20000, "final_amount": 470000 }, "items": [], "payment": { "payment_type": "bank_transfer", "payment_status": "success", "amount": 470000, "snap_token": "contoh-snap-token", "paid_at": "2026-11-11T08:10:00.000000Z" } }
  ],
  "links": { "first": "https://batikrubungkuning.sanxnn.my.id/api/orders?page=1", "last": "https://batikrubungkuning.sanxnn.my.id/api/orders?page=1", "prev": null, "next": null },
  "meta": { "current_page": 1, "from": 1, "last_page": 1, "links": [], "path": "https://batikrubungkuning.sanxnn.my.id/api/orders", "per_page": 10, "to": 1, "total": 1 }
}
```

Objek order pada response riwayat tetap mempunyai field lain seperti `promo`, `shipping`, `address`, `notes`, `custom_order`, `created_at`, dan `updated_at`; contoh ringkas di atas menonjolkan field untuk daftar. Status order yang digunakan: `pending_payment` → `paid` → `processing` → `packing` → `shipped` → `completed`; `cancelled` untuk pembatalan/gagal/expired. Status payment: `pending`, `success`, `failed`, atau `expire`.

`POST /orders/25/cancel` hanya untuk `pending_payment` dan tanpa body. Jika transaksi Midtrans pending, backend mencoba membatalkannya dahulu. Contoh sukses:

```json
{ "message": "Pesanan berhasil dibatalkan.", "data": { "id": 25, "order_number": "BRK-20261111080000-ABCDE", "status": "cancelled", "is_custom_order": false, "pricing": { "subtotal": 500000, "discount_date_cantik": 0, "discount_voucher": 50000, "total_discount": 50000, "shipping_cost": 20000, "final_amount": 470000 } } }
```

`data` pada contoh pembatalan disingkat; server mengirim `OrderResource` lengkap. Order yang sudah dibayar tidak bisa dibatalkan melalui endpoint ini. Pengembalian dana belum tersedia melalui API pelanggan. Saat ini `shipping.weight` pada `OrderResource` selalu `2000`, walaupun berat untuk perhitungan ongkir dapat diubah melalui konfigurasi; jangan gunakan field itu sebagai bukti berat aktual bila konfigurasi diubah.

## Custom order

Custom order adalah permintaan produk sesuai deskripsi pelanggan. Admin meninjau dan memberi quotation sebelum pelanggan memilih alamat dan membuat order pembayaran. Saat ini request pelanggan hanya menerima **teks deskripsi**; belum ada endpoint upload lampiran meski resource dapat menampilkan `attachments` yang sudah ada.

`POST /custom-orders`:

```json
{ "description": "Saya ingin batik tulis motif bunga warna biru untuk acara keluarga." }
```

Response `201` (juga bentuk detail `GET /custom-orders/{id}`):

```json
{
  "data": {
    "id": 4,
    "reference_number": "CO-20261111080000-ABCDE",
    "description": "Saya ingin batik tulis motif bunga warna biru untuk acara keluarga.",
    "status": "pending_review",
    "estimated_price": null,
    "estimated_time": null,
    "admin_notes": null,
    "attachments": [],
    "order": null,
    "created_at": "2026-11-11T08:00:00.000000Z",
    "updated_at": "2026-11-11T08:00:00.000000Z"
  }
}
```

`GET /custom-orders` memakai `data: [<CustomOrderResource>, ...]`, `links`, dan `meta` dengan `per_page: 10`. Setelah admin memberi penawaran, `status` menjadi `quoted` dan `estimated_price`, `estimated_time`, `admin_notes` terisi sesuai keputusan admin.

`POST /custom-orders/4/approve` hanya saat `quoted`:

```json
{ "address_id": 3, "notes": "Warna biru tua" }
```

Response `201`:

```json
{
  "message": "Custom Order berhasil disetujui. Silakan lakukan pembayaran penuh.",
  "data": { "id": 26, "order_number": "BRK-20261111080000-FGHIJ", "status": "pending_payment", "is_custom_order": true, "pricing": { "subtotal": 400000, "discount_date_cantik": 0, "discount_voucher": 0, "total_discount": 0, "shipping_cost": 20000, "final_amount": 420000 }, "custom_order": { "id": 4, "reference_number": "CO-20261111080000-ABCDE", "status": "approved", "description": "Saya ingin batik tulis motif bunga warna biru untuk acara keluarga.", "estimated_price": 400000, "estimated_time": "14 hari" } }
}
```

`data` di atas adalah `OrderResource` lengkap yang disingkat untuk contoh. Sesudah approve, panggil `POST /payments/26/snap-token` untuk pembayaran. `POST /custom-orders/4/cancel` tanpa body hanya berlaku sebelum approve (`pending_review` atau `quoted`) dan mengembalikan `{"message":"Custom Order berhasil dibatalkan.","data":<CustomOrderResource>}`. Status custom order yang relevan untuk Flutter: `pending_review`, `quoted`, `approved`, `rejected`, `cancelled`.

## Roadmap integrasi Flutter dan gap backend

| Tahap | Prioritas | Pekerjaan |
| --- | --- | --- |
| 1. Dasar aplikasi | Tinggi | Konfigurasi base URL per environment, client HTTP dengan header JSON/Bearer, penyimpanan token aman, parsing error `401/403/422/429`, dan model pagination katalog vs resource collection. |
| 2. Katalog | Tinggi | Beranda, kategori/subkategori, filter `category_id`/`subcategory_id`, pencarian, detail produk/bundling, pilihan varian, dan pemuatan gambar. |
| 3. Akun dan transaksi | Tinggi | OTP email, login/profil, alamat, keranjang, checkout, Snap Midtrans, polling/refresh detail order, riwayat, pembatalan pending. Uji bersama MySQL, RajaOngkir, SMTP, dan Midtrans sandbox. |
| 4. Custom order | Sedang | Permintaan teks, daftar/detail, quotation, approve, pembayaran, dan pembatalan. |
| 5. Perluasan API | Sedang | Jika dibutuhkan desain Flutter: endpoint pratinjau ongkir/promo/voucher sebelum checkout, daftar voucher publik, upload lampiran custom order, dan filter/sort produk menurut harga/stok. Selaraskan pula `shipping.weight` pada response order dengan berat konfigurasi. Endpoint perluasan tersebut **belum tersedia**; jangan panggil sebagai endpoint aktif. |
| 6. Operasional | Sedang | Tetapkan kontrak API versi stabil, tambah pengujian respons JSON dan perangkat nyata terhadap URL production, serta pastikan webhook Midtrans mengarah ke URL production. |

Dokumen ini mencatat perilaku backend saat ini, bukan janji endpoint baru. Jika kontrak API berubah, perbarui contoh response dan model Flutter bersama perubahan controller/resource terkait.
