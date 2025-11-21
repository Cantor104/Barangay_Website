<?php
session_start();
include "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 1. CAPTURE DATA FROM FORM ---
    // Account Info
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    // Personal Info
    $fname    = trim($_POST['first_name']);
    $mname    = trim($_POST['middle_name']);
    $lname    = trim($_POST['last_name']);
    $suffix   = trim($_POST['suffix']);
    $gender   = $_POST['gender'];
    $dob      = $_POST['birth_date'];
    $religion = trim($_POST['religion']);

    // Address & Residency
    $address   = trim($_POST['address']);
    $purok     = isset($_POST['purok']) ? trim($_POST['purok']) : null;
    $residency = $_POST['residency_status']; 
    $years     = intval($_POST['years_in_pb2']);

    // Contact Info
    $contact   = trim($_POST['contact_number']);

    // Emergency Contact
    $em_name   = trim($_POST['emergency_contact_name']);
    $em_rel    = trim($_POST['emergency_contact_relationship']);
    $em_num    = trim($_POST['emergency_contact_number']);

    // --- 2. SERVER-SIDE VALIDATION ---

    // A. Validate Email Structure (Strict)
    // Rule: Local Part + @ + Domain Name + . + TLD (2+ chars)
    $emailRegex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
    
    if (!preg_match($emailRegex, $email)) {
        echo "<script>alert('Invalid Email. It must contain a Local Part, @ symbol, Domain Name, and TLD (e.g., .com).'); window.history.back();</script>";
        exit;
    }

    // B. Check password match
    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit;
    }

    // C. Strong password check (Server-side backup)
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $password)) {
        echo "<script>alert('Password is too weak. It must include uppercase, lowercase, numbers, and symbols.'); window.history.back();</script>";
        exit;
    }

    // D. Check duplicates (Username or Email)
    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE username='$username' OR email='$email'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Username or Email already registered. Please try another.'); window.history.back();</script>";
        exit;
    }

    // --- 3. HASHING & DATABASE INSERTION ---
    
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    mysqli_begin_transaction($conn);

    try {
        // Insert into users
        $queryUser = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'resident')";
        $stmtUser = mysqli_prepare($conn, $queryUser);
        mysqli_stmt_bind_param($stmtUser, "sss", $username, $email, $hashedPassword);
        
        if (!mysqli_stmt_execute($stmtUser)) {
            throw new Exception("Error creating user account.");
        }

        $new_user_id = mysqli_insert_id($conn);

        // Insert into residents
        $queryResident = "INSERT INTO residents 
            (user_id, first_name, middle_name, last_name, suffix, gender, birth_date, religion, 
             contact_number, email_address, address, purok, residency_status, years_in_pb2, 
             emergency_contact_name, emergency_contact_relationship, emergency_contact_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmtRes = mysqli_prepare($conn, $queryResident);
        
        mysqli_stmt_bind_param($stmtRes, "issssssssssisisss", 
            $new_user_id, $fname, $mname, $lname, $suffix, $gender, $dob, $religion,
            $contact, $email, $address, $purok, $residency, $years,
            $em_name, $em_rel, $em_num
        );

        if (!mysqli_stmt_execute($stmtRes)) {
            throw new Exception("Error saving resident profile.");
        }

        mysqli_commit($conn);
        echo "<script>alert('Registration successful! You may now log in.'); window.location='user_login.php';</script>";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Registration Failed: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register | Barangay Pasong Buaya II</title>
  <link rel="stylesheet" href="auth.css">
  <style>
    .auth-container { max-width: 600px; margin-top: 3rem; margin-bottom: 3rem; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .full-width { grid-column: span 2; }
    .section-title { 
        grid-column: span 2; 
        font-size: 0.9rem; 
        color: #047857; 
        font-weight: bold; 
        margin-top: 1rem; 
        border-bottom: 1px solid #bbf7d0; 
        padding-bottom: 5px;
    }
    select {
        width: 100%;
        padding: 1rem;
        border-radius: 1rem;
        border: 2px solid #bbf7d0;
        background: #ecfdf5;
        outline: none;
        font-size: 1rem;
        color: #1f2937;
    }
    /* Warning Text Styles */
    .warning-text {
        display: none;
        color: #dc2626; /* Red color */
        font-size: 0.85rem;
        margin-top: -0.5rem;
        margin-bottom: 0.5rem;
        grid-column: span 2;
        background: #fef2f2;
        padding: 0.5rem;
        border-radius: 0.5rem;
        border: 1px solid #fca5a5;
    }
    .input-error {
        border-color: #dc2626 !important;
        background-color: #fef2f2 !important;
    }
  </style>
</head>
<body>

<header>
  <h1>Barangay Pasong Buaya II</h1>
  <nav>
    <a href="index.html">Home</a>
    <a href="user_login.php">Login</a>
  </nav>
</header>

<div class="auth-container">
  <h2>Resident Registration</h2>

  <form action="user_register.php" method="POST" class="auth-form" onsubmit="return validateForm()">

    <!-- 1. PERSONAL INFORMATION -->
    <div class="form-grid">
        <div class="section-title">Personal Information</div>
        
        <input type="text" name="first_name" placeholder="First Name" required>
        <input type="text" name="middle_name" placeholder="Middle Name">
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="text" name="suffix" placeholder="Suffix (e.g. Jr.)">
        
        <select name="gender" required>
            <option value="" disabled selected>Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select>

        <input type="date" name="birth_date" required title="Birth Date">
        <input type="text" name="religion" placeholder="Religion" class="full-width">
    </div>

    <!-- 2. ADDRESS & CONTACT -->
    <div class="form-grid">
        <div class="section-title">Address & Contact</div>
        
        <input type="text" name="address" placeholder="Complete Address (Block, Lot, Street)" class="full-width" required>
        <input type="number" name="purok" placeholder="Purok No." required>
        
        <select name="residency_status" required>
            <option value="" disabled selected>Residency Status</option>
            <option value="Owner">Owner</option>
            <option value="Renter">Renter</option>
            <option value="Sharer">Sharer</option>
            <option value="Boarder">Boarder</option>
        </select>

        <input type="number" name="years_in_pb2" placeholder="Years in PB2" required>
        <input type="text" name="contact_number" placeholder="Mobile Number" required>
    </div>

    <!-- 3. EMERGENCY CONTACT -->
    <div class="form-grid">
        <div class="section-title">Emergency Contact</div>
        
        <input type="text" name="emergency_contact_name" placeholder="Contact Person Name" class="full-width" required>
        <input type="text" name="emergency_contact_relationship" placeholder="Relationship" required>
        <input type="text" name="emergency_contact_number" placeholder="Emergency Mobile No." required>
    </div>

    <!-- 4. ACCOUNT DETAILS (With Validations) -->
    <div class="form-grid">
        <div class="section-title">Account Details</div>
        
        <input type="text" name="username" placeholder="Username" class="full-width" required>
        
        <!-- Email Field -->
        <input type="email" name="email" id="email" placeholder="Email Address" class="full-width" required oninput="clearError('email-warning', 'email')">
        
        <!-- Email Warning -->
        <div class="warning-text" id="email-warning">
            <strong>Invalid Email Format.</strong> It must include:
            <ul style="margin: 5px 0 0 20px; padding: 0;">
                <li>Local Part (before @)</li>
                <li>"@" Symbol</li>
                <li>Domain Name (e.g. gmail, yahoo)</li>
                <li>Top-Level Domain (e.g. .com, .ph)</li>
            </ul>
        </div>

        <!-- Password Fields -->
        <input type="password" name="password" id="password" placeholder="Password" required oninput="clearError('pw-warning', 'password')">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required oninput="clearError('pw-warning', 'confirm_password')">
        
        <!-- Password Warning -->
        <p class="warning-text" id="pw-warning">
            Password must be at least 6 characters and contain:<br>
            ✔ Uppercase Letter (A-Z)<br>
            ✔ Lowercase Letter (a-z)<br>
            ✔ Number (0-9)<br>
            ✔ Symbol (!@#$%^&*)
        </p>
    </div>

    <button type="submit" class="primary" style="margin-top: 1rem;">Register Account</button>

    <p class="switch-text">
      Already have an account? <a href="user_login.php">Login here</a>
    </p>
  </form>
</div>

<script>
// Helper to clear errors when user types
function clearError(warningId, inputId) {
    document.getElementById(warningId).style.display = 'none';
    document.getElementById(inputId).classList.remove('input-error');
}

function validateForm() {
    let isValid = true;

    // 1. Validate Email (Strict Regex based on your requirements)
    const email = document.getElementById("email");
    const emailWarning = document.getElementById("email-warning");
    
    // Matches: [Alphanumeric/symbols] @ [Alphanumeric/Hyphens] . [Letters (2+)]
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    if (!emailRegex.test(email.value)) {
        emailWarning.style.display = "block";
        email.classList.add("input-error");
        isValid = false;
    } else {
        emailWarning.style.display = "none";
        email.classList.remove("input-error");
    }

    // 2. Validate Password
    const pw = document.getElementById("password");
    const confirm = document.getElementById("confirm_password");
    const pwWarning = document.getElementById("pw-warning");

    // Strict Regex: At least 6 chars, 1 Upper, 1 Lower, 1 Number, 1 Symbol
    const pwRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/;

    // Check complexity
    if (!pwRegex.test(pw.value)) {
        pwWarning.innerHTML = `
            <strong>Weak Password:</strong><br>
            Must contain Uppercase, Lowercase, Number, and Symbol.
        `;
        pwWarning.style.display = "block";
        pw.classList.add("input-error");
        isValid = false;
    } 
    // Check match
    else if (pw.value !== confirm.value) {
        pwWarning.innerHTML = "Passwords do not match.";
        pwWarning.style.display = "block";
        confirm.classList.add("input-error");
        isValid = false;
    } 
    else {
        pwWarning.style.display = "none";
        pw.classList.remove("input-error");
        confirm.classList.remove("input-error");
    }

    return isValid;
}
</script>

</body>
</html>