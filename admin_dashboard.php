<?php
session_start();
include "db_connection.php";

// Security: Only Admins
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch requests + Resident Name + Resident Email (from users table)
$query = "SELECT h.*, r.first_name, r.last_name, u.email 
          FROM document_history h 
          JOIN residents r ON h.resident_id = r.resident_id 
          LEFT JOIN users u ON r.user_id = u.user_id
          ORDER BY h.request_date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | BIMS</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f3f4f6; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #047857; color: white; }
        
        /* Button Styles */
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 5px; color: white; font-size: 0.85rem; margin-right: 5px; display: inline-block; }
        .btn-print { background: #0ea5e9; } /* Blue */
        .btn-print:hover { background: #0284c7; }
        .btn-email { background: #8b5cf6; } /* Purple */
        .btn-email:hover { background: #7c3aed; }
        .btn-logout { background: #ef4444; } /* Red */
        
        /* Status Badges */
        .status-badge { padding: 3px 8px; border-radius: 10px; font-size: 0.8rem; font-weight: bold; }
        .Pending { background: #fef3c7; color: #d97706; }
        .Processing { background: #e0f2fe; color: #0284c7; }
        .Ready { background: #d1fae5; color: #059669; } /* Ready for Claiming */
        .Completed { background: #dcfce7; color: #166534; }
        .Cancelled { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>

    <div class="header">
        <h2> Document Requests</h2>
        <a href="logout.php" class="btn btn-logout">Logout</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Resident Name</th>
                    <th>Document Type</th>
                    <th>Purpose</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php 
                    // Prepare Data variables
                    $ref_no = "REF-" . str_pad($row['history_id'], 6, "0", STR_PAD_LEFT);
                    $name   = $row['first_name'] . " " . $row['last_name'];
                    
                    // --- PREPARE EMAIL CONTENT ---
                    $to = $row['email'];
                    $subject = "Update on Document Request $ref_no";
                    $body = "Dear $name,\n\nThis is an update regarding your request for a " . $row['document_type'] . ".\n\nCurrent Status: " . $row['status'] . "\n\nPlease visit the Barangay Hall for further instructions or claiming.\n\nThank you,\nBarangay Pasong Buaya II";
                    
                    // Encode for URL (makes it safe for the link)
                    $mailto_link = "mailto:" . $to . "?subject=" . urlencode($subject) . "&body=" . urlencode($body);
                ?>
                <tr>
                    <td><strong><?php echo $ref_no; ?></strong></td>
                    <td><?php echo $name; ?></td>
                    <td><?php echo $row['document_type']; ?></td>
                    <td><?php echo $row['purpose']; ?></td>
                    <td><?php echo date("M d, Y", strtotime($row['request_date'])); ?></td>
                    <td>
                        <span class="status-badge <?php echo explode(' ', $row['status'])[0]; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                    </td>
                    <td>
                        <!-- Print Button -->
                        <a href="print_clearance.php?id=<?php echo $row['history_id']; ?>" class="btn btn-print" target="_blank">🖨️ Print</a>
                        
                        <!-- Email Button -->
                        <?php if(!empty($to)): ?>
                            <a href="<?php echo $mailto_link; ?>" class="btn btn-email"> Email</a>
                        <?php else: ?>
                            <span style="color:gray; font-size:0.8rem;">No Email</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</body>
</html>