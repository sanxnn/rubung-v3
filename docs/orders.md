# Pesanan

1. Pelanggan memilih alamat, lalu checkout dengan voucher dan catatan opsional. Backend memvalidasi stok, menghitung diskon dan ongkir, menyimpan detail pesanan, serta mencadangkan stok.
2. Pesanan dimulai dari `pending_payment`. Setelah pembayaran dikonfirmasi, admin menangani transisi `paid → processing → packing → shipped` dan mencatat nomor resi.
3. Setelah barang diterima, pelanggan dapat menyelesaikan pesanan yang sudah dibayar dan dikirim. Pengulangan aksi tidak menambah riwayat. Admin juga dapat menyelesaikan pesanan yang sudah dikirim dan riwayat mencatat pelakunya.
4. Pembatalan pelanggan tersedia selama pesanan masih menunggu pembayaran dan mengikuti hasil pemeriksaan transaksi. Pesanan yang sudah dibayar mengikuti alur pengajuan refund.

Muat ulang detail agar status pembayaran dan ringkasan refund terbaru tampil. Refund memiliki status tersendiri. [Checkout, pembayaran, dan riwayat](api-reference.md#checkout-pembayaran-dan-riwayat) · [Konfirmasi penerimaan](api-reference.md#pengiriman-dan-konfirmasi-penerimaan)
