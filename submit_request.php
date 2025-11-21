<?php
session_start();
include "db_connection.php";

// 1. Security: Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id = $_SESSION['user_id'];

    // 2. Get Resident ID from User ID
    $res_query = mysqli_query($conn, "SELECT resident_id FROM residents WHERE user_id='$user_id'");
    if (mysqli_num_rows($res_query) == 0) {
        echo "<script>alert('Error: Resident profile not found.'); window.location='index.php';</script>";
        exit;
    }
    $row = mysqli_fetch_assoc($res_query);
    $resident_id = $row['resident_id'];

    // 3. Capture Form Data
    $doc_type     = mysqli_real_escape_string($conn, $_POST['doc_type']);
    $purpose      = mysqli_real_escape_string($conn, $_POST['purpose']);
    $civil_status = mysqli_real_escape_string($conn, $_POST['civil_status']);

    // 4. Update Resident Profile (Civil Status)
    // We update this now so the Admin sees the correct status when printing later
    $update_sql = "UPDATE residents SET civil_status='$civil_status' WHERE resident_id='$resident_id'";
    mysqli_query($conn, $update_sql);

    // 5. Handle File Uploads
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    function uploadFile($fileInputName, $targetDir) {
        if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] != 0) return null;
        
        $fileName = basename($_FILES[$fileInputName]["name"]);
        $targetFilePath = $targetDir . uniqid() . "_" . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
        
        $allowTypes = array('jpg','png','jpeg','pdf');
        if(in_array(strtolower($fileType), $allowTypes)){
            if(move_uploaded_file($_FILES[$fileInputName]["tmp_name"], $targetFilePath)){
                return $targetFilePath;
            }
        }
        return null;
    }

    $frontID   = uploadFile('id_front', $uploadDir);
    $backID    = uploadFile('id_back', $uploadDir);
    $holdingID = uploadFile('id_holding', $uploadDir);

    // 6. Insert Request into Database
    $query = "INSERT INTO document_history 
              (resident_id, document_type, purpose, status, file_id_front, file_id_back, file_photo_holding, request_date) 
              VALUES 
              ('$resident_id', '$doc_type', '$purpose', 'Pending', '$frontID', '$backID', '$holdingID', NOW())";

    if (mysqli_query($conn, $query)) {
        $new_history_id = mysqli_insert_id($conn);
        
        // Generate Reference Number (e.g., REF-000005)
        $ref_number = "REF-" . str_pad($new_history_id, 6, "0", STR_PAD_LEFT);

        // SHOW REFERENCE NUMBER & REDIRECT
        echo "<script>
            alert('Request Submitted Successfully!\\n\\nYour Reference Number is: $ref_number\\n\\nPlease save this number for tracking. Wait for the Admin to process your request.');
            window.location='services.php';
        </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>