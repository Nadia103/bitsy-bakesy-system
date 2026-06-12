<?php
session_start();

// Pastikan hanya Admin boleh proses
if (!isset($_SESSION['staffRole']) || $_SESSION['staffRole'] != 'Admin') {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bitsy";

// Sambung ke database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data dari form
$name = $_POST['staffName'];
$email = $_POST['staffEmail'];
$pass = $_POST['staffPassword']; // plain text
$role = $_POST['staffRole'];

// Check duplicate email
$sql_check = "SELECT * FROM staff WHERE staffEmail='$email'";
$result = $conn->query($sql_check);

if ($result->num_rows > 0) {
    echo "<script>alert('Email already exists!'); window.history.back();</script>";
} else {
    $sql_insert = "INSERT INTO staff (staffName, staffEmail, staffPassword, staffRole)
                   VALUES ('$name', '$email', '$pass', '$role')";
    if ($conn->query($sql_insert) === TRUE) {
        echo "<script>alert('Staff account created successfully!'); window.location.href='staff_list.php';</script>";
    } else {
        echo "<script>alert('Error: ".$conn->error."'); window.history.back();</script>";
    }
}

$conn->close();
?>
