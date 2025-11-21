<?php
session_start();
include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    // Check user in the database
    $query = "SELECT * FROM users WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {

        $row = mysqli_fetch_assoc($result);

        // Verify password (HASH CHECK)
        if (password_verify($password, $row['password'])) {

            // Set logged-in session variables
            $_SESSION['user_id'] = $row['user_id']; 
            
            // Check if fullname exists (users table), otherwise fallback to username
            $_SESSION['fullname'] = isset($row['fullname']) ? $row['fullname'] : $row['username'];

            echo "<script>
                alert('Login successful! Welcome back.');
                window.location='index.php'; 
            </script>";
            exit;

        } else {
            echo "<script>
                alert('Incorrect password. Try again.');
                window.location='user_login.php';
            </script>";
            exit;
        }

    } else {
        echo "<script>
            alert('Username not found.');
            window.location='user_login.php';
        </script>";
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Barangay Pasong Buaya II</title>
  <link rel="stylesheet" href="auth.css">

  <style>
    .password-wrapper {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      right: 15px;
      top: 11px;
      cursor: pointer;
      font-size: 1.2rem;
      color: #047857;
    }
  </style>
</head>
<body>

<header>
  <h1>Barangay Pasong Buaya II</h1>
  <nav>
    <!-- Updated link to index.php -->
    <a href="index.php">Home</a>
    <a href="user_register.php">Register</a>
  </nav>
</header>

<div class="auth-container">
  <h2>Resident Login</h2>

  <form action="user_login.php" method="POST" class="auth-form">

    <input type="text" name="username" placeholder="Username" required>

    <div class="password-wrapper">
      <input type="password" name="password" id="password" placeholder="Password" required>
      <span class="toggle-password" onclick="togglePW()">👁️</span>
    </div>

    <button type="submit" class="primary">Login</button>

    <p class="switch-text">
      Don’t have an account? <a href="user_register.php">Register here</a>
    </p>
  </form>
</div>

<script>
function togglePW() {
  const pw = document.getElementById("password");
  if (pw.type === "password") {
    pw.type = "text";
  } else {
    pw.type = "password";
  }
}
</script>

</body>
</html>