# Laporan penjualan

Buka `/dashboard/reports` untuk daftar pesanan dan ringkasan. Saring dengan `start_date`, `end_date`, dan `status`. Filter tanggal mengikuti tanggal pembuatan pesanan. Ekspor Excel pada `/dashboard/reports/export` menggunakan filter yang sama.

Ringkasan menghitung omzet dari pesanan berstatus `paid`, `processing`, `packing`, `shipped`, atau `completed`, dikurangi refund `completed` yang terkait. Filter tanggal mengikuti tanggal pesanan, termasuk untuk pengurangan refund. Pembayaran yang terlambat masuk pada pesanan `cancelled` tetap di luar omzet penjualan. Nominal diskon mengikuti potongan efektif hingga batas subtotal. Ekspor menampilkan diskon, refund, dan total bersih per pesanan. [Alur refund](refund.md)
