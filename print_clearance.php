<?php
session_start();
include "db_connection.php";

// 1. SECURITY CHECK: Only Admins can view this page
if (!isset($_SESSION['admin_id'])) {
    echo "<div style='text-align:center; margin-top:50px; font-family:Arial;'>
            <h2 style='color:red;'>Access Denied</h2>
            <p>You do not have permission to view this document.</p>
            <a href='index.php'>Return to Home</a>
          </div>";
    exit;
}

// 2. Get the Request ID
if (!isset($_GET['id'])) {
    die("Error: No document ID specified.");
}

$history_id = intval($_GET['id']);

// 3. Fetch Resident & Request Data
$query = "SELECT h.*, r.first_name, r.middle_name, r.last_name, r.suffix, r.address, r.birth_date, r.gender, r.civil_status 
          FROM document_history h
          JOIN residents r ON h.resident_id = r.resident_id
          WHERE h.history_id = '$history_id' LIMIT 1";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die("Error: Document request not found.");
}

$row = mysqli_fetch_assoc($result);

// 4. Format Data for the Certificate
$fullname = strtoupper($row['first_name'] . " " . substr($row['middle_name'], 0, 1) . ". " . $row['last_name'] . " " . $row['suffix']);
$address  = ucwords($row['address']);
$purpose  = strtoupper($row['purpose']);
$date_issued = date("jS \d\a\y \of F Y", strtotime($row['request_date']));

// Calculate Age
$dob = new DateTime($row['birth_date']);
$now = new DateTime();
$age = $now->diff($dob)->y;

// Civil Status (Defaults to Single if empty)
$civil_status = isset($row['civil_status']) && !empty($row['civil_status']) ? ucfirst($row['civil_status']) : 'Single'; 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Clearance - <?php echo $fullname; ?></title>
    <link rel="stylesheet" href="document.css">
</head>
<body>

    <!-- Admin Controls -->
    <div class="no-print action-bar">
        <button onclick="window.print()"> Print Document</button>
        <button onclick="window.close()" style="background:#ef4444; color:white;"> Close Window</button>
    </div>

    <!-- The Printable Document -->
    <div class="paper">
        <div class="doc-header">
            <div class="logo-left">
                <img src="assets/barangay_logo.png" alt="Logo" onerror="this.style.display='none'"> 
            </div>
            <div class="header-text">
                <p>Republic of the Philippines</p>
                <p>Province of Cavite</p>
                <p>City of Imus</p>
                <h3>BARANGAY PASONG BUAYA II</h3>
                <p><b>OFFICE OF THE PUNONG BARANGAY</b></p>
            </div>
            <div class="logo-right">
                <img src="assets/city_logo.png" alt="Logo" onerror="this.style.display='none'">
            </div>
        </div>

        <hr class="divider-line">

        <h1 class="doc-title">BARANGAY CLEARANCE</h1>

        <div class="doc-body">
            <p class="salutation">TO WHOM IT MAY CONCERN:</p>

            <p class="paragraph">
                <b>THIS IS TO CERTIFY</b> that <b><?php echo $fullname; ?></b>, 
                <b><?php echo $age; ?></b> years old, 
                <b><?php echo $civil_status; ?></b>, Filipino citizen, is a permanent resident of 
                <b><?php echo $address; ?></b>, Barangay Pasong Buaya II, City of Imus, Cavite.
            </p>

            <p class="paragraph">
                <b>THIS IS TO CERTIFY FURTHER</b> that the above-named person has no derogatory record on file in this office as of this date. He/She is known to be of good moral character and a law-abiding citizen in the community.
            </p>

            <p class="paragraph">
                This certification is issued upon the request of the above-named person for the purpose of:
            </p>

            <h3 class="purpose-text">"<?php echo $purpose; ?>"</h3>

            <p class="paragraph">
                <b>ISSUED</b> this <?php echo $date_issued; ?> at Barangay Pasong Buaya II, City of Imus, Cavite.
            </p>
        </div>

        <div class="signatures">
            <div class="sign-column">
                <p>Prepared by:</p>
                <br><br>
                <p class="sign-line"><b>BARANGAY SECRETARY</b></p>
                <p class="sign-label">Barangay Secretary</p>
            </div>

            <div class="sign-column">
                <p>Approved by:</p>
                <br><br>
                <p class="sign-line"><b>HON. KAPITAN NAME</b></p> 
                <p class="sign-label">Punong Barangay</p>
            </div>
        </div>

        <div class="doc-footer">
            <p><i>Not valid without the official Barangay Seal.</i></p>
            <div class="seal-box">OFFICIAL<br>SEAL</div>
        </div>

    </div>

</body>
</html>