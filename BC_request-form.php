<?php
session_start();
include "db_connection.php";

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('You must login first to request a document.'); window.location='user_login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// 2. Fetch Resident Data linked to this User
// We join 'residents' and 'users' to get the email from the users table if needed
$query = "SELECT r.*, u.email 
          FROM residents r 
          JOIN users u ON r.user_id = u.user_id 
          WHERE r.user_id = '$user_id' LIMIT 1";

$result = mysqli_query($conn, $query);

// Initialize empty variables to avoid errors if no data found
$fname = $mname = $lname = $gender = $dob = $email = $contact = "";
$religion = $civil_status = $address = $purok = $residency = $years = "";
$em_name = $em_rel = $em_num = "";

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    // Assign DB values to variables
    $fname    = $row['first_name'];
    $mname    = $row['middle_name'];
    $lname    = $row['last_name'];
    $gender   = $row['gender'];
    $dob      = $row['birth_date'];
    $email    = $row['email']; // From users table
    $contact  = $row['contact_number'];
    $religion = $row['religion'];
    
    // Address Handling: The DB has one full address field. 
    // We will put it in the 'Street' field or a general address field.
    $address  = $row['address']; 
    
    $residency = $row['residency_status'];
    $years    = $row['years_in_pb2'];
    
    $em_name  = $row['emergency_contact_name'];
    $em_rel   = $row['emergency_contact_relationship'];
    $em_num   = $row['emergency_contact_number'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Barangay Clearance Request | Barangay Pasong Buaya II</title>
  <link rel="stylesheet" href="request-form.css">
</head>
<body>
  <header>
    <h1>Barangay Pasong Buaya II</h1>
    <nav>
      <a href="index.html">Home</a>
      <a href="services.html">Services</a>
      <a href="#">Announcements</a>
    </nav>
  </header>

  <main>
    <div class="form-container">
      <h2>Barangay Clearance Request</h2>
      <div class="step-indicator">
        <div class="step active">Personal</div>
        <div class="step">Address</div>
        <div class="step">Important Info</div>
        <div class="step">Proof of Identity</div>
      </div>

      <form id="multiStepForm" action="submit_request.php" method="POST" enctype="multipart/form-data">
        
        <!-- Step 1: Personal Information -->
        <div class="form-section active">
          <label>Precinct # (optional)</label>
          <input type="text" name="precinct">
          
          <label>Firstname *</label>
          <!-- VALUE ATTRIBUTE IS PRE-FILLED WITH PHP -->
          <input type="text" name="first_name" value="<?php echo $fname; ?>" required readonly style="background-color: #f3f4f6;">
          
          <label>Middlename</label>
          <input type="text" name="middle_name" value="<?php echo $mname; ?>" readonly style="background-color: #f3f4f6;">
          
          <label>Lastname *</label>
          <input type="text" name="last_name" value="<?php echo $lname; ?>" required readonly style="background-color: #f3f4f6;">
          
          <label>Gender *</label>
          <select name="gender" required style="background-color: #f3f4f6; pointer-events: none;">
            <option value="">Select</option>
            <option value="Male" <?php if($gender == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if($gender == 'Female') echo 'selected'; ?>>Female</option>
          </select>
          
          <label>Birth Date *</label>
          <input type="date" name="birth_date" value="<?php echo $dob; ?>" required readonly style="background-color: #f3f4f6;">
          
          <label>Email</label>
          <input type="email" name="email" value="<?php echo $email; ?>" readonly style="background-color: #f3f4f6;">
          
          <label>Contact *</label>
          <input type="text" name="contact" value="<?php echo $contact; ?>" required>
          
          <label>Religion</label>
          <input type="text" name="religion" value="<?php echo $religion; ?>">
          
          <!-- Civil Status was not in your DB yet, so we leave it editable -->
          <label>Civil Status *</label>
          <select name="civil_status" required>
            <option value="">Select</option>
            <option value="Single">Single</option>
            <option value="Married">Married</option>
            <option value="Widowed">Widowed</option>
          </select>
          
          <label>Sector (Optional)</label>
          <input type="text" name="sector">
          
          <h4>Emergency Details</h4>
          <label>Emergency Contact Name *</label>
          <input type="text" name="em_name" value="<?php echo $em_name; ?>" required readonly style="background-color: #f3f4f6;">
          
          <label>Emergency Relationship *</label>
          <input type="text" name="em_rel" value="<?php echo $em_rel; ?>" required readonly style="background-color: #f3f4f6;">
          
          <label>Emergency Contact # *</label>
          <input type="text" name="em_num" value="<?php echo $em_num; ?>" required readonly style="background-color: #f3f4f6;">
          
          <!-- Emergency Address was not in DB, so left editable -->
          <label>Emergency Contact Address *</label>
          <input type="text" name="em_address" required>
        </div>

        <!-- Step 2: Address Information -->
        <div class="form-section">
          <label>Residency *</label>
          <select name="residency" required style="background-color: #f3f4f6; pointer-events: none;">
            <option value="">Select</option>
            <option value="Owner" <?php if($residency == 'Owner') echo 'selected'; ?>>Owner</option>
            <option value="Renter" <?php if($residency == 'Renter') echo 'selected'; ?>>Renter</option>
            <option value="Living with Family" <?php if($residency == 'Sharer' || $residency == 'Living with Family') echo 'selected'; ?>>Living with Family</option>
          </select>
          
          <label>Years in Pasong Buaya II *</label>
          <input type="number" name="years" value="<?php echo $years; ?>" min="0" required readonly style="background-color: #f3f4f6;">
          
          <label>Full Address (From Profile)</label>
          <input type="text" name="full_address" value="<?php echo $address; ?>" readonly style="background-color: #f3f4f6;">
          
          <p style="font-size: 0.8rem; color: gray; margin-top: 10px;">* Please specify Subdivision/Street below if not clear above.</p>
          
          <label>Subdivision / Street *</label>
          <input type="text" name="subdivision" required>
          
          <label>Barangay *</label>
          <input type="text" value="Pasong Buaya II" readonly>
          <label>City *</label>
          <input type="text" value="Imus City" readonly>
          <label>Province *</label>
          <input type="text" value="Cavite" readonly>
        </div>

        <!-- Step 3: Important Info -->
        <div class="form-section">
          <label>Certificate Type *</label>
          <input type="text" name="doc_type" value="Barangay Clearance" readonly>
          
          <label>Purpose *</label>
          <input type="text" name="purpose" placeholder="e.g. Job Application, School Requirement" required>
        </div>

        <!-- Step 4: Proof of Identity -->
        <div class="form-section">
          <p>* Please provide Two (2) Valid IDs and a Photo of you holding them.</p>
          
          <label>Upload Valid ID (Front) *</label>
          <input type="file" name="id_front" accept="image/*,application/pdf" required>
          
          <label>Upload Valid ID (Back) *</label>
          <input type="file" name="id_back" accept="image/*,application/pdf" required>
          
          <label>Upload Your Photo Holding IDs *</label>
          <input type="file" name="id_holding" accept="image/*,application/pdf" required>
          
          <label>Additional Notes (optional)</label>
          <textarea name="notes" placeholder="Any additional information"></textarea>
        </div>

        <div class="btn-container">
          <button type="button" id="prevBtn">Previous</button>
          <button type="button" id="nextBtn">Next</button>
          <!-- Hidden Submit button for JS to trigger -->
          <button type="submit" id="submitBtn" style="display: none;">Submit Request</button>
        </div>
      </form>
    </div>
  </main>

  <footer>
    <h4>Barangay Pasong Buaya II</h4>
    <p>Serving our community through innovation, compassion, and transparency.</p>
    <p>© 2025 All Rights Reserved</p>
  </footer>

  <script>
    const steps = document.querySelectorAll('.step');
    const sections = document.querySelectorAll('.form-section');
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    const submitBtn = document.getElementById('submitBtn');
    const form = document.getElementById('multiStepForm');
    let current = 0;

    function showStep(i) {
      sections.forEach((sec, index) => sec.classList.toggle('active', index === i));
      steps.forEach((step, index) => {
        step.classList.toggle('active', index === i);
        step.classList.toggle('completed', index < i);
      });
      prevBtn.style.display = i === 0 ? 'none' : 'inline-block';
      
      if (i === sections.length - 1) {
        nextBtn.textContent = 'Submit';
        // Change button behavior to submit form
        nextBtn.onclick = function() {
           // Simple validation for file inputs on last step
           const fileInputs = sections[current].querySelectorAll('input[type="file"]');
           let allFiles = true;
           fileInputs.forEach(input => {
               if (input.hasAttribute('required') && input.files.length === 0) allFiles = false;
           });
           
           if(allFiles) {
               alert('Submitting your request...');
               form.submit(); 
           } else {
               alert('Please upload all required files.');
           }
        };
      } else {
        nextBtn.textContent = 'Next';
        nextBtn.onclick = function() { nextStep(); };
      }
    }

    function nextStep() {
        // Validate current step inputs
        const currentSection = sections[current];
        const inputs = currentSection.querySelectorAll('input[required], select[required]');
        let valid = true;
        
        inputs.forEach(input => {
            if (!input.value) {
                valid = false;
                input.style.borderColor = "red";
            } else {
                input.style.borderColor = "#bbf7d0";
            }
        });

        if (valid) {
            if (current < sections.length - 1) {
                current++;
                showStep(current);
            }
        } else {
            alert("Please fill in all required fields before proceeding.");
        }
    }

    prevBtn.addEventListener('click', () => {
      if (current > 0) {
        current--;
        showStep(current);
      }
    });

    showStep(current);
  </script>
</body>
</html>