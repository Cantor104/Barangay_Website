<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Pasong Buaya II</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <h1>Barangay Pasong Buaya II</h1>
    <nav>
      <a href="index.php">Home</a>
      <a href="services.php">Services</a>
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

  <main>
    <h2>Welcome to Barangay Pasong Buaya II</h2>
    <p>Empowering our community with transparent services, timely updates, and accessible online tools for all residents.</p>
    <div class="hero-buttons">
      <!-- Logic: If logged in, go to services, else go to login -->
      <button class="primary" onclick="window.location.href='<?php echo isset($_SESSION['user_id']) ? 'services.php' : 'user_login.php'; ?>'">Track My Document Request</button>
      <button class="outline">Learn More</button>
    </div>
  </main>

  <section>
    <h3>Digital Barangay Services</h3>
    <div class="services-grid">
      <div class="service-card">Community Events</div>
      <div class="service-card">Incident Reporting</div>
      <div class="service-card">Resident Feedback</div>
      <div class="service-card">Volunteer Programs</div>
      <div class="service-card"><a href="services.php"></a>Barangay Document Requests</div>
    </div>
  </section>

  <footer>
    <h4>Barangay Pasong Buaya II</h4>
    <p>Serving our community through innovation, compassion, and transparency.</p>
    <p>© 2025 All Rights Reserved</p>
  </footer>

  <button id="chat-btn">💬</button>
  <div id="chat-box">
    <h5>Hi, I'm Crocky!: Barangay Assistance Chat</h5>
    <div class="chat-window" id="chat-window">
      <p><strong>Resident:</strong> Hello! How can I get a Barangay Clearance?</p>
      <p><strong>Crocky:</strong> You can request it in this website in 'Services' under <em>Clearance Request</em>.</p>
    </div>
    <div class="chat-input">
      <input type="text" id="chat-input" placeholder="Type your message...">
      <button id="send-btn">➤</button>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>