# Batik Rubung Kuning - Dokumentasi Proyek

Proyek ini adalah aplikasi e-commerce batik berbasis Laravel 12 yang mencakup katalog produk, keranjang, checkout, pembayaran Midtrans, custom order, dan manajemen refund untuk panel admin.

---

## Struktur Database

### Tabel Utama

| Tabel | Keterangan |
|---|---|
| `users` | Akun pelanggan & admin (role: admin/customer). Field: name, email, phone, gender, birth_date, password, role. |
| `addresses` | Alamat pelanggan. Field: user_id, recipient_name, phone, province, city, district, village, postal_code, detail, is_primary. Digunakan kode wilayah (province_code, city_code, district_code, village_code) atau nama. |
| `categories` | Kategori produk. Field: name, slug, description, has_subcategories. Relasi ke products. |
| `subcategories` | Subkategori produk. Field: name, slug, category_id (cascade). |
| `products` | Produk. Field: category_id (wajib), subcategory_id (opsional untuk batik), name, slug, description, image. |
| `product_variants` | Varian produk (size/package). Field: product_id, name, sku, price, stock. Relasi ke product. |
| `product_bundle_items` | Item komponen paket. Field: parent_variant_id, component_variant_id, quantity (unique constraint). |
| `carts` | Keranjang belanja. Field: user_id (unique, cascade). |
| `cart_items` | Item keranjang. Field: cart_id, product_variant_id, quantity. Unique constraint per cart+variant. |
| `vouchers` | Voucher diskon. Field: code, discount_type (percentage/fixed), discount_value, min_purchase, max_discount, usage_limit, used_count, start_date, end_date, is_active. |
| `date_promos | Promo berdasarkan tanggal. Field: name, discount_type, discount_value, start_datetime, end_datetime, is_active. |
| `orders` | Pesanan. Field: user_id, address_id, order_number (unique), subtotal, discount_date_cantik, date_promo_id, discount_voucher, voucher_id, shipping_cost, final_amount, status (pending_payment/paid/processing/packing/shipped/completed/cancelled), shipping_courier, resi_number, snapshot_* fields, notes, stock_reserved_at, stock_released_at. |
| `order_items` | Item pesanan. Field: order_id, product_variant_id (nullable untuk custom order), product_name, variant_name, quantity, unit_price, subtotal. |
| `order_status_histories` | Riwayat status pesanan. Field: order_id, old_status, new_status, changed_by (user), notes, created_at. |
| `custom_orders | Permintaan produk custom. Field: user_id, reference_number (unique), description, estimated_price, estimated_time, status (pending_review/quoted/approved/rejected/cancelled), admin_notes, order_id (jika sudah jadi order). |
| `custom_order_attachments | Lampiran custom order. Field: custom_order_id, file_path, uploaded_by, created_at. |
| `payments | Riwayat pembayaran Midtrans. Field: order_id (unique), midtrans_transaction_id, payment_type, payment_status (pending/success/failed/expire), amount, paid_at, snap_token. |
| `refund_requests | Pengajuan refund. Field: order_id, user_id, reviewed_by (nullable), reason, evidence_path, amount, status (pending/approved/processing/completed/rejected), refund_method (midtrans/manual), refund_key, manual_reference, admin_notes, reviewed_at, completed_at. |
| `email_otps | OTP verifikasi. Field: user_id, email, otp_hash, purpose (email_verification/password_reset), attempts, expires_at, used_at. |

### Relasi Utama

- User → many Addresses → one primary
- User → many Orders → one Payment
- User → many CustomOrders
- Category → many Products → many Variants
- Order → many OrderItems → one ProductVariant
- Order → one RefundRequest (jika ada)
- CustomOrder → one Order (jika disetujui)
- RefundRequest → one Order → one Payment

---

## API Endpoints (Public + Private)

### Publik (tidak perlu token)

| Metode | Path | Fungsi |
|---|---|---|
| GET /categories | Semua kategori + subkategori |
| GET /categories/{id} | Detail kategori |
| GET /products | Katalog produk (query: search, category_id, subcategory_id, page) |
| GET /products/{id} | Detail produk + varian |
| GET /bundles | Paket bundling (query: search, page) |
| GET /bundles/{id} | Detail paket + komponen |
| GET /storage-image/{path} | File gambar (response biner) |
| POST /auth/register | Registrasi pelanggan (name, email, phone?, gender, birth_date, password) |
| POST /auth/email/verify | Verifikasi OTP email |
| POST /auth/email/resend | Kirim ulang OTP |
| POST /auth/login | Login (email, password) |
| POST /auth/password/forgot | Lupa password |
| POST /auth/password/verify-otp | Verifikasi OTP reset |
| POST /auth/password/reset | Set password baru |
| GET /auth/me | Profil pelanggan (privat) |
| PUT /auth/profile | Update profil (wajib semua field) |
| POST /auth/logout | Cabut token |
| GET /regions/provinces | Daftar provinsi dari wilayah.id |
| GET /regions/regencies/{provinceCode} | Kabupaten/kota |
| GET /regions/districts/{cityCode} | Kecamatan |
| GET /regions/villages/{districtCode} | Desa/kelurahan |

### Privat (butuh Bearer token dari login/registrasi)

| Metode | Path | Fungsi |
|---|---|---|
| GET /addresses | Daftar alamat pelanggan |
| POST /addresses | Buat alamat (field: label, recipient_name, phone, province?, city?, district?, province_code?, city_code?, district_code?, village_code?, postal_code, detail, is_primary?) |
| GET /addresses/{id} | Detail alamat |
| PUT /addresses/{id} | Ubah alamat |
| PATCH /addresses/{id}/primary | Jadikan alamat utama |
| DELETE /addresses/{id} | Hapus alamat |
| GET /cart | Isi keranjang + total |
| POST /cart/items | Tambah item keranjang (product_variant_id, quantity) |
| PUT /cart/items/{cartItemId} | Ubah quantity |
| DELETE /cart/items/{cartItemId} | Hapus item |
| DELETE /cart | Kosongkan keranjang |
| GET /orders | Riwayat pesanan (query: page) |
| POST /orders | Checkout keranjang (address_id, voucher_code?, notes?) |
| GET /orders/{id} | Detail pesanan lengkap |
| POST /orders/{id}/cancel | Batalkan pes pending_payment |
| POST /payments/{orderId}/snap-token | Buat/ambil token pembayaran Midtrans |
| GET /custom-orders | Riwayat custom order (query: page) |
| POST /custom-orders | Buat custom order (description, attachments[]) |
| GET /custom-orders/{id} | Detail custom order |
| POST /custom-orders/{id}/attachments | Tambah lampiran (hanya status pending_review, max 5) |
| POST /custom-orders/{id}/approve | Setujui quotation (address_id, notes?) |
| POST /custom-orders/{id}/cancel | Batal (pending_review/quoted) |

### Callback Server (bukan endpoint Flutter)

- POST /payments/midtrans/webhook - Notifikasi Midtrans (signature verification, update status pembayaran dan refund)

---

## Models & Relasi

### User

- `isAdmin()` → role === 'admin'
- `isCustomer()` → role === 'customer'
- `addresses()` → hasMany Address
- `primaryAddress()` → hasOne Address (is_primary)
- `cart()` → hasOne Cart
- `orders()` → hasMany Order
- `customOrders()` → hasMany CustomOrder

### Category

- `subcategories()` → hasMany Subcategory
- `products()` → hasMany Product

### Product

- `category()` → BelongsTo Category
- `subcategory()` → BelongsTo Subcategory
- `variants()` → hasMany ProductVariant
- `getLowestStockAttribute()` → min stock dari varian

### ProductVariant

- `product()` → BelongsTo Product
- `bundleComponents()` → hasMany ProductBundleItem (parent_variant_id)
- `bundleAsComponent()` → hasMany ProductBundleItem (component_variant_id)
- `getIsBundleAttribute()` → apakah punya komponen bundle
- `getAvailableBundleStockAttribute()` → stok tersedia dari semua komponen

### Order

- `user()` → BelongsTo User
- `address()` → BelongsTo Address
- `datePromo()` → BelongsTo DatePromo
- `voucher()` → BelongsTo Voucher
- `items()` → hasMany OrderItem
- `statusHistories()` → hasMany OrderStatusHistory
- `payment()` → hasOne Payment
- `refundRequests()` → hasMany RefundRequest
- `customOrder()` → hasOne CustomOrder
- `isCustomOrder()` → apakah punya custom order

### RefundRequest

- `order()` → BelongsTo Order
- `user()` → BelongsTo User
- `reviewer()` → BelongsTo User (reviewed_by)

### CustomOrder

- `user()` → BelongsTo User
- `order()` → BelongsTo Order
- `attachments()` → hasMany CustomOrderAttachment
- `isPending()`, `isQuoted()`, `isApproved()`, `isRejected()`, `isCancelled()`

---

## Services

### WilayahService

- `list(string $level, ?string $parentCode)` → ambil data wilayah dari wilayah.id API (provinces/regencies/districts/villages)
- `resolve(array $codes)` → resolve kode wilayah ke nama (province, city, district, village). Butuh province_code, city_code, district_code, village_code.

### RajaOngkirService

- `getCost(Address $address)` → hitung ongkir dari origin toko ke alamat customer menggunakan RajaOngkir API.
- `findOrigin(string $key)` → cari origin toko (konfig: services.rajongkir.origin_search, default: "Sumbersari,Jember")
- `findDestination(?string $postalCode, ?string $district, ?string $city, string $key)` → cari destination customer (prioritas: postal_code → district+city → city)

### MidtransRefundService

- Digunakan di admin refund flow. Metode: `supports()`, `process()`, `assertNoRefundRecorded()`.

---

## HTTP Resources (Output Format)

- **AddressResource** → id, label, recipient_name, phone, province, province_code, city, city_code, district, district_code, village, village_code, postal_code, detail, full_address, is_primary, created_at, updated_at
- **CartResource** → id, items (mapping variant→stock/product), total_items, total_amount
- **CustomOrderResource** → id, reference_number, description, status, estimated_price, estimated_time, admin_notes, attachments, order (jika ada), created_at, updated_at
- **OrderResource** → id, order_number, status, is_custom_order, pricing (subtotal, discount_date_cantik, discount_voucher, total_discount, shipping_cost, final_amount), promo (date_promo, voucher), shipping (courier, resi_number, cost, weight), address (snapshot), notes, items, payment (payment_type, payment_status, amount, snap_token, paid_at), created_at, updated_at
- **RefundRequestResource** → id, order_id, order_number, reason, evidence_url, amount, status, refund_method, admin_notes, manual_reference, reviewed_at, completed_at, created_at

---

## Alur Sistem Utama

### 1. Registrasi & Verifikasi Email

1. User registrasi via `POST /auth/register` → create user + OTP verification
2. OTP dikirim email, 10 menit berlaku, max 5 percobaan
3. User verifikasi via `POST /auth/email/verify` → set email_verified_at
4. Login ditolak hingga email diverifikasi

### 2. Alur Checkout Reguler

1. User memilih alamat dari daftar atau buat alamat baru
2. User keranjang sudah terisi item varian produk
3. `POST /orders` dengan `address_id`, opsional `voucher_code` dan `notes`
4. Backend proses:
   - Validasi stok varian + stok bundle
   - Hitung subtotal
   - Cek/discount promo tanggal aktif
   - Cek/discount voucher (min_purchase, usage_limit)
   - Diskon = min(date_promo + voucher, subtotal)
   - Hitung ongkir via RajaOngkirService (origin: toko, dest: alamat, weight: 2KG, courier: config)
   - Hitung final_amount = subtotal - total_discount + shipping_cost
   - Buat Order dengan status pending_payment
   - Kurangi stok produk/dari komponen bundle
   - Kosongkan keranjang
   - Buat Midtrans Snap token
   - Return order + payment + snap_token

### 3. Alur Custom Order

1. Customer buat custom order via `POST /custom-orders` (description, attachments optional)
2. Admin review panel → set status quoted + estimated_price + estimated_time
3. Customer setujui via `POST /custom-orders/{id}/approve` (pilih alamat, tambah notes)
4. Sistem buat Order baru dari custom order + hitung ongkir
5. Customer lakukan pembayaran Midtrans
6. Setelah payment success → status order menjadi paid/processing → custom order status menjadi approved

### 4. Alur Refund (Admin)

1. Admin lihat daftar refund via `GET /admin/refunds`
2. Admin approve refund via `POST /admin/refunds/{id}/approve` (pilih metode: midtrans/manual)
3. Jika midtrans → diproses via MidtransRefundService
4. Jika manual → admin catat manual_reference saat completed
5. Admin reject via `POST /admin/refunds/{id}/reject`
6. Admin complete manual via `POST /admin/refunds/{id}/complete`

---

## Konfigurasi Environment

File `.env` butuh:

```
MIDTRANS_SERVER_KEY=...
MIDTRANS_IS_PRODUCTION=false
SERVICES_RAJONGKIR_API_KEY=...
SERVICES_RAJONGKIR_ORIGIN_SEARCH=Sumbersari,Jember
SERVICES_RAJONGKIR_WEIGHT=2000
SERVICES_RAJONGKIR_COURIER=jnt
```

---

## Catatan Penting

- Stok 0 saat ini bisa mewakili pre-order; keputusan akhir dilakukan saat checkout
- `shipping.weight` di OrderResource selalu 2000, bukan berat aktual
- Kode wilayah (province_code, city_code, district_code, village_code) harus sesuai urutan dan nilai benar di wilayah.id
- Voucher dan promo tanggal dievaluasi saat checkout, disimpan di order sebagai date_promo_id dan voucher_id
- Setiap customer hanya boleh 1 cart aktif (unique user_id di carts)
- Custom order tidak menggunakan stok produk reguler, tapi bisa memiliki lampiran (max 5 gambar, 2MB per gambar)
- Refund hanya bisa dilakukan jika order status: paid/processing/packing/shipped/completed dan payment_status: success
- Email OTP expires 10 menit, max 5 percobaan per kode
- Sistem menggunakan Laravel Sanctum untuk token API