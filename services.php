<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services | Barangay Pasong Buaya II</title>
  <link rel="stylesheet" href="style.css">
</head>
<body class="fade-in">
  <header>
    <h1>Barangay Pasong Buaya II</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="services.php" class="active">Services</a>
      <a href="#">Announcements</a>

      <!-- DYNAMIC LOGIN/LOGOUT CHECK -->
      <?php if(isset($_SESSION['user_id'])): ?>
        <div class="user-profile">
          <span class="user-name">👤 <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
          <a href="logout.php" class="logout-btn">Logout</a>
        </div>
      <?php else: ?>
        <a href="user_login.php" class="login-link">Login</a>
      <?php endif; ?>
    </nav>
  </header>

  <main class="services-main">
    <h2>Barangay Document Requests</h2>
    <p>Access public service requests easily and conveniently online. Select the service you need and click “Request Now.”</p>

    <div class="service-request-grid">
      <div class="request-card">
        <h3>Barangay Clearance</h3>
        <p>Apply for your Barangay Clearance digitally in just a few minutes.</p>
        <button class="request-btn" onclick="window.location.href='BC_request-form.php'">Request Now</button>
      </div>

      <div class="request-card">
        <h3>Certificate of Residency</h3>
        <p>Request your residency certificate without visiting the office.</p>
        <button class="request-btn">Request Now</button>
      </div>

      <div class="request-card">
        <h3>Barangay ID</h3>
        <p>Apply for your official Barangay ID for verification and records.</p>
        <button class="request-btn">Request Now</button>
      </div>

      <div class="request-card">
        <h3>Business Clearance</h3>
        <p>Secure your barangay clearance for business operations quickly.</p>
        <button class="request-btn">Request Now</button>
      </div>

      <div class="request-card">
        <h3>Certificate of Indigency</h3>
        <p>Get certification assistance for scholarship or medical aid purposes.</p>
        <button class="request-btn">Request Now</button>
      </div>

      <div class="request-card">
        <h3>Volunteer Registration</h3>
        <p>Join community projects and outreach programs within the barangay.</p>
        <button class="request-btn">Request Now</button>
      </div>
    </div>
  </main>

  <footer>
    <h4>Barangay Pasong Buaya II</h4>
    <p>Serving our community through innovation, compassion, and transparency.</p>
    <p>© 2025 All Rights Reserved</p>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      document.body.classList.add('fade-loaded');
      const links = document.querySelectorAll('a[href]');
      links.forEach(link => {
        if (link.hostname === window.location.hostname && !link.href.includes('logout.php')) {
          link.addEventListener('click', e => {
            e.preventDefault();
            document.body.classList.remove('fade-loaded');
            setTimeout(() => window.location = link.href, 300);
          });
        }
      });
    });
  </script>
</body>
</html>