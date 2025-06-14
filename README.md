# Logistics Maritime Website

Sistem manajemen logistik maritim berbasis web yang menghubungkan seluruh wilayah Indonesia dengan layanan pengiriman yang handal dan terpercaya.

##  Deskripsi Proyek

Website Logistics Maritime adalah platform digital yang menyediakan layanan logistik maritim dengan sistem manajemen yang terintegrasi. Platform ini memungkinkan pengguna untuk melacak pengiriman, mengelola pesanan, dan mengakses berbagai informasi terkait layanan logistik maritim.

##  Fitur Utama

### Untuk Pengguna Umum
- **Beranda**: Informasi umum perusahaan dan layanan
- **Tracking Pengiriman**: Sistem pelacakan barang real-time
- **Informasi Jadwal**: Jadwal keberangkatan dan estimasi tiba
- **Registrasi & Login**: Sistem autentikasi pengguna

### Untuk Pengguna Terdaftar
- **Dashboard Pengguna**: Panel kontrol personal
- **Riwayat Pengiriman**: Histori transaksi dan pengiriman
- **Manajemen Profil**: Pengaturan akun pengguna

### Untuk Administrator
- **Dashboard Admin**: Panel kontrol administratif
- **Manajemen Pengguna**: Kelola akun pengguna
- **Manajemen Pengiriman**: Kontrol operasional pengiriman
- **Laporan**: Analisis dan reporting

##  Teknologi yang Digunakan

- **Backend**: PHP (Native)
- **Frontend**: HTML5, CSS3, JavaScript
- **Database**: MySQL
- **Styling**: CSS Custom + Responsive Design
- **Icons**: Custom icons dan Lucide icons

##  Fitur Responsif

Website ini dirancang dengan pendekatan mobile-first dan mendukung:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (320px - 767px)

##  Sistem Autentikasi

### Jenis Pengguna
1. **Guest**: Akses terbatas ke informasi publik
2. **User**: Akses ke dashboard dan fitur tracking
3. **Admin**: Akses penuh ke sistem manajemen

### Fitur Keamanan
- Session management
- Password hashing
- Role-based access control
- CSRF protection

##  Halaman Website

### Halaman Publik
1. **Beranda** (`index.php`)
   - Hero section dengan CTA
   - Sejarah perusahaan
   - Informasi pelayanan
   - Highlight fitur
   - Mitra kerja sama

2. **Tentang Kami** (`public/tentang.php`)
   - Tentang perusahaan
   - Fasilitas dan pelayanan
   - jaminan pelayanan
   - Syarat dan ketentuan 
   - Jadwal Pengiriman
   - Karir
   - Tim

3. **Fasilitas & Pelayanan** (`public/fasilitas.php`)
   - Pelabuhan
   - Pelayanan Pelabuhan
   - Kapal
   - Kontainer 
   - Proses Logistik Maritim
   - Peta Jaringan Logistik

4. **Jaminan Pelayanan** (`public/jaminan.php`)
   - Pemesanan Yang Mudah
   - Ketepatan Waktu
   - Operasi Profesional
   - Standar manajemen
   - Jaminan klaim	
   - Respon Cepat

5. **Syarat & Ketentuan** (`public/syarat.php`)
   - Ketentuan Umum
   - Larangan pengiriman
   - Inspeksi
   - Menjamin pengiriman kepemilikan
   - Prosedur untuk klaim

6. **Jadwal Pengiriman** (`public/informasi.php`)
   - Nama Kapal
   - Pelabuhan Keberangkatan
   - Pelabuhan Tujuan
   - Tanggal Keberangkatan

7. **Karir** (`public/karir.php`)
   - Mengapa Berkarir bersama kami?
   - Lowongan Kerja
   - Lamaran kerja

8. **Tim** (`public/tim.php`)
   - Jabatan tim
   - Profil tim
   - Kontak tim

9. **Kontak** (`public/kontak.php`)
   - Informasi kontak
   - Formulir kontak
   - Lokasi kantor

### Halaman Autentikasi
- **Login** (`login/login.php`)
- **Register** (`login/register.php`)
- **Logout** (`login/logout.php`)

### Halaman Dashboard
- **User Dashboard** (`user/user-dashboard.php`)
- **Admin Dashboard** (`admin/dashboard.php`)

##  Desain & UI/UX
- Figma : https://www.figma.com/design/OrI86Rh6XitzRaFwWjkwiK/WIREFRAME-UI-UX-KELOMPOK-6?node-id=93-2&p=f&t=LtkBhjOwYxfBptqC-0

### Color Scheme
- Primary: #2c5aa0 (Blue)
- Secondary: #1e3f73 (Dark Blue)
- Background: #ffffff (White)
- Text: #333333 (Dark Gray)

### Typography
- Font Family: System fonts
- Responsive font sizes
- Clear hierarchy

### Components
- Navigation dengan dropdown
- Cards untuk konten
- Buttons dengan hover effects
- Modal dan form elements
- Social media icons

##  Kontak & Support

- **Alamat**: Jalan Pelabuhan Gang Pantai Indah No 72
- **Telepon**: +6212-3456-7810
- **Email**: logisticmaritime@gmail.com
- **WhatsApp**: +6281234567890

### Social Media
- Instagram: @logistikmaritime
- Facebook: /logistikmaritime
- TikTok: @logistikmaritime
- Twitter: @logistikmaritime
- LinkedIn: /company/logistikmaritime

##  Mitra Kerja Sama

Website menampilkan logo dan link ke mitra strategis:
- ASUS
- Shopee
- Komatsu
- Adidas
- Tokopedia
- Bukalapak
- Advan
- iPhone
- Yamaha

##  Instalasi & Setup

1. Clone atau download project
2. Setup web server (Apache/Nginx)
3. Konfigurasi database di `config/database.php`
4. Import database schema
5. Sesuaikan path file di konfigurasi
6. Akses website melalui browser

##  Konfigurasi

Pastikan untuk mengkonfigurasi:
- Database connection
- Session settings
- File paths
- Email settings (jika ada)
- Security settings

##  Lisensi

© 2025 Logistics Maritime. All rights reserved

 ##  Anggota Tim

| Nama                       | NIM        |
| -------------------------- | ---------- |
| Fauzan Aldi                | 2301020022 |
| Albertus Nyam Frandis      | 2301020034 |
| Roy Adiyta                 | 2301020093 |
| Fadhillah Nanda Maulana    | 2301020088 |
