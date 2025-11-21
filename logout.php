<?php
session_start();
session_unset();
session_destroy();
echo "<script>alert('You have been logged out.'); window.location='user_login.php';</script>";
exit;
?>