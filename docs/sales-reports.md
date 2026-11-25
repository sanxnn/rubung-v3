# Laporan penjualan

Buka `/dashboard/reports` untuk daftar pesanan dan ringkasan. Saring dengan `start_date`, `end_date`, dan `status`. Filter tanggal mengikuti tanggal pembuatan pesanan. Ekspor Excel pada `/dashboard/reports/export` menggunakan filter yang sama.

Omzet dihitung dari pesanan yang sudah dibayar dan dikurangi nominal refund `completed` yang terkait. Pengajuan yang sedang diproses belum mengurangi omzet. Ekspor menampilkan nominal refund dan total bersih per pesanan. [Alur refund](refund.md)
