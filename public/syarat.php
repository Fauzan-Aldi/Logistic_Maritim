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
    <title>Syarat dan Ketentuan - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/css/syarat.css" />
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

    <!-- Main Content -->
    <main class="main-content">
      <div class="content-container">
        <h1>SYARAT DAN KETENTUAN</h1>

        <div class="intro-text">
          <p>
            Syarat dan Ketentuan yang ditetapkan di bawah ini mengatur pemakaian
            jasa yang ditawarkan oleh PT Logistic Maritim (LOGMAR) atas
            penggunaan Website LOGMAR, maka pengguna dianggap telah membaca,
            memahami dan menyetujui isi dalam Syarat dan Ketentuan ini;
          </p>
        </div>

        <!-- Ketentuan Umum Section -->
        <section class="terms-section">
          <h2>Ketentuan Umum:</h2>
          <ol>
            <li>
              Logistic Maritim (LOGMAR) hanya akan mengangkut dokumen atau
              barang pada syarat dan ketentuan yang berlaku di LOGMAR.
            </li>
            <li>
              MT LOGISTIK berhak untuk menolak atau menerima dokumen
              transportasi atau item tertentu dari pengirim, sesuai dengan
              ketentuan yang ada di MT LOGISTIK.
            </li>
            <li>
              MT LOGISTIK berhak membawa dokumen atau barang milik pengirim
              menggunakan penanganan, penyimpanan, dan transportasi yang cocok
              sesuai kebijakan MT LOGISTIK.
            </li>
            <li>
              Dokumen atau barang kemasan pengirim adalah tanggung jawab
              pengirim.
            </li>
            <li>
              MT LOGISTIK tidak bertanggung jawab atas kehilangan atau kerusakan
              dokumen atau ketidaksempurnaan kemasan barang yang disebabkan oleh
              pengirim.
            </li>
            <li>
              Pengirim bertanggung jawab untuk menyertakan nama dan alamat
              lengkap tujuan pengiriman, jenis atau isi dokumen atau barang
              kiriman sehingga pengiriman bisa dilakukan dengan benar.
            </li>
            <li>
              MT LOGISTIK tidak bertanggung jawab atas keterlambatan, kerugian,
              kerusakan dan biaya yang timbul dari kelalaian dan kesalahan
              pengirim dalam memenuhi kewajiban-kewajiban di atas.
            </li>
          </ol>
        </section>

        <!-- Larangan Pengiriman Section -->
        <section class="terms-section">
          <h2>Larangan pengiriman:</h2>
          <ol>
            <li>
              MT LOGISTIK tidak menerima barang berbahaya peledak atau terbakar,
              obat, emas dan perak, koin, abu, sianida, platinum dan batu atau
              logam mulia dan perangko, barang curian, periksa tunai, wesel,
              atau cek perjalanan, surat, barang antik, lukisan antik, benda
              yang mengandung gas hewan atau tanaman hidup serta benda cair.
            </li>
            <li>
              Jika pengirim mengirimkan barang-barang tersebut tanpa
              sepengetahuan MT LOGISTIK, MT LOGISTIK dibebaskan dari segala
              tuntutan pengirim dan biaya dan tuntutan tersebut menjadi tanggung
              jawab pengirim.
            </li>
            <li>
              MT LOGISTIK berhak untuk mengambil semua langkah yang diperlukan
              sesegera MT LOGISTIK mengetahui adanya pelanggaran kondisi ini,
              termasuk untuk menjalankan hak-hak yang diatur dalam undang-undang
              perlindungan konsumen.
            </li>
          </ol>
        </section>

        <!-- Inspeksi Section -->
        <section class="terms-section">
          <h2>Inspeksi:</h2>
          <ol>
            <li>
              MT LOGISTIK berhak tetapi tidak berkewajiban untuk memeriksa
              barang atau dokumen yang dikirim oleh pengirim untuk memastikan
              bahwa barang atau dokumen atau barang benar-benar memenuhi syarat
              untuk diangkut ke alamat tujuan pada ketentuan prosedur operasi
              standar dan metode penanganan MT LOGISTIK.
            </li>
            <li>
              MT LOGISTIK dalam melaksanakan hak-hak mereka tidak menjamin atau
              menyatakan bahwa seluruh pengiriman layak untuk transportasi.
            </li>
            <li>
              MT LOGISTIK tidak bertanggung jawab atas isi dan kiriman yang
              tidak cocok dengan deskripsi yang diberikan pengirim ke MT
              LOGISTIK.
            </li>
          </ol>
        </section>

        <!-- Menjamin Pengiriman Section -->
        <section class="terms-section">
          <h2>Menjamin pengiriman kepemilikan:</h2>
          <ol>
            <li>
              Pengirim dengan ini menjamin bahwa yang bersangkutan adalah
              pemilik yang sah dan berhak atas penyerahan dokumen atau barang
              yang diserahkan oleh MT LOGISTIK dan telah berhak untuk terikat
              dengan syarat dan kondisi.
            </li>
            <li>
              Pengirim menyatakan MT LOGISTIK dibebaskan dari tuntutan pihak
              mana pun dan dari semua biaya dan kerusakan atau biaya lainnya
              dalam hal pelanggaran jaminan ini.
            </li>
            <li>
              MT LOGISTIK hanya bertanggung jawab untuk membayar kerugian yang
              diderita sebagai akibat dari kerusakan atau kerugian pengiriman
              dokumen atau barang yang dikirim oleh MT LOGISTIK, dengan catatan
              hanya jika MT LOGISTIK dengan catatan hanya jika kerusakan
              tersebut semata-mata karena kelalaian MT LOGISTIK.
            </li>
            <li>
              MT LOGISTIK tidak bertanggung jawab atas kerusakan tidak langsung
              atau konsekuensial, termasuk dan tidak terbatas pada kerugian
              komersial, kerugian tidak langsung atau kerugian lainnya termasuk
              kerugian yang terjadi dalam transportasi atau pengiriman yang
              disebabkan oleh hal-hal yang berada di luar kemampuan kontrol MT
              LOGISTIK atau kompensasi untuk kerusakan yang disebabkan oleh
              bencana alam atau force majeure.
            </li>
            <li>
              Nilai ganti rugi sesuai dengan syarat dan ketentuan MT LOGISTIK.
              Dalam menentukan nilai akuntabilitas MT LOGISTIK mempertimbangkan
              nilai dari dokumen atau penggantian barang pada waktu dan tempat
              pengiriman, tanpa menghubungkan dengan nilai komersial dan
              konsekuensi kerugian.
            </li>
          </ol>
        </section>

        <!-- Prosedur Klaim Section -->
        <section class="terms-section">
          <h2>Prosedur untuk klaim:</h2>
          <p>
            Setiap klaim pengirim sehubungan dengan kewajiban dan tanggung jawab
            MT LOGISTIK harus disampaikan secara tertulis dan diterima oleh
            kantor MT LOGISTIK paling lambat 10 (sepuluh) hari setelah tanggal
            dimana seharusnya dokumen atau barang sudah sampai.
          </p>
        </section>

        <?php
        // You can add dynamic content here if needed
        // For example, display current date for terms last updated
        /*
        <div class="last-updated">
          <p><em>Terakhir diperbarui: <?php echo date('d F Y'); ?></em></p>
        </div>
        */
        ?>
      </div>
    </main>

    <!-- Footer -->
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

        // Terms section hover effects (keeping this as it's animation)
        const termsSections = document.querySelectorAll('.terms-section');
        termsSections.forEach(section => {
          section.addEventListener('mouseenter', function () {
            this.style.transform = 'translateX(5px)';
            this.style.transition = 'transform 0.3s ease';
          });

          section.addEventListener('mouseleave', function () {
            this.style.transform = 'translateX(0)';
          });
        });

        // Smooth scrolling for anchor links (keeping this as it's animation)
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
          link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
              targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start',
              });
            }
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