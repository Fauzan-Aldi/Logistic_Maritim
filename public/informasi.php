<?php
// Konfigurasi database (sesuaikan dengan pengaturan Anda)
$host = 'localhost';
$dbname = 'logistics_maritime';
$username = 'root';
$password = '';

// Variabel untuk hasil pencarian
$tracking_data = null;
$search_performed = false;

// Proses pencarian jika ada input
if (isset($_POST['search']) && !empty($_POST['tracking_id'])) {
    $search_performed = true;
    $tracking_id = $_POST['tracking_id'];
    
    // Di sini Anda bisa menambahkan koneksi ke database
    // Untuk sementara, saya akan menggunakan data dummy
    if ($tracking_id == 'SHIP123456789ID') {
        $tracking_data = [
            'id' => 'SHIP123456789ID',
            'pengirim' => [
                'nama' => 'Andi Pratama',
                'alamat' => 'Jl. Merpati No. 12, Batam, Kepulauan Riau, 29349',
                'telepon' => '0812-3456-7890'
            ],
            'penerima' => [
                'nama' => 'Siti Nurhaliza',
                'alamat' => 'Jl. Anggrek Raya No. 45, Jakarta Utara, Jakarta, 10630',
                'telepon' => '0821-9876-5432'
            ],
            'kapal' => 'LGMRT-001',
            'keberangkatan' => 'Batam',
            'tgl_berangkat' => '01 April 2025',
            'tujuan' => 'Jakarta',
            'estimasi_tiba' => '08 April 2025',
            'status' => 'Dalam Perjalanan'
        ];
    }
}

// Data jadwal pengiriman (dalam implementasi nyata, ambil dari database)
$jadwal_pengiriman = [
    ['kapal' => 'LGMRT-001', 'keberangkatan' => 'Batam', 'tujuan' => 'Jakarta', 'tanggal' => '01 April 2025', 'status' => 'Penuh'],
    ['kapal' => 'LGMRT-002', 'keberangkatan' => 'Aceh', 'tujuan' => 'Jakarta', 'tanggal' => '05 April 2025', 'status' => 'Tersedia'],
    ['kapal' => 'LGMRT-003', 'keberangkatan' => 'Semarang', 'tujuan' => 'Jakarta', 'tanggal' => '08 April 2025', 'status' => 'Penuh'],
    ['kapal' => 'LGMRT-004', 'keberangkatan' => 'Pontianak', 'tujuan' => 'Jakarta', 'tanggal' => '12 April 2025', 'status' => 'Tersedia'],
    ['kapal' => 'LGMRT-005', 'keberangkatan' => 'Jakarta', 'tujuan' => 'Makassar', 'tanggal' => '14 April 2025', 'status' => 'Tersedia'],
    ['kapal' => 'LGMRT-006', 'keberangkatan' => 'Makassar', 'tujuan' => 'Bali', 'tanggal' => '19 April 2025', 'status' => 'Penuh'],
    ['kapal' => 'LGMRT-007', 'keberangkatan' => 'Timika', 'tujuan' => 'Makassar', 'tanggal' => '21 April 2025', 'status' => 'Tersedia'],
    ['kapal' => 'LGMRT-008', 'keberangkatan' => 'Makassar', 'tujuan' => 'Semarang', 'tanggal' => '23 April 2025', 'status' => 'Penuh'],
    ['kapal' => 'LGMRT-009', 'keberangkatan' => 'Jakarta', 'tujuan' => 'Pontianak', 'tanggal' => '27 April 2025', 'status' => 'Tersedia'],
    ['kapal' => 'LGMRT-010', 'keberangkatan' => 'Bali', 'tujuan' => 'Timika', 'tanggal' => '30 April 2025', 'status' => 'Penuh']
];
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informasi Pengiriman - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/css/informasi.css" />
  </head>
  <style>
      .auth-buttons {
        display: flex;
        gap: 10px;
        align-items: center;
      }

      .btn-login,
      .btn-signup {
        padding: 8px 16px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
      }

      .btn-login {
        color: #2c5aa0;
        border: 2px solid #2c5aa0;
        background: transparent;
      }

      .btn-login:hover {
        background: #2c5aa0;
        color: white;
      }

      .btn-signup {
        background: #2c5aa0;
        color: white;
        border: 2px solid #2c5aa0;
      }

      .btn-signup:hover {
        background: #1e3f73;
        border-color: #1e3f73;
      }

      /* Mobile Auth Buttons */
      .mobile-auth-buttons {
        display: flex;
        gap: 10px;
        padding: 15px 0;
        border-top: 1px solid #eee;
        margin-top: 10px;
      }

      .btn-login-mobile,
      .btn-signup-mobile {
        flex: 1;
        padding: 10px;
        text-align: center;
        text-decoration: none;
        border-radius: 5px;
        font-weight: 500;
        transition: all 0.3s ease;
      }

      .btn-login-mobile {
        color: #2c5aa0;
        border: 2px solid #2c5aa0;
        background: transparent;
      }

      .btn-signup-mobile {
        background: #2c5aa0;
        color: white;
        border: 2px solid #2c5aa0;
      }
</style>
  <body>
    <!-- Header -->
    <header class="header">
      <div class="nav-container">
        <div class="logo">
          <div class="logo-icon">
            <img src="../logistikmaritim/image/logo.png" alt="Logo" />
          </div>
          <span>Logistics Maritime</span>
        </div>
        <nav>
          <ul class="nav-menu">
            <li><a href="../index.php">Beranda</a></li>
            <li class="dropdown">
              <a href="#tentang" class="dropdown-toggle">
                Tentang Kami
                <span class="dropdown-arrow">▼</span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="tentang.php">Tentang Perusahaan</a></li>
                <li><a href="fasilitas.php">Fasilitas dan Pelayanan</a></li>
                <li><a href="jaminan.php">Jaminan Pelayanan</a></li>
                <li><a href="syarat.php">Syarat dan Ketentuan</a></li>
                <li><a href="informasi.php">Jadwal Pengiriman</a></li>
                <li><a href="karir.php">Karir</a></li>
                <li><a href="tim.php">Tim</a></li>
              </ul>
            </li>
            <li><a href="informasi.php">Jadwal Pengiriman</a></li>
            <li><a href="kontak.php">Kontak Kami</a></li>
          </ul>
        </nav>
         <!-- Auth Buttons 
        <div class="auth-buttons">
          <a href="../login/login.php" class="btn-login">Masuk</a>
          <a href="../login/register.php" class="btn-signup">Daftar</a>
        </div> -->

        <!-- Mobile Menu Button -->
        <div class="mobile-menu-btn">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </div>

      <!-- Mobile Menu -->
      <div class="mobile-menu">
        <ul>
          <li><a href="index.php">Beranda</a></li>
          <li class="mobile-dropdown">
            <a href="#" class="mobile-dropdown-toggle">
              Tentang Kami
              <span class="mobile-dropdown-arrow">▼</span>
            </a>
            <ul class="mobile-dropdown-menu">
              <li><a href="tentang-perusahaan.php">Profil Perusahaan</a></li>
              <li><a href="#sejarah">Sejarah</a></li>
              <li><a href="#visi-misi">Visi & Misi</a></li>
              <li><a href="tim-profesional.php">Tim Manajemen</a></li>
              <li><a href="#penghargaan">Penghargaan</a></li>
            </ul>
          </li>
          <li><a href="#" class="active">Jadwal Pengiriman</a></li>
          <li><a href="kontak.php">Kontak Kami</a></li>
        </ul>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-overlay">
        <div class="hero-content">
          <h1>Cek Jadwal Pengiriman</h1>
          <p>Pantau Jadwal Pengiriman</p>
        </div>
      </div>
    </section>

    <!-- Schedule Section -->
    <section class="schedule-section">
      <div class="schedule-container">
        <h2>JADWAL PENGIRIMAN</h2>
        <p class="schedule-subtitle">Jadwal Pengiriman April 2025</p>

        <div class="schedule-table-container">
          <table class="schedule-table">
            <thead>
              <tr>
                <th>Nama Kapal</th>
                <th>Pelabuhan Keberangkatan</th>
                <th>Pelabuhan Tujuan</th>
                <th>Tanggal Keberangkatan</th>

              </tr>
            </thead>
            <tbody>
              <?php foreach ($jadwal_pengiriman as $jadwal): ?>
              <tr>
                <td><?php echo htmlspecialchars($jadwal['kapal']); ?></td>
                <td><?php echo htmlspecialchars($jadwal['keberangkatan']); ?></td>
                <td><?php echo htmlspecialchars($jadwal['tujuan']); ?></td>
                <td><?php echo htmlspecialchars($jadwal['tanggal']); ?></td>
 
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  <!-- Footer -->
  <footer class="footer" id="kontak">
      <div class="footer-container">
        <div class="footer-section">
          <div class="footer-logo">
            <div class="footer-logo-icon">
              <img src="../logistikmaritim/image/logo.png" alt="logo" />
            </div>
            <div>
              <h3>Logistics Maritime</h3>
              <p>Mengirim barang dengan efisien, tepat, dan profesional</p>
            </div>
          </div>
        </div>

        <div class="footer-section">
          <h4>Navigasi</h4>
          <ul>
            <li><a href="../index.php">Beranda</a></li>
            <li><a href="tentang.php">Tentang Perusahaan</a></li>
            <li><a href="fasilitas.php">Fasilitas dan Pelayanan</a></li>
            <li><a href="jaminan.php">Jaminan Pelayanan</a></li>
            <li><a href="syarat.php">Syarat dan Ketentuan</a></li>
            <li><a href="informasi.php">Jadwal Pengiriman</a></li>
            <li><a href="karir.php">Karir</a></li>
            <li><a href="tim.php">Tim</a></li>
          </ul>
        </div>

        <div class="footer-section">
          <h4>Kontak Kami</h4>
          <p>
            <strong>Kantor Pusat</strong><br />
            Jalan Pelabuhan Gang Pantai Indah No 72
          </p>
          <div class="social-icons">
              <a href="https://wa.me/6281234567890" target="_blank" class="social-icon">
                <img src="../logistikmaritim/image/wa.png" alt="WhatsApp" />
              </a>
              <a href="https://www.instagram.com/logistikmaritime" target="_blank" class="social-icon">
                <img src="../logistikmaritim/image/ig.png" alt="Instagram" />
              </a>
              <a href="https://www.facebook.com/logistikmaritime" target="_blank" class="social-icon">
                <img src="../logistikmaritim/image/fb.png" alt="Facebook" />
              </a>
              <a href="https://www.tiktok.com/@logistikmaritime" target="_blank" class="social-icon">
                <img src="../logistikmaritim/image/tt.png" alt="TikTok" />
              </a>
              <a href="https://twitter.com/logistikmaritime" target="_blank" class="social-icon">
                <img src="../logistikmaritim/image/tw.png" alt="Twitter" />
              </a>
              <a href="https://www.linkedin.com/company/logistikmaritime" target="_blank" class="social-icon">
                <img src="../logistikmaritim/image/ld.png" alt="LinkedIn" />
              </a>
            </div>

          <p>
            <strong>Telepon Pusat</strong><br />
            +6212-3456-7810
          </p>

          <p>
            <strong>Email</strong><br />
            logisticmaritime@gmail.com
          </p>
        </div>
      </div>

      <div class="footer-bottom">
        <p>© 2025 Logistics Maritime. All rights reserved</p>
      </div>
    </footer>

    <script>
      // Dropdown functionality
      document.addEventListener('DOMContentLoaded', function () {
        // Desktop dropdown
        const dropdown = document.querySelector('.dropdown');
        const dropdownToggle = document.querySelector('.dropdown-toggle');

        if (dropdown && dropdownToggle) {
          dropdownToggle.addEventListener('click', function (e) {
            e.preventDefault();
            dropdown.classList.toggle('active');
          });

          // Close dropdown when clicking outside
          document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
              dropdown.classList.remove('active');
            }
          });
        }

        // Mobile menu functionality
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu');

        if (mobileMenuBtn && mobileMenu) {
          mobileMenuBtn.addEventListener('click', function () {
            mobileMenuBtn.classList.toggle('active');
            mobileMenu.classList.toggle('active');
          });
        }

        // Mobile dropdown
        const mobileDropdown = document.querySelector('.mobile-dropdown');
        const mobileDropdownToggle = document.querySelector(
          '.mobile-dropdown-toggle'
        );

        if (mobileDropdown && mobileDropdownToggle) {
          mobileDropdownToggle.addEventListener('click', function (e) {
            e.preventDefault();
            mobileDropdown.classList.toggle('active');
          });
        }

        // Table row hover effects
        const tableRows = document.querySelectorAll('tbody tr');
        tableRows.forEach(row => {
          row.addEventListener('mouseenter', function () {
            this.style.backgroundColor = '#f8f9fa';
          });

          row.addEventListener('mouseleave', function () {
            this.style.backgroundColor = '';
          });
        });

        <?php if ($search_performed): ?>
        // Auto scroll to results if search was performed
        const trackingResults = document.getElementById('trackingResults');
        if (trackingResults) {
          trackingResults.scrollIntoView({ behavior: 'smooth' });
        }
        <?php endif; ?>
      });
      // Back to Top Button Script
document.addEventListener("DOMContentLoaded", () => {
  // Create the button element
  const backToTopButton = document.createElement("button")
  backToTopButton.id = "back-to-top"
  backToTopButton.innerHTML = "&#8679;" // Up arrow symbol
  backToTopButton.setAttribute("aria-label", "Back to top")
  backToTopButton.setAttribute("title", "Back to top")

  // Apply styles to the button
  backToTopButton.style.position = "fixed"
  backToTopButton.style.bottom = "20px"
  backToTopButton.style.right = "20px"
  backToTopButton.style.width = "50px"
  backToTopButton.style.height = "50px"
  backToTopButton.style.borderRadius = "50%"
  backToTopButton.style.backgroundColor = "#0d6efd" // Bootstrap primary color
  backToTopButton.style.color = "white"
  backToTopButton.style.border = "none"
  backToTopButton.style.fontSize = "24px"
  backToTopButton.style.cursor = "pointer"
  backToTopButton.style.display = "none" // Hidden by default
  backToTopButton.style.zIndex = "1000"
  backToTopButton.style.boxShadow = "0 2px 5px rgba(0,0,0,0.3)"
  backToTopButton.style.transition = "opacity 0.3s, transform 0.3s"

  // Add hover effect
  backToTopButton.addEventListener("mouseover", function () {
    this.style.backgroundColor = "#0b5ed7" // Darker blue on hover
    this.style.transform = "translateY(-3px)"
  })

  backToTopButton.addEventListener("mouseout", function () {
    this.style.backgroundColor = "#0d6efd"
    this.style.transform = "translateY(0)"
  })

  // Append the button to the body
  document.body.appendChild(backToTopButton)

  // Show/hide the button based on scroll position
  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      // Show button after scrolling down 300px
      backToTopButton.style.display = "block"
      backToTopButton.style.opacity = "1"
    } else {
      backToTopButton.style.opacity = "0"
      setTimeout(() => {
        if (window.pageYOffset <= 300) {
          backToTopButton.style.display = "none"
        }
      }, 300)
    }
  })

  // Scroll to top when the button is clicked
  backToTopButton.addEventListener("click", () => {
    window.scrollTo({
      top: 0,
      behavior: "smooth",
    })
  })
})

    </script>
  </body>
</html>