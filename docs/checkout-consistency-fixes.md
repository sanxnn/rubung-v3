# Perbaikan konsistensi checkout dan pembayaran

Perbaikan ini berangkat dari audit backend pada `main` commit `c45d6a2`.

| Kasus | Penyelesaian dan alasan |
| --- | --- |
| Promo Rp70.000 dan voucher Rp50.000 pada subtotal Rp100.000 membuat rincian Snap berbeda dari total tagihan. Laporan juga mencatat potongan terlalu tinggi. | Terapkan promo dulu dan batasi voucher ke sisa Rp30.000. Simpan potongan efektif untuk pesanan baru. Untuk pesanan lama, batasi nilai saat dibaca sehingga percobaan ulang Snap dan laporan konsisten tanpa migrasi atau perubahan total historis. Voucher tanpa manfaat tidak memakai kuota. |
| Satu bundle memakai dua unit dan pembelian reguler memakai dua unit varian yang sama, sementara stok hanya tiga. | Jumlahkan permintaan stok per varian dan periksa semua kebutuhan sebelum mengurangi stok. Kunci varian dengan urutan ID dan baca ulang keranjang di dalam transaksi. Pendekatan ini menghindari alokasi berdasarkan urutan item, dan permintaan yang gagal tidak mengosongkan keranjang. |
| Midtrans menyatakan pembayaran sukses setelah pesanan dibatalkan. Pengajuan refund pelanggan sebelumnya ditolak. | Izinkan pemilik mengajukan refund pada pesanan batal hanya setelah pembayaran sukses. Tampilkan penanda di detail admin dan gunakan review refund yang sudah ada. Pesanan tetap dibatalkan karena stok atau kuota dapat sudah dilepas. |
| Respons konfirmasi penerimaan menghilangkan ringkasan refund yang terlihat pada detail pesanan. | Muat relasi refund yang sama setelah konfirmasi agar klien dapat memakai respons aksi tanpa kehilangan ringkasan. |

## Verifikasi

Tes regresi mencakup penolakan stok gabungan yang tidak cukup, checkout saat stok tepat cukup, percobaan checkout ulang pada keranjang kosong, potongan efektif pada Snap awal dan retry pesanan lama, ringkasan laporan, serta refund untuk pesanan batal dengan pembayaran sukses. Snap dan ongkir disimulasikan. Pengujian ini belum mencakup checkout serentak dari dua koneksi atau transaksi Midtrans sandbox langsung.

## Tindak lanjut operasional

Rekonsiliasi terjadwal perlu aturan untuk transaksi yang belum terkonfirmasi, pemulihan stok, serta pemantauan scheduler. Penanda pre-order per varian bisa dipertimbangkan jika varian Batik nantinya memerlukan status habis yang berbeda dari PO.


## Tindak lanjut audit dev

- **Respons Snap terlambat.** Pembuatan token pertama dan retry memakai penguncian pesanan yang sama seperti webhook. Token baru hanya disimpan jika pesanan masih menunggu pembayaran. Jika pembayaran sudah sukses, checkout mengembalikan status terbaru dan mempertahankan ID serta waktu pembayaran. Tes menyimulasikan settlement saat request Snap masih berjalan.
- **Stok nol dan PO.** Keranjang dan checkout memakai syarat PO yang sama. Varian reguler kategori Batik dengan stok nol boleh dipesan tanpa cadangan stok. Varian reguler kategori lain membutuhkan stok, sedangkan bundling tetap membutuhkan stok komponen. Ini menyamakan perilaku API dengan arti badge PO di panel admin.
