# Arsitektur backend Batik Rubung Kuning

Backend Laravel 12 menyediakan API pelanggan dan panel web admin. Lihat [kontrak API dan alur sistem](../roadmap.md) untuk integrasi, serta [README](../README.md) untuk konfigurasi dan pengujian lokal.

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
| `date_promos` | Promo berdasarkan tanggal. Field: name, discount_type, discount_value, start_datetime, end_datetime, is_active. |
| `orders` | Pesanan. Field: user_id, address_id, order_number (unique), subtotal, discount_date_cantik, date_promo_id, discount_voucher, voucher_id, shipping_cost, final_amount, status (pending_payment/paid/processing/packing/shipped/completed/cancelled), shipping_courier, resi_number, snapshot_* fields, notes, stock_reserved_at, stock_released_at. |
| `order_items` | Item pesanan. Field: order_id, product_variant_id (nullable untuk custom order), product_name, variant_name, quantity, unit_price, subtotal. |
| `order_status_histories` | Riwayat status pesanan. Field: order_id, old_status, new_status, changed_by (user), notes, created_at. |
| `custom_orders` | Permintaan produk custom. Field: user_id, reference_number (unique), description, estimated_price, estimated_time, status (pending_review/quoted/approved/rejected/cancelled), admin_notes, order_id (jika sudah jadi order). |
| `custom_order_attachments` | Lampiran custom order. Field: custom_order_id, file_path, uploaded_by, created_at. |
| `payments` | Riwayat pembayaran Midtrans. Field: order_id (unique), midtrans_transaction_id, payment_type, payment_status (pending/success/failed/expire), amount, paid_at, snap_token. |
| `refund_requests` | Pengajuan refund. Field: order_id, user_id, reviewed_by (nullable), reason, evidence_path, amount, status (pending/approved/processing/completed/rejected/cancelled), refund_method (midtrans/manual), refund_key, manual_reference, admin_notes, reviewed_at, completed_at. |
| `email_otps` | OTP verifikasi. Field: user_id, email, otp_hash, purpose (email_verification/password_reset), attempts, expires_at, used_at. |

### Relasi Utama

- User → many Addresses → one primary
- User → many Orders → one Payment
- User → many CustomOrders
- Category → many Products → many Variants
- Order → many OrderItems → one ProductVariant
- Order → many RefundRequests (pengajuan ulang dimungkinkan setelah ditolak atau dibatalkan)
- CustomOrder → one Order (jika disetujui)
- RefundRequest → one Order → one Payment

## Model dan relasi

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

## Layanan eksternal

### WilayahService

- `list(string $level, ?string $parentCode)` → ambil data wilayah dari wilayah.id API (provinces/regencies/districts/villages)
- `resolve(array $codes)` → resolve kode wilayah ke nama (province, city, district, village). Butuh province_code, city_code, district_code, village_code.

### RajaOngkirService

- `getCost(Address $address)` → hitung ongkir dari origin toko ke alamat customer menggunakan RajaOngkir API.
- `findOrigin(string $key)` → cari origin toko (konfig: services.rajongkir.origin_search, default: "Sumbersari,Jember")
- `findDestination(?string $postalCode, ?string $district, ?string $city, string $key)` → cari destination customer (prioritas: postal_code → district+city → city)

### MidtransRefundService

- Digunakan untuk memproses refund, memeriksa konfirmasi dari API status Midtrans, dan memeriksa kelayakan sebelum pengalihan ke jalur manual.
