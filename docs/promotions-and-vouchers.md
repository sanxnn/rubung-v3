# Promo dan voucher

Admin mengelola periode, jenis diskon, nilai, dan status aktif melalui menu promo dan voucher. Voucher juga memiliki minimum belanja dan dapat memiliki batas kuota. Pratinjau voucher mengevaluasi keranjang pelanggan dan tidak memakai kuota.

Pada checkout, backend menghitung promo tanggal yang aktif dan memvalidasi ulang voucher. Promo tanggal diterapkan terlebih dahulu. Voucher hanya memakai sisa subtotal sebelum ongkir. Voucher yang menghasilkan potongan nol tidak memakai kuota. API, rincian pembayaran, laporan, dan ekspor menampilkan potongan yang benar-benar diterapkan. Gunakan subtotal hasil pratinjau untuk tampilan voucher dan total pada respons checkout untuk tagihan. Pemakaian voucher dicatat saat membuat pesanan. Pembatalan pesanan menunggu pembayaran dapat memulihkan kuota melalui alur pembatalan. Refund selesai mempertahankan catatan pemakaian asal. [Body pratinjau voucher](api-reference.md#pratinjau-voucher)
