<?php
session_start();          // Mula session
session_unset();          // Padam semua session variable (contoh: $_SESSION['user_id'], $_SESSION['username'])
session_destroy();        // Hapus session sepenuhnya
header("Location: index.php");  // Redirect ke halaman utama
exit();
?>
