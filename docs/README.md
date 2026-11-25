# Dokumentasi backend

- [Referensi API](api-reference.md) memuat endpoint, autentikasi, body request, contoh respons, dan status HTTP.
- [Pengujian API di Postman dan HTTPie](api-testing/README.md) memuat file impor, environment, dan urutan pengujian.
- [Arsitektur backend](backend-architecture.md) memuat stack, versi, struktur aplikasi, database, penyimpanan, dan integrasi.
- [Catatan perbaikan checkout dan pembayaran](checkout-consistency-fixes.md) merangkum kasus audit, keputusan, dan verifikasi.
- [README proyek](../README.md) memuat langkah instalasi dan pengujian lokal.

## Panduan fitur

| Fitur | Cakupan |
| --- | --- |
| [Autentikasi dan profil](authentication.md) | Registrasi, OTP, login, reset password, profil, dan sesi admin |
| [Katalog](catalog.md) | Kategori, subkategori, varian, bundling, media, dan pengelolaan admin |
| [Alamat dan pengiriman](addresses-and-shipping.md) | Wilayah, alamat utama, ongkir, kurir, dan nomor resi |
| [Keranjang](cart.md) | Item, kuantitas, stok, dan checkout |
| [Promo dan voucher](promotions-and-vouchers.md) | Promo tanggal, pratinjau voucher, kuota, dan aturan diskon |
| [Pesanan](orders.md) | Checkout, riwayat, status, pembatalan, dan konfirmasi penerimaan |
| [Pembayaran](payments.md) | Midtrans Snap, webhook, dan status transaksi |
| [Custom order](custom-orders.md) | Permintaan, lampiran, penawaran, dan pembayaran |
| [Refund](refund.md) | Pengajuan pelanggan, peninjauan admin, dan rekonsiliasi |
| [Panel admin](admin-dashboard.md) | Akses, dashboard, pengelolaan pelanggan, dan modul admin |
| [Laporan penjualan](sales-reports.md) | Filter, omzet bersih, dan ekspor Excel |
| [Profil perusahaan dan kontak](company-profile-and-contact.md) | Halaman publik dan pengiriman pesan lewat email |
