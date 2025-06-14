<?php
require_once '../config/database.php';

// Inisialisasi koneksi database
$pdo = Database::getInstance()->getConnection();

// Inisialisasi variabel untuk form
$nama = $email = $phone = $pesan = "";
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
    
    // Remove any non-numeric characters for validation
    $phone_numbers_only = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if phone number length is between 12-13 digits
    if (strlen($phone_numbers_only) < 12) {
        $errors[] = "Nomor handphone minimal 12 digit";
    } elseif (strlen($phone_numbers_only) > 13) {
        $errors[] = "Nomor handphone maksimal 13 digit";
    }
    
    // Optional: Check if it starts with valid Indonesian phone prefixes
    if (!preg_match('/^(08|628|\+628)/', $phone)) {
        $errors[] = "Format nomor handphone tidak valid (gunakan format: 08xxx atau +628xxx)";
    }
}
    
    if (empty($_POST["pesan"])) {
        $errors[] = "Pesan harus diisi";
    } else {
        $pesan = htmlspecialchars(trim($_POST["pesan"]));
    }
    
    // Jika tidak ada error, proses data
    if (empty($errors)) {
        try {
            // Simpan data ke database
            $stmt = $pdo->prepare("
                INSERT INTO general_messages (nama, email, phone, pesan, status, created_at) 
                VALUES (?, ?, ?, ?, 'new', NOW())
            ");
            
            if ($stmt->execute([$nama, $email, $phone, $pesan])) {
                $success_message = "Pesan Anda berhasil dikirim! Kami akan menghubungi Anda segera.";
                
                // Reset form values
                $nama = $email = $phone = $pesan = "";
            } else {
                $errors[] = "Terjadi kesalahan saat menyimpan data";
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
  <title>Kontak Kami - Logistics Maritime</title>
  <link rel="stylesheet" href="../logistikmaritim/css/kontak.css" />
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

/* Alert styles */
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
</style>

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
              <li><a href="informasi.php">Informasi Pengiriman</a></li>
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
      <ul class="mobile-nav-dmenu">
        <li><a href="index.php">Beranda</a></li>
        <li class="mobile-ropdown">
          <a href="#tentang" class="mobile-dropdown-toggle">Tentang Kami <span class="mobile-dropdown-arrow">▼</span></a>
          <ul class="mobile-dropdown-menu">
            <li><a href="tentang.php">Tentang Perusahaan</a></li>
            <li><a href="fasilitas.php">Fasilitas dan Pelayanan</a></li>
            <li><a href="jaminan.php">Jaminan Pelayanan</a></li>
            <li><a href="syarat.php">Syarat dan Ketentuan</a></li>
            <li><a href="informasi.php">Informasi Pengiriman</a></li>
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
        <h1>Kontak Kami</h1>
        <p>Hubungi kami, kami siap membantu Anda kapan saja</p>
      </div>
    </div>
  </section>

  <!-- Contact Section -->
  <section class="contact-section">
    <div class="contact-container">
      <div class="contact-form-container">
        <h2>Hubungi Kami</h2>
        <p>
          Kami sebaik mungkin melayani anda. Jika ada kritik dan saran seputar
          pelayanan kami, kami harap anda mengirimkan keluhan anda pada kami
          melalui form dibawah ini :
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

        <form class="contact-form" method="POST" action="">
          <div class="form-group">
            <label for="nama">Nama Lengkap:</label>
            <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($nama); ?>" required />
          </div>
          <div class="form-group">
            <label for="phone">No Handphone:</label>
            <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required />
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required />
          </div>
          <div class="form-group">
            <label for="pesan">Pesan:</label>
            <textarea id="pesan" name="pesan" rows="6" required><?php echo htmlspecialchars($pesan); ?></textarea>
          </div>
          <button type="submit" class="submit-btn">Kirim</button>
        </form>
      </div>

      <div class="contact-map-container">
        <div class="map-container">
          <iframe src="map/mapkontak.html" width="100%" height="350" frameborder="0"></iframe>
        </div>
        <div class="address">
          <h3>Kantor Pusat</h3>
          <p>
            Kepulauan Riau, Tanjung Pinang, Jalan Pantai Indah, Gang Pantai Indah NO. 72, Kode Pos 29113
          </p>
          <button class="location-btn" onclick="window.open('https://maps.google.com/?q=Jalan+Pantai+Indah+Gang+Pantai+Indah+72+Tanjung+Pinang', '_blank')">
            Cek Lokasi
          </button>
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
            <li><a href="informasi.php">Informasi Pengiriman</a></li>
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
            <a href="#" class="social-icon">
              <img src="../logistikmaritim/image/wa.png" alt="WhatsApp" />
            </a>
            <a href="#" class="social-icon">
              <img src="../logistikmaritim/image/ig.png" alt="Instagram" />
            </a>
            <a href="#" class="social-icon">
              <img src="../logistikmaritim/image/fb.png" alt="Facebook" />
            </a>
            <a href="#" class="social-icon">
              <img src="../logistikmaritim/image/tt.png" alt="TikTok" />
            </a>
            <a href="#" class="social-icon">
              <img src="../logistikmaritim/image/tw.png" alt="Twitter" />
            </a>
            <a href="#" class="social-icon">
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

  <!-- JavaScript & Style -->
  <script src="../js/kontak.js"></script>
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
        
        // Clear success message after 5 seconds
        const successAlert = document.querySelector('.alert-success');
        if (successAlert) {
          setTimeout(function() {
            successAlert.style.opacity = '0';
            successAlert.style.transition = 'opacity 0.3s ease';
            setTimeout(function() {
              successAlert.style.display = 'none';
            }, 300);
          }, 5000);
        }
      });
    
// Phone number validation
const phoneInput = document.getElementById('phone');
if (phoneInput) {
    phoneInput.addEventListener('input', function() {
        const phone = this.value.replace(/[^0-9]/g, '');
        const phoneLength = phone.length;
        
        // Remove existing validation messages
        const existingError = this.parentNode.querySelector('.phone-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Add validation message
        if (phoneLength > 0 && (phoneLength < 12 || phoneLength > 13)) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'phone-error';
            errorDiv.style.color = '#dc3545';
            errorDiv.style.fontSize = '0.875rem';
            errorDiv.style.marginTop = '5px';
            
            if (phoneLength < 12) {
                errorDiv.textContent = 'Nomor handphone minimal 12 digit';
            } else {
                errorDiv.textContent = 'Nomor handphone maksimal 13 digit';
            }
            
            this.parentNode.appendChild(errorDiv);
        }
    });
}
    </script>
  <style>
    <?php include 'logistikmaritim/css/kontak.css'; ?>
  </style>
</body>

</html>
