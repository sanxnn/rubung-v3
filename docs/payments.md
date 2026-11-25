# Pembayaran Midtrans

Checkout mencoba menyediakan token Snap. Klien membuka Snap jika pesanan masih `pending_payment` dan token tersedia. Jika token kosong, lihat status pesanan dan pembayaran pada respons checkout. Panggil endpoint retry Snap hanya jika pesanan masih menunggu pembayaran. Jika webhook sudah mengonfirmasi pembayaran, checkout mengembalikan status terbaru dan token bisa kosong. Klien memuat ulang detail saat kembali dari Snap.

Midtrans mengirim webhook ke backend. Backend memeriksa signature dan nominal, lalu menerapkan transisi pembayaran dan pesanan. Pada Flutter, gunakan hasil pembacaan detail pesanan sebagai sumber status akhir. Jika transaksi gagal atau kedaluwarsa, ikuti status pesanan yang dikembalikan server.

Pada pembatalan, pembayaran `pending` dengan token Snap atau ID transaksi Midtrans harus mendapat konfirmasi Midtrans sebelum cadangan dilepas. Respons `409` berarti hasil belum terkonfirmasi atau pembayaran baru tercatat. Muat ulang pesanan sebelum mencoba lagi. Jika pembayaran sukses tercatat pada pesanan batal, ikuti alur refund.

Gunakan pasangan kunci sandbox dan `MIDTRANS_IS_PRODUCTION=false` di lingkungan pengujian. Arahkan webhook Midtrans ke URL publik backend yang sesuai. [Referensi checkout dan Snap](api-reference.md#checkout-pembayaran-dan-riwayat) · [Refund](refund.md)
