<?php
require_once 'config/database.php';
require_once 'config/session.php';

// Define isLoggedIn function if not already defined
if (!function_exists('isLoggedIn')) {
    function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }
}

// Get user data if logged in
$user = getUserData();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Logistics Maritime</title>
    <link rel="stylesheet" href="logistikmaritim/css/index.css" />
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

      /* Dropdown */
.dropdown {
    position: relative;
}

.dropdown-arrow {
    font-size: 0.8rem;
    transition: transform 0.3s;
}

.dropdown.active .dropdown-arrow {
    transform: rotate(180deg);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: white;
    min-width: 220px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    border-radius: 8px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1001;
    border: 1px solid #e0e0e0;
}

.dropdown.active .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-menu li {
    list-style: none;
}

.dropdown-menu a {
    display: block;
    padding: 0.8rem 1.2rem;
    color: #333;
    text-decoration: none;
    transition: all 0.3s;
    border-bottom: 1px solid #f0f0f0;
    font-size: 0.9rem;
}

.dropdown-menu a:hover {
    background: #f8f9fa;
    color: #0a3356;
    padding-left: 1.5rem;
}

.dropdown-menu li:last-child a {
    border-bottom: none;
    border-radius: 0 0 8px 8px;
}

.dropdown-menu li:first-child a {
    border-radius: 8px 8px 0 0;
}
</style>

<body>
    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <div class="logo-icon">
                    <img src="logistikmaritim/image/logo.png" alt="logo" />
                </div>
                <span>Logistics Maritime</span>
            </div>
            <nav>
                <ul class="nav-menu">
                    <li><a href="index.php">Beranda</a></li>
                    <li class="dropdown">
                        <a href="#tentang" class="dropdown-toggle">
                            Tentang Kami
                            <span class="dropdown-arrow">▼</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="public/tentang.php">Tentang Perusahaan</a></li>
                            <li><a href="public/fasilitas.php">Fasilitas dan Pelayanan</a></li>
                            <li><a href="public/jaminan.php">Jaminan Pelayanan</a></li>
                            <li><a href="public/syarat.php">Syarat dan Ketentuan</a></li>
                            <li><a href="public/informasi.php">Jadwal Pengiriman</a></li>
                            <li><a href="public/karir.php">Karir</a></li>
                            <li><a href="public/tim.php">Tim</a></li>
                        </ul>
                    </li>
                    <li><a href="public/informasi.php">Jadwal Pengiriman</a></li>
                    <li><a href="public/kontak.php">Kontak Kami</a></li>
                </ul>
            </nav>

            <!-- Auth Buttons -->
            <div class="auth-buttons" style="display: flex; flex-direction:row">
                <?php if (isLoggedIn()): ?>
                  <div class="user-menu">
                    <span>Welcome, <?php echo isset($users['name']) ? htmlspecialchars($users['name']) : 'User'; ?></span>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                        <a href="admin/dashboard.php" class="btn-dashboard">Admin Dashboard</a>
                    <?php else: ?>
                        <a href="user/user-dashboard.php" class="btn-dashboard">Dashboard</a>
                    <?php endif; ?>
                    <a href="login/logout.php" class="btn-logout">Logout</a>
                </div>  
                <?php else: ?>
                    <div class="auth-buttons">
                        <a href="login/login.php" class="btn-login">Masuk</a>
                        <a href="login/register.php" class="btn-signup">Daftar</a>
                    </div>
                    
                    
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button class="mobile-menu-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <div class="mobile-menu">
        <ul>
          <li><a href="index.php">Beranda</a></li>
          <li class="mobile-dropdown">
            <a href="#" class="mobile-dropdown-toggle">
              Tentang Kami
              <span class="mobile-dropdown-arrow">▼</span>
            </a>
            <ul class="mobile-dropdown-menu">
            <li><a href="public/tentang.php">Tentang Perusahaan</a></li>
               <li><a href="public/fasilitas.php">Fasilitas dan Pelayanan</a></li>
                <li><a href="public/jaminan.php">Jaminan Pelayanan</a></li>
                <li><a href="public/syarat.php">Syarat dan Ketentuan</a></li>
                <li><a href="public/formasi.php">Jadwal Pengiriman</a></li>
               <li><a href="public/karir.php">Karir</a></li>
                <li><a href="public/tim.php">Tim</a></li>
            </ul>
          </li>
          <li><a href="public/informasi.php">Jadwal Pengiriman</a></li>
                    <li><a href="public/kontak.php">Kontak Kami</a></li>
        </ul>
      </div>
    </header>
        </div>

    </header>

    <!-- Hero Section -->
    <section class="hero" id="beranda">
        <div class="hero-container">
            <div class="hero-content">
                <h1>LOGISTICS MARITIME</h1>
                <p>Mewujudkan Kesuksesan Logistik Anda</p>
                <?php if (isLoggedIn()): ?>
                    <a href="user/user-dashboard.php" class="cta-button">
                        Dashboard
                    </a>
                <?php else: ?>
                    <a href="login/login.php" class="cta-button">
                        Login untuk Cek Pengiriman
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section">
        <div class="content-container">
            <div class="content-item">
                <div class="content-image">
                    <img src="/php-native/logistikmaritim/image/2.png" alt="Port Container" />
                </div>
                <div class="content-text">
                    <h3>SEJARAH</h3>
                    <p>Dari pelabuhan kecil hingga kami menghubungkan seluruh wilayah Indonesia dengan layanan logistik yang handal dan terpercaya. Dengan pengalaman puluhan tahun dalam industri maritim, kami telah membangun jaringan yang luas dan berpengalaman. Kami memastikan setiap pengiriman berjalan lancar, tepat waktu, dan sesuai standar tertinggi. Mulai dari pemulihan di pelabuhan hingga barang tiba di tujuan, sistem manajemen logistik kami menjaga keakuratan, kecepatan, dan keamanan dalam setiap tahap pengiriman barang melalui jalur laut di seluruh Indonesia.</p>
                    <a href="public/tentang.php" class="selengkapnya-btn">Selengkapnya →</a>
                </div>
            </div>

            <div class="content-item">
                <div class="content-image">
                    <img src="logistikmaritim/image/3.png" alt="Logistics Service" />
                </div>
                <div class="content-text">
                    <h3>PELAYANAN</h3>
                    <p>Kami menawarkan pelayanan kapal logistik dengan sistem penyimpanan yang mudah dan fleksibel, didukung oleh teknologi terdepan untuk memastikan efisiensi maksimal. Pengiriman dilakukan dengan tepat waktu, menggunakan kapal yang dilengkapi teknologi dan fasilitas bongkar muat modern. Kami juga menyediakan jaminan klaim yang jelas serta respon cepat terhadap permintaan dan kendala, guna memastikan kelancaran operasional dan kepuasan pelanggan di seluruh wilayah Indonesia.</p>
                    <a href="public/fasilitas.php" class="selengkapnya-btn">Selengkapnya →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Highlight Section -->
    <section class="highlight-section">
        <div class="highlight-container">
            <h2 class="highlight-title">HIGHLIGHT</h2>
            <div class="highlight-grid">
                <div class="highlight-card">
                    <img src="logistikmaritim/image/4.png" alt="Visi Misi" />
                    <h4>Visi & Misi</h4>
                    <p>Menjadi leader logistik maritim terpercaya dengan visi untuk menghubungkan seluruh Indonesia melalui layanan pelayanan.</p>
                    <a href="public/tentang.php" class="selengkapnya-btn1a">Selengkapnya →</a>
                </div>
                <div class="highlight-card">
                    <img src="logistikmaritim/image/5.png" alt="Karir" />
                    <h4>Karir</h4>
                    <p>Bergabung dengan tim profesional kami di bidang logistik maritim. Kami menyediakan kesempatan karir yang menarik dengan lingkungan kerja yang dinamis dan pengembangan profesional.</p>
                    <a href="public/karir.php" class="selengkapnya-btn1b">Selengkapnya →</a>
                </div>
                <div class="highlight-card">
                    <img src="logistikmaritim/image/6.png" alt="Syarat Ketentuan" />
                    <h4>Syarat & Ketentuan</h4>
                    <p>Ketahui syarat dan ketentuan layanan logistik maritim kami untuk memastikan kelancaran dan keamanan dalam setiap transaksi pengiriman barang melalui jalur laut.</p>
                    <a href="public/syarat.php" class="selengkapnya-btn1c">Selengkapnya →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners-section">
        <div class="partners-container">
            <h2 class="partners-title">MITRA</h2>
            <p class="partners-subtitle">Kerja sama yang telah kami lakukan</p>
            <div class="partners-grid">
                <div class="partner-logo">
                    <a href="https://www.asus.com/" target="_blank">
                        <img src="logistikmaritim/image/asus.png" alt="ASUS" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.shopee.com/" target="_blank">
                        <img src="logistikmaritim/image/shoope.png" alt="Shopee" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.komatsu.com/" target="_blank">
                        <img src="logistikmaritim/image/komatsu.png" alt="Komatsu" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.adidas.com/" target="_blank">
                        <img src="logistikmaritim/image/adidas.png" alt="Adidas" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.tokopedia.com/" target="_blank">
                        <img src="logistikmaritim/image/tokopedia.png" alt="Tokopedia" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.bukalapak.com/" target="_blank">
                        <img src="logistikmaritim/image/bukalapak.png" alt="Bukalapak" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.advan.id/" target="_blank">
                        <img src="logistikmaritim/image/advan.png" alt="Advan" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.iphone.com/" target="_blank">
                        <img src="logistikmaritim/image/iphone.png" alt="iPhone" />
                    </a>
                </div>
                <div class="partner-logo">
                    <a href="https://www.yamaha.com/" target="_blank">
                        <img src="logistikmaritim/image/yamaha.png" alt="Yamaha" />
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
              <img src="logistikmaritim/image/logo.png" alt="logo" />
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
            <li><a href="index.php">Beranda</a></li>
            <li><a href="public/tentang.php">Tentang Perusahaan</a></li>
            <li><a href="public/fasilitas.php">Fasilitas dan Pelayanan</a></li>
            <li><a href="public/jaminan.php">Jaminan Pelayanan</a></li>
            <li><a href="public/syarat.php">Syarat dan Ketentuan</a></li>
            <li><a href="public/informasi.php">Jadwal Pengiriman</a></li>
            <li><a href="public/karir.php">Karir</a></li>
            <li><a href="public/tim.php">Tim</a></li>
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
                  <img src="logistikmaritim/image/wa.png" alt="WhatsApp" />
                </a>
                <a href="https://www.instagram.com/logistikmaritime" target="_blank" class="social-icon">
                  <img src="logistikmaritim/image/ig.png" alt="Instagram" />
                </a>
                <a href="https://www.facebook.com/logistikmaritime" target="_blank" class="social-icon">
                  <img src="logistikmaritim/image/fb.png" alt="Facebook" />
                </a>
                <a href="https://www.tiktok.com/@logistikmaritime" target="_blank" class="social-icon">
                  <img src="logistikmaritim/image/tt.png" alt="TikTok" />
                </a>
                <a href="https://twitter.com/logistikmaritime" target="_blank" class="social-icon">
                  <img src="logistikmaritim/image/tw.png" alt="Twitter" />
                </a>
                <a href="https://www.linkedin.com/company/logistikmaritime" target="_blank" class="social-icon">
                  <img src="logistikmaritim/image/ld.png" alt="LinkedIn" />
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

    <script src="/php-native/logistikmaritim/js/script.js"> </script>
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