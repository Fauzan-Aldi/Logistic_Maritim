<?php
// You can add PHP logic here if needed
// For example: session management, database connections, etc.

// Example of PHP variables that could be used
$page_title = "Profil Perusahaan - Logistics Maritime";
$company_name = "Logistics Maritime";
$company_tagline = "Mengirim barang dengan efisien, tepat, dan profesional";
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="../logistikmaritim/css/tentang.css" />
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
              <a href="#tentang" class="dropdown-toggle">
                Tentang Kami
                <span class="dropdown-arrow">▼</span>
              </a>
              <ul class="dropdown-menu">
                <li><a href="tentang.php">Tentang Perusahaan</a></li>
                <li><a href="fasilitas.php">Fasilitas dan Pelayanan</a></li>
                <li><a href="jaminan.php">Jaminan Pelayanan</a></li>
                <li><a href="syarat.php">Syarat dan Ketentuan</a></li>
                <li><a href="informasi.php">jadwal Pengiriman</a></li>
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
        <ul class="mobile-nav-menu">
          <li><a href="../index.php">Beranda</a></li>
          <li class="mobile-dropdown">
            <a href="#tentang" class="mobile-dropdown-toggle">
              Tentang Kami
              <span class="mobile-dropdown-arrow">▼</span>
            </a>
            <ul class="mobile-dropdown-menu">
              <li><a href="tentang.php">Tentang Perusahaan</a></li>
              <li><a href="fasilitas.php">Fasilitas dan Pelayanan</a></li>
              <li><a href="jaminan.php">Jaminan Pelayanan</a></li>
              <li><a href="syarat.php">Syarat dan Ketentuan</a></li>
              <li><a href="informasi.php">jadwal Pengiriman</a></li>
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
          <h1>PROFIL PERUSAHAAN</h1>
          <p>
            Komitmen kami dalam membangun layanan logistik yang andal dan
            profesional
          </p>
        </div>
      </div>
    </section>

    <!-- Company History Section -->
    <section class="history-section">
      <div class="history-container">
        <div class="history-content">
          <h2>Sejarah Perusahaan</h2>
          <p>
            Sejak awal berdiri, kami hadir sebagai penyedia layanan logistik
            maritim yang bertujuan untuk menghubungkan seluruh wilayah kepulauan
            Indonesia. Dengan mengedepankan efisiensi, keamanan, dan ketepatan
            waktu, kami membangun sistem logistik yang andal melalui jaringan
            luas dan dukungan teknologi modern. Komitmen kami menjadi mitra
            terpercaya dalam mendukung distribusi barang, memperlancara rantai
            pasok, dan mendorong pertumbuhan ekonomi nasional melalui
            konektivitas maritim yang kuat.
          </p>
        </div>
        <div class="history-image">
          <img src="../logistikmaritim/image/8.png" alt="Pelabuhan dari Udara" />
        </div>
      </div>
    </section>

    <!-- Vision Mission Section -->
    <section class="vision-mission-section">
      <div class="vision-mission-container">
        <h2>Visi dan Misi Kami</h2>

        <div class="vision-card">
          <div class="vision-icon">
            <img src="../logistikmaritim/image/9.png" alt="Vision Icon" />
          </div>
          <p>
            Memajukan dan mengembangkan perusahaan jasa cargo berbasis logistik
            maritim dengan manajemen risiko yang andal, terkemuka, dan dipercaya
            oleh masyarakat di seluruh Indonesia.
          </p>
        </div>

        <div class="mission-grid">
          <div class="mission-card">
            <div class="mission-icon">
              <img src="../logistikmaritim/image/10.png" alt="Service Icon" />
            </div>
            <p>
              Menyediakan jasa angkutan ke seluruh pelosok tanah air dengan
              mengutamakan kepuasan pelanggan.
            </p>
          </div>

          <div class="mission-card">
            <div class="mission-icon">
              <img src="../logistikmaritim/image/11.png" alt="Trust Icon" />
            </div>
            <p>
              Menjaga kepercayaan pelanggan dengan pelayanan yang amanah,
              profesional, dan berorientasi pada kepuasan pelanggan.
            </p>
          </div>

          <div class="mission-card">
            <div class="mission-icon">
              <img src="../logistikmaritim/image/12.png" alt="Maritime Icon" />
            </div>
            <p>
              Menyediakan layanan logistik maritim yang cepat, aman, dan
              terjangkau melalui jalur laut untuk mendukung distribusi barang
              antar wilayah di Indonesia.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Company Assessment Section -->
    <section class="assessment-section">
      <div class="assessment-container">
        <h2>PENILAIAN PERUSAHAAN</h2>
        <p class="assessment-subtitle">
          Penilaian Perusahaan Di mata Pelanggan
        </p>

        <div class="assessment-grid">
          <div class="assessment-card1">
            <div class="assessment-icon">
              <img src="../logistikmaritim/image/13.png" alt="Security Icon" />
            </div>
            <h3>Keamanan</h3>
            <p>
              Barang dan data dari pelanggan sangatlah berharga. Demi privasi
              pelanggan, data akan kami rahasiakan sebaik mungkin.
            </p>
          </div>

          <div class="assessment-card">
            <div class="assessment-icon">
              <img src="../logistikmaritim/image/14.png" alt="Trust Icon" />
            </div>
            <h3>Amanah</h3>
            <p>
              Amanah merupakan tugas penting dalam perusahaan ini. Tanpa adanya
              amanah membuat kepercayaan pelanggan hilang.
            </p>
          </div>

          <div class="assessment-card2">
            <div class="assessment-icon">
              <img src="../logistikmaritim/image/15.png" alt="Price Icon" />
            </div>
            <h3>Murah</h3>
            <p>
              Layanan yang diberikan tetap berkualitas, meskipun ditawarkan
              dengan harga yang terjangkau. Biaya yang efisien menjadi prioritas
              yang lebih untuk pelanggan kami.
            </p>
          </div>
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
      // Dropdown functionality (keeping this as it's UI animation/interaction)
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

        // Mobile menu functionality (keeping this as it's UI animation/interaction)
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu');

        if (mobileMenuBtn && mobileMenu) {
          mobileMenuBtn.addEventListener('click', function () {
            mobileMenuBtn.classList.toggle('active');
            mobileMenu.classList.toggle('active');
          });
        }

        // Mobile dropdown (keeping this as it's UI animation/interaction)
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

        // Vision and Mission card hover effects (keeping this as it's animation)
        const visionCard = document.querySelector('.vision-card');
        const missionCards = document.querySelectorAll('.mission-card');
        const assessmentCards = document.querySelectorAll(
          '.assessment-card, .assessment-card1, .assessment-card2'
        );

        if (visionCard) {
          visionCard.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
          });

          visionCard.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
          });
        }

        missionCards.forEach(card => {
          card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
          });

          card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
          });
        });

        assessmentCards.forEach(card => {
          card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
          });

          card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
          });
        });

        // History image hover effect (keeping this as it's animation)
        const historyImage = document.querySelector('.history-image img');
        if (historyImage) {
          historyImage.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.05)';
            this.style.transition = 'transform 0.3s ease';
          });

          historyImage.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
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