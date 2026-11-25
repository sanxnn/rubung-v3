# Alamat dan pengiriman

1. Pilih provinsi, kabupaten/kota, kecamatan, dan desa/kelurahan secara berurutan. Kirim semua kode wilayah dan kode pos lima digit. Backend memvalidasi induk tiap kode dan mengisi nama wilayah.
2. Pelanggan dapat menyimpan beberapa alamat dan mengganti alamat utama. Alamat pertama otomatis menjadi utama. Checkout memakai alamat milik pelanggan dan menyimpan salinan alamat pada pesanan.
3. Backend menghitung ongkir melalui RajaOngkir berdasarkan alamat, lokasi asal, kurir, dan berat yang dikonfigurasi. Gunakan ongkir dari respons pesanan. Field `shipping.weight` pada API bernilai tetap 2000.
4. Admin mengisi resi ketika mengubah pesanan menjadi `shipped`. Pelanggan melihat resi pada detail pesanan.

[Kontrak alamat dan wilayah](api-reference.md#alamat) · [Alur pesanan](orders.md)
