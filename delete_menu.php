<?php
session_start();

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

// Sambung ke database
$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Dapatkan menuID dari URL
$menuID = $_GET['id'] ?? null;
if (!$menuID) {
    header("Location: manage_menu.php");
    exit();
}

// Dapatkan nama gambar untuk delete file
$stmt = $conn->prepare("SELECT image FROM menu WHERE menuID=?");
$stmt->bind_param("i", $menuID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $imageFile = $row['image'];
    if ($imageFile && file_exists('images/'.$imageFile)) {
        unlink('images/'.$imageFile); // delete file gambar
    }

    // Delete record menu
    $delStmt = $conn->prepare("DELETE FROM menu WHERE menuID=?");
    $delStmt->bind_param("i", $menuID);
    $delStmt->execute();
    $delStmt->close();
}

$stmt->close();
$conn->close();

// Redirect balik ke manage menu
header("Location: manage_menu.php");
exit();
?>
