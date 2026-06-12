<?php
session_start();
$conn = mysqli_connect("localhost","root","","bitsy");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// Redirect kalau user tak login atau cart kosong
if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: menu.php");
    exit();
}

$customerID = $_SESSION['user_id'];
$customerName = $_POST['customerName'] ?? '';
$customerPhone = $_POST['customerPhone'] ?? '';

// Total amount
$total = 0;
foreach($_SESSION['cart'] as $item) {
    $price = $item['price'] ?? 0;
    $pieces = $item['pieces'] ?? 1;  // default 1 kalau takde
    $total += $price * $pieces;
}

// Insert order ke database
$order_sql = "INSERT INTO orders (customerID, customerName, customerPhone, totalAmount)
              VALUES ('$customerID', '$customerName', '$customerPhone', '$total')";
if(mysqli_query($conn, $order_sql)){
    $orderID = mysqli_insert_id($conn);

    // Masukkan detail setiap item
    foreach($_SESSION['cart'] as $item){
        $menuID = $item['menuID'] ?? 0;
        $size = $item['size'] ?? '';       // default kosong
        $pieces = $item['pieces'] ?? 1;    // default 1
        $price = $item['price'] ?? 0;

        $detail_sql = "INSERT INTO order_details (orderID, menuID, size, pieces, price)
                       VALUES ('$orderID', '$menuID', '$size', '$pieces', '$price')";
        mysqli_query($conn, $detail_sql);
    }

    // Kosongkan cart lepas checkout
    unset($_SESSION['cart']);
    echo "Order placed successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
