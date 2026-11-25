# Keranjang

Pelanggan memiliki satu keranjang. Tambah item menggunakan `product_variant_id` dan kuantitas minimal satu. Penambahan varian yang sama menambah kuantitas. Gunakan ID item keranjang untuk mengganti kuantitas atau menghapus item. ID tersebut berbeda dari ID varian.

Subtotal keranjang berasal dari harga server dan belum termasuk promo, voucher, atau ongkir. Keranjang belum mencadangkan stok. Checkout membaca ulang keranjang, memeriksa stok varian atau komponen paket, menghitung total, dan mengosongkan keranjang saat pesanan berhasil dibuat. [Endpoint dan body keranjang](api-reference.md#keranjang) · [Alur checkout](orders.md)

Varian reguler Batik dengan stok nol dapat dipesan sebagai PO. Stok nol pada kategori lain, serta stok bundling yang habis, ditolak saat menambah atau mengubah jumlah item dan diperiksa ulang saat checkout.
