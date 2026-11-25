# Custom order

1. Pelanggan mengirim deskripsi dan gambar opsional. Maksimal lima gambar JPEG, PNG, atau WebP per pengajuan, masing-masing maksimal 2 MB. Lampiran dapat ditambah saat `pending_review`.
2. Admin memberi penawaran dengan estimasi harga, estimasi waktu, dan catatan opsional, atau menolak. Penawaran dapat dilihat saat `quoted`.
3. Pelanggan menyetujui penawaran dengan memilih alamat. Backend membuat pesanan terhubung dengan status `pending_payment` dan custom order menjadi `approved`. Gunakan ID pesanan hasil persetujuan untuk Snap.
4. Pelanggan dapat membatalkan custom order saat `pending_review` atau `quoted`. Setelah disetujui, gunakan alur pesanan dan pembayaran yang terkait.

[Endpoint, body, dan respons custom order](api-reference.md#custom-order)
