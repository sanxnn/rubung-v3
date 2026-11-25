# Pembayaran Midtrans

Checkout mencoba menyediakan token Snap. Jika token belum tersedia, pelanggan dapat meminta token melalui endpoint Snap untuk pesanan yang masih bisa dibayar. Klien membuka Snap dan memuat ulang detail pesanan ketika kembali.

Midtrans mengirim webhook ke backend. Backend memeriksa signature dan nominal, lalu menerapkan transisi pembayaran dan pesanan. Pada Flutter, gunakan hasil pembacaan detail pesanan sebagai sumber status akhir. Jika transaksi gagal atau kedaluwarsa, ikuti status pesanan yang dikembalikan server.

Gunakan pasangan kunci sandbox dan `MIDTRANS_IS_PRODUCTION=false` di lingkungan pengujian. Arahkan webhook Midtrans ke URL publik backend yang sesuai. [Referensi checkout dan Snap](api-reference.md#checkout-pembayaran-dan-riwayat) · [Refund](refund.md)
