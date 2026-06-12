<?php
session_start();

// Bersihkan semua session
$_SESSION = [];
session_unset();
session_destroy();

// Redirect ke login page
header("Location: staff_login.php");
exit();
?>
