<?php
require_once '../config/database.php';

// Inisialisasi koneksi database
$pdo = Database::getInstance()->getConnection();

// Inisialisasi variabel untuk form
$nama = $email = $phone = $position = "";
$errors = array();
$success_message = "";

// Proses form ketika disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validasi input
    if (empty($_POST["nama"])) {
        $errors[] = "Nama lengkap harus diisi";
    } else {
        $nama = htmlspecialchars(trim($_POST["nama"]));
    }
    
    if (empty($_POST["email"])) {
        $errors[] = "Email harus diisi";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid";
    } else {
        $email = htmlspecialchars(trim($_POST["email"]));
    }
    
    if (empty($_POST["phone"])) {
        $errors[] = "Nomor handphone harus diisi";
    } else {
        $phone = htmlspecialchars(trim($_POST["phone"]));
    }
    
    if (empty($_POST["position"])) {
        $errors[] = "Posisi pekerjaan harus dipilih";
    } else {
        $position = htmlspecialchars(trim($_POST["position"]));
    }
    
    // Validasi file upload
    $resume_filename = null;
    if (empty($_FILES["resume"]["name"])) {
        $errors[] = "File resume harus diupload";
    } else {
        $allowed_types = array('pdf', 'doc', 'docx');
        $file_extension = strtolower(pathinfo($_FILES["resume"]["name"], PATHINFO_EXTENSION));
        
        if (!in_array($file_extension, $allowed_types)) {
            $errors[] = "File harus berformat PDF, DOC, atau DOCX";
        }
        
        if ($_FILES["resume"]["size"] > 5000000) { // 5MB limit
            $errors[] = "Ukuran file maksimal 5MB";
        }
    }
    
    // Jika tidak ada error, proses data
    if (empty($errors)) {
        try {
            // Upload file resume
            $upload_dir = "uploads/resumes/";
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_name = time() . "_" . basename($_FILES["resume"]["name"]);
            $target_file = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
                $resume_filename = $file_name;
                
                // Simpan data ke database
                $stmt = $pdo->prepare("
                    INSERT INTO contact_messages (nama, email, phone, position, resume_filename, status, created_at) 
                    VALUES (?, ?, ?, ?, ?, 'new', NOW())
                ");
                
                if ($stmt->execute([$nama, $email, $phone, $position, $resume_filename])) {
                    $success_message = "Lamaran Anda berhasil dikirim! Kami akan menghubungi Anda segera.";
                    
                    // Reset form values
                    $nama = $email = $phone = $position = "";
                } else {
                    $errors[] = "Terjadi kesalahan saat menyimpan data";
                    // Hapus file yang sudah diupload jika gagal simpan ke database
                    if (file_exists($target_file)) {
                        unlink($target_file);
                    }
                }
            } else {
                $errors[] = "Terjadi kesalahan saat mengupload file";
            }
        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Formulir Lamaran - Logistics Maritime</title>
    <link rel="stylesheet" href="../logistikmaritim/css/form.css" />
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

      /* Error and Success Messages */
      .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: 500;
      }

      .alert-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
      }

      .alert-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
      }

      .error-list {
        margin: 0;
        padding-left: 20px;
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
        <ul class="mobile-nav-menu">
          <li><a href="index.php">Beranda</a></li>
          <li class="mobile-dropdown">
            <a href="#tentang" class="mobile-dropdown-toggle">
              Tentang Kami
              <span class="mobile-dropdown-arrow">▼</span>
            </a>
            <ul class="mobile-dropdown-menu">
              <li><a href="tentang.html">Tentang Perusahaan</a></li>
              <li><a href="fasilitas.html">Fasilitas dan Pelayanan</a></li>
              <li><a href="jaminan.html">Jaminan Pelayanan</a></li>
              <li><a href="syarat.html">Syarat dan Ketentuan</a></li>
              <li><a href="informasi.html">Jadwal Pengiriman</a></li>
              <li><a href="karir.html">Karir</a></li>
              <li><a href="tim.html">Tim</a></li>
            </ul>
          </li>
          <li><a href="informasi.html">Jadwal Pengiriman</a></li>
          <li><a href="kontak.html">Kontak Kami</a></li>
          <li class="mobile-auth-buttons">
            <a href="login/login.php" class="btn-login-mobile">Masuk</a>
            <a href="login/register.php" class="btn-signup-mobile">Daftar</a>
          </li>
        </ul>
      </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-overlay">
        <div class="hero-content">
          <h1>Bergabunglah Bersama Kami</h1>
          <p>Temukan peluang dan tumbuh bersama kami di industri logistik</p>
        </div>
      </div>
    </section>

    <!-- Application Form Section -->
    <section class="application-section">
      <div class="application-container">
        <div class="form-wrapper">
          <h2>Formulir Lamaran Kerja</h2>
          <p>
            Silahkan lengkapi formulir dibawah ini untuk melamar posisi yang
            tersedia.
          </p>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
              <strong>Terjadi kesalahan:</strong>
              <ul class="error-list">
                <?php foreach ($errors as $error): ?>
                  <li><?php echo $error; ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (!empty($success_message)): ?>
            <div class="alert alert-success">
              <?php echo $success_message; ?>
            </div>
          <?php endif; ?>

          <form class="application-form" method="POST" enctype="multipart/form-data">
            <div class="form-group">
              <label for="nama">Nama Lengkap:</label>
              <input
                type="text"
                id="nama"
                name="nama"
                value="<?php echo htmlspecialchars($nama); ?>"
                placeholder="Masukkan Nama Lengkap Anda"
                required
              />
            </div>

            <div class="form-group">
              <label for="email">Email:</label>
              <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($email); ?>"
                placeholder="contoh@email.com"
                required
              />
            </div>

            <div class="form-group">
              <label for="phone">Nomor Handphone:</label>
              <input
                type="tel"
                id="phone"
                name="phone"
                value="<?php echo htmlspecialchars($phone); ?>"
                placeholder="08XXXXXXXXX"
                required
              />
            </div>

            <div class="form-group">
              <label for="position">Pekerjaan yang dilamar:</label>
              <div class="select-wrapper">
                <select id="position" name="position" required>
                  <option value="" disabled <?php echo empty($position) ? 'selected' : ''; ?>>Pilih Posisi</option>
                  <option value="manajer-logistik" <?php echo ($position == 'manajer-logistik') ? 'selected' : ''; ?>>
                    Manajer Logistik Maritim
                  </option>
                  <option value="operator-crane" <?php echo ($position == 'operator-crane') ? 'selected' : ''; ?>>
                    Operator Crane Pelabuhan
                  </option>
                  <option value="supply-chain" <?php echo ($position == 'supply-chain') ? 'selected' : ''; ?>>
                    Supply Chain Analyst
                  </option>
                  <option value="marketing" <?php echo ($position == 'marketing') ? 'selected' : ''; ?>>
                    Marketing Staff
                  </option>
                  <option value="dokumentasi" <?php echo ($position == 'dokumentasi') ? 'selected' : ''; ?>>
                    Dokumentasi Logistik
                  </option>
                  <option value="perencana-rute" <?php echo ($position == 'perencana-rute') ? 'selected' : ''; ?>>
                    Perencana Rute Kapal
                  </option>
                </select>
                <div class="select-arrow">▼</div>
              </div>
            </div>

            <div class="form-group">
              <label for="resume">Resume:</label>
              <div class="file-upload">
                <input
                  type="file"
                  id="resume"
                  name="resume"
                  accept=".pdf,.doc,.docx"
                  required
                />
                <label for="resume" class="file-label">
                  <span class="file-text">Klik atau seret CV Anda di sini</span>
                </label>
              </div>
            </div>

            <button type="submit" class="submit-btn">Lamar Sekarang</button>
          </form>
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
            </div>>

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

        // Form input focus animations
        const formInputs = document.querySelectorAll('input, select');
        formInputs.forEach(input => {
          input.addEventListener('focus', function () {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
          });

          input.addEventListener('blur', function () {
            this.style.transform = 'scale(1)';
          });
        });

        // Submit button hover animation
        const submitBtn = document.querySelector('.submit-btn');
        if (submitBtn) {
          submitBtn.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-3px)';
            this.style.transition = 'transform 0.3s ease';
          });

          submitBtn.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
          });
        }

        // File upload area hover animation
        const fileLabel = document.querySelector('.file-label');
        if (fileLabel) {
          fileLabel.addEventListener('mouseenter', function () {
            this.style.transform = 'scale(1.02)';
            this.style.transition = 'transform 0.3s ease';
          });

          fileLabel.addEventListener('mouseleave', function () {
            this.style.transform = 'scale(1)';
          });
        }

        // Clear success message after 5 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
          setTimeout(function() {
            successAlert.style.opacity = '0';
            setTimeout(function() {
              successAlert.style.display = 'none';
            }, 300);
          }, 5000);
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
