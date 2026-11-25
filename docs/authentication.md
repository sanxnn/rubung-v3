# Autentikasi dan profil

1. Pelanggan registrasi dengan identitas, email, password, dan konfirmasi password. Backend mengirim OTP verifikasi.
2. Verifikasi OTP, lalu login untuk memperoleh token Sanctum. Login akun pelanggan yang belum terverifikasi ditolak.
3. Gunakan Bearer token untuk membaca atau memperbarui profil. Perubahan email memulai verifikasi ulang. Logout mencabut token saat ini.
4. Untuk lupa password, minta OTP reset, verifikasi untuk memperoleh token reset, lalu kirim password baru dan konfirmasinya. Reset mencabut token login lama.

OTP berlaku sepuluh menit dengan maksimal lima percobaan per kode. Admin masuk dari `/login` dengan sesi web. Alur pelanggan dan admin memakai mekanisme autentikasi sesuai permukaannya. [Endpoint dan body akun](api-reference.md#akun-dan-otp)
