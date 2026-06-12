<?php
session_start();

if(!isset($_SESSION['staffRole']) || $_SESSION['staffRole'] != 'Cashier'){
    header("Location: staff_login.php");
    exit();
}

if(!isset($_POST['items']) || empty($_POST['items'])){
    die("Invalid order.");
}

$conn = mysqli_connect("localhost","root","","bitsy");
if(!$conn){ die("Connection failed: ".mysqli_connect_error()); }

$staffID = $_SESSION['staffID'];
$customerID = $_POST['customerID'] ?? 0;
$paymentMethod = $_POST['paymentMethod'];
$totalAmount = 0;

// =========================
// 1. VALIDATE STOCK
// =========================
foreach($_POST['items'] as $item){
    $unitID = (int)$item['menuID']; // actually unitID
    $qty    = (int)$item['qty'];

    $res = mysqli_query($conn, "SELECT stockQuantity FROM menu_unit WHERE unitID=$unitID LIMIT 1");
    if(!$res || mysqli_num_rows($res) == 0){
        die("Menu item not found.");
    }

    $row = mysqli_fetch_assoc($res);
    $stock = (int)$row['stockQuantity'];

    if($stock < $qty){
        die("Insufficient stock for item ID $unitID.");
    }

    $price = (float)$item['price'];
    $totalAmount += $price * $qty;
}

// =========================
// 2. INSERT ORDER
// =========================
$sql = "INSERT INTO orders
(customerID,totalAmount,status,paymentMethod,orderDate,staffID,orderType)
VALUES
($customerID,$totalAmount,'Pending','$paymentMethod',NOW(),$staffID,'Walk-in')";

if(!mysqli_query($conn,$sql)){
    die("Failed to insert order: " . mysqli_error($conn));
}

$orderID = mysqli_insert_id($conn);

// =========================
// 3. INSERT ORDER ITEMS & DEDUCT STOCK
// =========================
foreach($_POST['items'] as $item){
    $unitID = (int)$item['menuID']; // actually unitID
    $qty    = (int)$item['qty'];
    $price  = (float)$item['price'];

    // Insert order item
    mysqli_query($conn,"INSERT INTO order_items (orderID,quantity,unitID,price)
                        VALUES ($orderID,$qty,$unitID,$price)");

    // Deduct stock
    mysqli_query($conn,"UPDATE menu_unit SET stockQuantity = stockQuantity - $qty WHERE unitID=$unitID");
}

// =========================
// 4. REDIRECT RECEIPT
// =========================
header("Location: receipt.php?orderID=$orderID");
exit();
?>
