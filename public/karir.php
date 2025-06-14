<?php
// Mulai session jika diperlukan
session_start();

// Konfigurasi halaman
$page_title = "Peluang Karir - Logistics Maritime";
$current_page = "karir";

// Data pekerjaan yang tersedia
$job_positions = [
    [
        'id' => 1,
        'title' => 'Manajer Logistik Maritim',
        'description' => 'Mengawasi operasional pengiriman barang melalui laut dari pelabuhan tepat waktu.',
        'image' => '../logistikmaritim/image/29.png'
    ],
    [
        'id' => 2,
        'title' => 'Operator Crane Pelabuhan',
        'description' => 'Mengoperasikan crane untuk bongkar muat kontainer di pelabuhan.',
        'image' => '../logistikmaritim/image/30.png'
    ],
    [
        'id' => 3,
        'title' => 'Supply Chain Analyst',
        'description' => 'Menganalisis dan mengoptimalkan alur distribusi barang dalam rantai pasok maritim.',
        'image' => '../logistikmaritim/image/31.png'
    ],
    [
        'id' => 4,
        'title' => 'Marketing Staff',
        'description' => 'Mempromosikan layanan logistik maritim dan mencari klien baru.',
        'image' => '../logistikmaritim/image/32.png'
    ],
    [
        'id' => 5,
        'title' => 'Dokumentasi Logistik',
        'description' => 'Mengurus dokumen pengiriman seperti Bill of Lading dan bea cukai.',
        'image' => '../logistikmaritim/image/33.png'
    ],
    [
        'id' => 6,
        'title' => 'Perencana Rute Kapal',
        'description' => 'Menentukan rute terbaik kapal untuk efisiensi waktu dan biaya.',
        'image' => '../logistikmaritim/image/34.png'
    ]
];

// Data keuntungan berkarir
$benefits = [
    [
        'icon' => '../logistikmaritim/image/25.png',
        'title' => 'Bagian dari Logistik Maritim',
        'description' => 'LOGMAR merupakan bagian dari Mitra Bahari Indonesia yang mengimplementasikan nilai-nilai integritas dan profesionalisme dalam setiap aspek pekerjaan.'
    ],
    [
        'icon' => '../logistikmaritim/image/26.png',
        'title' => 'Pengembangan Diri',
        'description' => 'LOGMAR dapat menjadi wadah untuk terus bertumbuh dan mengembangkan potensi diri setiap karyawan untuk mengembangkan minat dan bakat keterampilan.'
    ],
    [
        'icon' => '../logistikmaritim/image/27.png',
        'title' => 'Remunerasi yang Kompetitif',
        'description' => 'LOGMAR menyediakan kompensasi dan benefit yang menarik sesuai dengan apresiasi terhadap performa kerja karyawan.'
    ],
    [
        'icon' => '../logistikmaritim/image/28.png',
        'title' => 'Lingkungan Kerja Kolaboratif',
        'description' => 'Bekerja di lingkungan yang mendorong kolaborasi, saling mendukung, serta memberikan inspirasi untuk mencapai tujuan bersama.'
    ]
];

// Informasi kontak perusahaan
$company_info = [
    'name' => 'Logistics Maritime',
    'tagline' => 'Mengirim barang dengan efisien, tepat, dan profesional',
    'address' => 'Jalan Pelabuhan Gang Pantai Indah No 72',
    'phone' => '+6212-3456-7810',
    'email' => 'logisticmaritime@gmail.com'
];

// Menu navigasi
$nav_menu = [
    ['url' => '../index.php', 'text' => 'Beranda'],
    ['url' => 'informasi.php', 'text' => 'Jadwal Pengiriman'],
    ['url' => 'kontak.php', 'text' => 'Kontak Kami']
];

$social_media = [
    ['url' => '#', 'icon' => '../logistikmaritim/image/wa.png', 'alt' => 'WhatsApp'],
    ['url' => '#', 'icon' => '../logistikmaritim/image/ig.png', 'alt' => 'Instagram'],
    ['url' => '#', 'icon' => '../logistikmaritim/image/fb.png', 'alt' => 'Facebook'],
    ['url' => '#', 'icon' => '../logistikmaritim/image/tt.png', 'alt' => 'TikTok'],
    ['url' => '#', 'icon' => '../logistikmaritim/image/tw.png', 'alt' => 'Twitter'],
    ['url' => '#', 'icon' => '../logistikmaritim/image/ld.png', 'alt' => 'LinkedIn']
];

// Function untuk cek halaman aktif
function isActivePage($page, $current) {
    return $page === $current ? 'active' : '';
}

// Function untuk generate safe HTML
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo e($page_title); ?></title>
    <link rel="stylesheet" href="../logistikmaritim/css/karir.css" />
  </head>
  <body>
  <!-- Header -->
  <header class="header">
    <div class="nav-container">
      <div class="logo">
        <div class="logo-icon">
          <img src="../logistikmaritim/image/logo.png" alt="logo" />
        </div>
        <span>Logistics Maritime</span>
      </div>
      <nav>
        <ul class="nav-menu">
          <li><a href="../index.php">Beranda</a></li>
          <li class="dropdown">
            <a href="#tentang" class="dropdown-toggle">Tentang Kami <span class="dropdown-arrow">▼</span></a>
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

      <div class="mobile-menu-btn"><span></span><span></span><span></span></div>
    </div>

    <div class="mobile-menu">
      <ul class="mobile-nav-menu">
        <li><a href="index.php">Beranda</a></li>
        <li class="mobile-dropdown">
          <a href="#tentang" class="mobile-dropdown-toggle">Tentang Kami <span class="mobile-dropdown-arrow">▼</span></a>
          <ul class="mobile-dropdown-menu">
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
        <li class="mobile-auth-buttons">
          <a href="login.php" class="btn-login-mobile">Masuk</a>
          <a href="register.php" class="btn-signup-mobile">Daftar</a>
        </li>
      </ul>
    </div>
  </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-overlay">
        <div class="hero-content">
          <h1>Peluang Karir Bersama Kami</h1>
          <p>
            Kami membuka kesempatan untuk bertumbuh dan belajar dengan
            lingkungan dinamis dan saling mendukung
          </p>
        </div>
      </div>
    </section>

    <!-- Why Work With Us Section -->
    <section class="benefits-section">
      <div class="benefits-container">
        <h2>MENGAPA BERKARIR BERSAMA KAMI?</h2>

        <div class="benefits-grid">
          <?php foreach ($benefits as $benefit): ?>
            <div class="benefit-card">
              <div class="benefit-icon">
                <img src="<?php echo e($benefit['icon']); ?>" alt="<?php echo e($benefit['title']); ?>" />
              </div>
              <h3><?php echo e($benefit['title']); ?></h3>
              <p><?php echo e($benefit['description']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Job Listings Section -->
    <section class="jobs-section">
      <div class="jobs-container">
        <h2>LOWONGAN KERJA</h2>
        <p class="jobs-subtitle">
          Berikut adalah posisi tenaga kerja yang dibutuhkan untuk memperkuat
          jaringan logistik maritim dan mendukung kelancaran operasional
          pengiriman barang.
        </p>

        <div class="jobs-grid">
          <?php foreach ($job_positions as $job): ?>
            <div class="job-card" data-job-id="<?php echo e($job['id']); ?>">
              <div class="job-image">
                <img src="<?php echo e($job['image']); ?>" alt="<?php echo e($job['title']); ?>" />
              </div>
              <div class="job-content">
                <h3><?php echo e($job['title']); ?></h3>
                <p><?php echo e($job['description']); ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="apply-section">
          <a href="form.php" class="apply-btn"> Lamar Sekarang </a>
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

        // Benefit cards hover effects
        const benefitCards = document.querySelectorAll('.benefit-card');
        benefitCards.forEach(card => {
          card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px)';
            this.style.transition = 'transform 0.3s ease';
          });

          card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
          });
        });

        // Job cards hover effects
        const jobCards = document.querySelectorAll('.job-card');
        jobCards.forEach(card => {
          card.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
          });

          card.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
          });

          // Optional: Add click functionality for job cards
          card.addEventListener('click', function () {
            const jobId = this.getAttribute('data-job-id');
            console.log('Clicked job ID:', jobId);
            // You can add navigation to job detail page here
            // window.location.href = `job-detail.php?id=${jobId}`;
          });
        });

        // Apply button hover effect
        const applyBtn = document.querySelector('.apply-btn');
        if (applyBtn) {
          applyBtn.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-3px)';
            this.style.transition = 'transform 0.3s ease';
          });

          applyBtn.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
          });
        }
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

    <style>
      /* Auth Buttons Styles */
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

      /* Mobile Menu Styles */
      .mobile-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
      }

      .mobile-menu.active {
        display: block;
      }

      .mobile-nav-menu {
        list-style: none;
        padding: 0;
        margin: 0;
      }

      .mobile-nav-menu li {
        border-bottom: 1px solid #eee;
      }

      .mobile-nav-menu a {
        display: block;
        padding: 15px 20px;
        text-decoration: none;
        color: #333;
      }

      .mobile-dropdown-menu {
        background: #f8f9fa;
        display: none;
      }

      .mobile-dropdown.active .mobile-dropdown-menu {
        display: block;
      }

      .mobile-dropdown-menu a {
        padding-left: 40px;
      }

      /* Active page indicator */
      .nav-menu a.active,
      .dropdown-menu a.active {
        color: #2c5aa0;
        font-weight: bold;
      }

      /* Job card cursor pointer */
      .job-card {
        cursor: pointer;
      }

      /* Responsive */
      @media (max-width: 768px) {
        .auth-buttons {
          display: none;
        }

        .nav-menu {
          display: none;
        }

        .mobile-menu-btn {
          display: block;
        }
      }

      @media (min-width: 769px) {
        .mobile-menu-btn {
          display: none;
        }

        .mobile-menu {
          display: none !important;
        }
      }
    </style>
  </body>
</html>