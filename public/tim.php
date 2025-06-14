<?php
// Tim Profesional - Logistics Maritime PHP Page
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tim Profesional - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/css/tim.css" />
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
        <ul class="mobile-nav-menu">
          <li><a href="../index.php">Beranda</a></li>
          <li class="mobile-dropdown">
            <a href="#" class="mobile-dropdown-toggle">
              Tentang Kami
              <span class="mobile-dropdown-arrow">▼</span>
            </a>
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

    <!-- Team Section -->
    <section class="team-section">
      <div class="team-container">
        <div class="team-header">
          <h1>TIM PROFESIONAL LOGISTICS MARITIME</h1>
          <p>Profesional di balik kesuksesan logistik pelabuhan dan kapal</p>
        </div>

        <div class="team-grid">
  <!-- Team Member 1 -->
<div class="team-card">
  <div class="team-photo">
    <img src="../logistikmaritim/image/albert.png" alt="Albertus Nyam Frandis" />
  </div>
  <div class="team-info">
    <h3>Albertus Nyam Frandis</h3>
    <h4>Head of IT Infrastructure</h4>
    <p>
      Bertanggung jawab atas stabilitas dan keamanan infrastruktur IT
      perusahaan, memastikan sistem berjalan optimal hingga
      pemeliharaan perangkat keras yang menunjang operasional logistik
      dan pelabuhan.
    </p>
    <div class="team-social">
      <a href="https://www.linkedin.com/in/albertusnyam" target="_blank" class="social-link linkedin">
        <img src="../logistikmaritim/image/ld.png" alt="LinkedIn" />
      </a>
      <a href="mailto:albertus.nyam@example.com" class="social-link email">
        <img src="../logistikmaritim/image/maillogo.png" alt="Email" />
      </a>
    </div>
  </div>
</div>

<!-- Team Member 2 -->
<div class="team-card">
  <div class="team-photo">
    <img src="../logistikmaritim/image/aldi.png" alt="Fauzan Aldi" />
  </div>
  <div class="team-info">
    <h3>Fauzan Aldi</h3>
    <h4>Chief Technology Officer (CTO)</h4>
    <p>
      Memimpin transformasi digital dalam perusahaan logistik maritim,
      termasuk pengembangan sistem tracking, dashboard operasional,
      dan infrastruktur teknologi yang mendukung efisiensi distribusi
      barang secara menyeluruh.
    </p>
    <div class="team-social">
      <a href="https://www.linkedin.com/in/fauzan-aldi" target="_blank" class="social-link linkedin">
        <img src="../logistikmaritim/image/ld.png" alt="LinkedIn" />
      </a>
      <a href="mailto:fauzan.aldi@example.com" class="social-link email">
        <img src="../logistikmaritim/image/maillogo.png" alt="Email" />
      </a>
    </div>
  </div>
</div>

<!-- Team Member 3 -->
<div class="team-card">
  <div class="team-photo">
    <img src="../logistikmaritim/image/fadil.png" alt="Fadhillah Nanda Maulana" />
  </div>
  <div class="team-info">
    <h3>Fadhillah Nanda Maulana</h3>
    <h4>System Development Lead</h4>
    <p>
      Memimpin tim pengembangan dan mengelola aplikasi digital yang
      digunakan untuk pelacakan kargo, monitoring armada, dan otomasi
      proses logistik guna meningkatkan efisiensi dan transparansi
      layanan.
    </p>
    <div class="team-social">
      <a href="https://www.linkedin.com/in/fadhillahnanda" target="_blank" class="social-link linkedin">
        <img src="../logistikmaritim/image/ld.png" alt="LinkedIn" />
      </a>
      <a href="mailto:fadhillah.nanda@example.com" class="social-link email">
        <img src="../logistikmaritim/image/maillogo.png" alt="Email" />
      </a>
    </div>
  </div>
</div>

<!-- Team Member 4 -->
<div class="team-card">
  <div class="team-photo">
    <img src="../logistikmaritim/image/roy.png" alt="Roy Adiyta" />
  </div>
  <div class="team-info">
    <h3>Roy Adiyta</h3>
    <h4>Data & Integration Specialist</h4>
    <p>
      Fokus pada integrasi data sistem logistik, pelabuhan, dan kapal,
      serta mengelola data operasional untuk menghasilkan analisa yang
      mendukung pengambilan keputusan dan optimalisasi rute
      pengiriman.
    </p>
    <div class="team-social">
      <a href="https://www.linkedin.com/in/royadiyta" target="_blank" class="social-link linkedin">
        <img src="../logistikmaritim/image/ld.png" alt="LinkedIn" />
      </a>
      <a href="mailto:roy.adiyta@example.com" class="social-link email">
        <img src="../logistikmaritim/image/maillogo.png" alt="Email" />
      </a>
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

        // Team card hover effects (keeping this as it's animation)
        const teamCards = document.querySelectorAll('.team-card');
        teamCards.forEach(card => {
          card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px)';
            this.style.transition = 'transform 0.3s ease';
          });

          card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
          });
        });

        // Social link hover effects (keeping this as it's animation)
        const socialLinks = document.querySelectorAll('.social-link');
        socialLinks.forEach(link => {
          link.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.2)';
            this.style.transition = 'transform 0.3s ease';
          });

          link.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
          });
        });
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