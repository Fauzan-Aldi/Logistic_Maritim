<?php
// You can add PHP logic here if needed
// For example: session management, database connections, etc.

// Example of basic PHP functionality you might want to add:
// session_start();
// include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fasilitas dan Pelayanan - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/css/fasilitas.css" />
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
            <a href="#tentang" class="mobile-dropdown-toggle">
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

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-overlay">
        <div class="hero-content">
          <h1>Fasilitas Dan Pelayanan</h1>
          <p>
            Kami hadir untuk mendukung kelancaran distribusi Anda di seluruh
            Indonesia
          </p>
        </div>
      </div>
    </section>

    <!-- Ports Section -->
    <section class="ports-section">
      <div class="section-container">
        <h2>PELABUHAN</h2>
        <p class="section-subtitle">
          Kami hadir dengan jaringan 10 pelabuhan utama di Indonesia sebagai
          simpul distribusi strategis
        </p>

        <div class="ports-grid">
          <?php
          // Array data pelabuhan
          $ports = [
            ['name' => 'BATAM', 'port' => 'Pelabuhan Batu Ampar Center', 'image' => 'batam.png'],
            ['name' => 'SEMARANG', 'port' => 'Pelabuhan Tanjung Emas', 'image' => 'semarang.png'],
            ['name' => 'JAKARTA', 'port' => 'Pelabuhan Tanjung Priok', 'image' => 'jakarta.png'],
            ['name' => 'ACEH', 'port' => 'Pelabuhan Ulee Lheue', 'image' => 'aceh.png'],
            ['name' => 'TIMIKA', 'port' => 'Pelabuhan Pomako', 'image' => 'timika.png'],
            ['name' => 'MAKASSAR', 'port' => 'Pelabuhan Soekarno Hatta', 'image' => 'makassar.png'],
            ['name' => 'PONTIANAK', 'port' => 'Pelabuhan Dwikora', 'image' => 'pontianak.png'],
            ['name' => 'BALI', 'port' => 'Pelabuhan Benoa', 'image' => 'bali.png']
          ];

          foreach ($ports as $port): ?>
          <div class="port-card">
            <div class="port-image">
              <img src="../logistikmaritim/image/<?php echo $port['image']; ?>" alt="Pelabuhan <?php echo $port['name']; ?>" />
            </div>
            <div class="port-info">
              <div class="port-location">
                <img src="../logistikmaritim/image/lokasi.png" alt="Location" />
                <span><?php echo $port['name']; ?></span>
              </div>
              <p><?php echo $port['port']; ?></p>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Port Services Section -->
    <section class="services-section">
      <div class="section-container">
        <h2>PELAYANAN PELABUHAN</h2>

        <div class="services-grid">
          <?php
          // Array data layanan
          $services = [
            ['name' => 'DERMAGA', 'image' => 'dermaga.png', 'class' => 'service-card'],
            ['name' => 'CRANE', 'image' => 'crane.png', 'class' => 'service-card'],
            ['name' => 'GUDANG', 'image' => 'gudang.png', 'class' => 'service-card'],
            ['name' => 'BEA CUKAI', 'image' => 'beacukai.png', 'class' => 'service-card1'],
            ['name' => 'DISTRIBUSI', 'image' => 'distribusi.png', 'class' => 'service-card']
          ];

          foreach ($services as $service): ?>
          <div class="<?php echo $service['class']; ?>">
            <div class="service-icon">
              <img src="../logistikmaritim/image/<?php echo $service['image']; ?>" alt="<?php echo $service['name']; ?>" />
            </div>
            <h3><?php echo $service['name']; ?></h3>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Ships Section -->
    <section class="ships-section">
      <div class="section-container">
        <h2>KAPAL</h2>
        <p class="section-subtitle">
          Kami didukung oleh armada kapal logistik modern yang telah melayani
          pengiriman antarpulau secara cepat, aman, dan efisien.
        </p>

        <div class="ships-grid">
          <?php
          // Generate ship cards dynamically
          for ($i = 1; $i <= 10; $i++): 
            $shipCode = sprintf("LGMRT-%03d", $i);
            $shipImage = "kapal{$i}.png";
          ?>
          <div class="ship-card">
            <div class="ship-image">
              <img src="../logistikmaritim/image/<?php echo $shipImage; ?>" alt="<?php echo $shipCode; ?>" />
            </div>
            <div class="ship-info">
              <span><?php echo $shipCode; ?></span>
            </div>
          </div>
          <?php endfor; ?>
        </div>
      </div>
    </section>

    <!-- Containers Section -->
    <section class="containers-section">
      <div class="section-container">
        <h2>KONTAINER</h2>
        <p class="section-subtitle">
          Kami menyediakan berbagai jenis kontainer logistik untuk memenuhi
          kebutuhan pengiriman barang Anda
        </p>

        <div class="containers-grid">
          <?php
          // Array data kontainer
          $containers = [
            ['name' => 'Dry Container', 'image' => 'dry.png'],
            ['name' => 'Open Side Container', 'image' => 'open.png'],
            ['name' => 'Reefer Container', 'image' => 'reefer.png'],
            ['name' => 'Ventilated Container', 'image' => 'ventilated.png'],
            ['name' => 'Thermal Container', 'image' => 'thermal.png'],
            ['name' => 'Flat Rack Container', 'image' => 'flat.png']
          ];

          foreach ($containers as $container): ?>
          <div class="container-card">
            <div class="container-image">
              <img src="../logistikmaritim/image/<?php echo $container['image']; ?>" alt="<?php echo $container['name']; ?>" />
            </div>
            <h3><?php echo $container['name']; ?></h3>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Logistics Process Section -->
    <section class="process-section">
      <div class="section-container">
        <h2>PROSES LOGISTIK MARITIM</h2>

        <div class="process-flow">
          <?php
          // Array data proses
          $processes = [
            ['name' => 'PEMESANAN', 'image' => 'pemesanan.png'],
            ['name' => 'BONGKAR MUAT', 'image' => 'bongkar.png'],
            ['name' => 'PENGIRIMAN', 'image' => 'pengiriman.png'],
            ['name' => 'PELACAKAN', 'image' => 'pelacakan.png'],
            ['name' => 'PENGANTARAN', 'image' => 'pengantaran.png']
          ];

          foreach ($processes as $index => $process): ?>
          <div class="process-step">
            <div class="process-icon">
              <img src="../logistikmaritim/image/<?php echo $process['image']; ?>" alt="<?php echo $process['name']; ?>" />
            </div>
            <h3><?php echo $process['name']; ?></h3>
          </div>
          
          <?php if ($index < count($processes) - 1): ?>
          <div class="process-arrow">
            <img src="../logistikmaritim/image/panah.png" alt="Arrow" />
          </div>
          <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- Logistics Network Map Section -->
    <section class="map-section">
      <div class="section-container">
        <h2>PETA JARINGAN LOGISTIK</h2>

        <div class="map-container">
          <iframe
            src="map/mapfasilitas.html"
            width="100%"
            height="520"
            frameborder="0"
          ></iframe>
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
      // Animation-only JavaScript
      document.addEventListener('DOMContentLoaded', function () {
        // Dropdown animation
        const dropdown = document.querySelector('.dropdown');
        const dropdownToggle = document.querySelector('.dropdown-toggle');

        if (dropdown && dropdownToggle) {
          dropdownToggle.addEventListener('click', function (e) {
            e.preventDefault();
            dropdown.classList.toggle('active');
          });

          document.addEventListener('click', function (e) {
            if (!dropdown.contains(e.target)) {
              dropdown.classList.remove('active');
            }
          });
        }

        // Mobile menu animation
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const mobileMenu = document.querySelector('.mobile-menu');

        if (mobileMenuBtn && mobileMenu) {
          mobileMenuBtn.addEventListener('click', function () {
            mobileMenuBtn.classList.toggle('active');
            mobileMenu.classList.toggle('active');
          });
        }

        // Mobile dropdown animation
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

        // Card hover animations
        const cards = document.querySelectorAll(
          '.port-card, .ship-card, .container-card, .service-card, .service-card1, .process-step'
        );
        cards.forEach(card => {
          card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-10px)';
            this.style.transition = 'transform 0.3s ease';
          });

          card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
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
  </body>
</html>