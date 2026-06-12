<?php
session_start();

// Check login
if (!isset($_SESSION['staffID']) || $_SESSION['staffRole'] !== 'Baker') {
    header("Location: staff_login.php");
    exit();
}

$staffID = $_SESSION['staffID'];

$conn = new mysqli("localhost", "root", "", "bitsy");
if ($conn->connect_error) { die("Connection failed: ".$conn->connect_error); }

// Validate POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderID = intval($_POST['orderID'] ?? 0);
    $status = $_POST['status'] ?? '';

    // Check valid status
    $allowed = ["Pending", "In Process", "Completed", "Cancelled"];
    if (!in_array($status, $allowed)) {
        $_SESSION['error'] = "Invalid status.";
        header("Location: manage_orders.php");
        exit();
    }

    // Validate order belongs to this baker
    $stmt = $conn->prepare("SELECT assignedTo FROM orders WHERE orderID = ?");
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $stmt->bind_result($assigned);
    $stmt->fetch();
    $stmt->close();

    if ($assigned != $staffID) {
        $_SESSION['error'] = "You are not assigned to this order.";
        header("Location: manage_orders.php");
        exit();
    }

    // Update status
    $stmt2 = $conn->prepare("UPDATE orders SET status = ? WHERE orderID = ?");
    $stmt2->bind_param("si", $status, $orderID);
    $stmt2->execute();
    $stmt2->close();

    $_SESSION['success'] = "Order status updated.";
    header("Location: manage_orders.php");
    exit();
}

header("Location: manage_orders.php");
exit();
