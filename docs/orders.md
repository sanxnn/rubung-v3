# Pesanan

1. Pelanggan memilih alamat, lalu checkout dengan voucher dan catatan opsional. Backend memvalidasi stok, menghitung diskon dan ongkir, menyimpan detail pesanan, serta mencadangkan stok.
2. Pesanan dimulai dari `pending_payment`. Setelah pembayaran dikonfirmasi, admin menangani transisi `paid → processing → packing → shipped` dan mencatat nomor resi.
3. Setelah barang diterima, pelanggan dapat menyelesaikan pesanan yang sudah dibayar dan dikirim. Pengulangan aksi tidak menambah riwayat. Admin juga dapat menyelesaikan pesanan yang sudah dikirim dan riwayat mencatat pelakunya.
4. Pelanggan dapat membatalkan saat pesanan masih `pending_payment`. Jika token Snap atau ID transaksi sudah tercatat pada pembayaran pending, backend menunggu konfirmasi pembatalan Midtrans sebelum melepas stok dan kuota. Pada `409`, muat ulang detail sebelum mencoba lagi. Pembayaran sukses mengikuti [alur pengajuan refund](refund.md).

Respons konfirmasi penerimaan memuat `data.refund` untuk ringkasan pengajuan terakhir atau `null`. Muat ulang detail jika perlu status pembayaran dan data pesanan terbaru. [Checkout, pembayaran, dan riwayat](api-reference.md#checkout-pembayaran-dan-riwayat) · [Konfirmasi penerimaan](api-reference.md#pengiriman-dan-konfirmasi-penerimaan)

Stok untuk varian reguler dan komponen bundle yang sama dihitung bersama. Jika total kebutuhan melebihi stok, checkout ditolak dan keranjang tetap tersedia. Backend memuat dan mengunci keranjang di dalam transaksi agar dua checkout tidak sama-sama memproses isi lama.
