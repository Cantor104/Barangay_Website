<?php
session_start();
include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // The plain text password typed by user

    $query = "SELECT * FROM admins WHERE username = '$username' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        // UPDATE: Use 'password_hash' column from the database
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['admin_id'] = $row['admin_id'];
            $_SESSION['admin_role'] = $row['role']; // Store role (Super/Admin)
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Incorrect Password";
        }
    } else {
        $error = "Admin not found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Barangay BIMS</title>
    <link rel="stylesheet" href="auth.css">
</head>
<body>
<div class="auth-container" style="margin-top: 100px;">
    <h2 style="color: #d97706;">Admin Access</h2>
    <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST" class="auth-form">
        <input type="text" name="username" placeholder="Admin Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" class="primary" style="background-color: #d97706;">Login to Dashboard</button>
    </form>
    <p class="switch-text"><a href="index.php">Back to Website</a></p>
</div>
</body>
</html>