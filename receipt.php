<?php
session_start();
$conn = mysqli_connect("localhost","root","","bitsy");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$orderID = $_GET['orderID'] ?? 0;
if(!$orderID){
    echo "Invalid order.";
    exit();
}

// Ambil order utama
$order_sql = "SELECT * FROM orders WHERE orderID = '$orderID'";
$order_res = mysqli_query($conn, $order_sql);
if(!$order_res){
    die("Query Error: " . mysqli_error($conn));
}
if(mysqli_num_rows($order_res) == 0){
    echo "Order not found.";
    exit();
}
$order = mysqli_fetch_assoc($order_res);

// Display walk-in if no customer
$customerDisplay = ($order['customerID'] == 0) ? "Walk-in Customer" : $order['customerID'];

// Ambil semua items untuk order ni
$item_sql = "
SELECT mn.menuName, oi.quantity, oi.unitType, oi.price
FROM order_items oi
JOIN menu_unit mu ON oi.unitID = mu.unitID
JOIN menu mn ON mu.menuID = mn.menuID
WHERE oi.orderID = '$orderID'
";
$item_res = mysqli_query($conn, $item_sql);
$items = [];
if($item_res){
    while($row = mysqli_fetch_assoc($item_res)){
        $items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receipt - Bitsy Bakesy</title>
<style>
body {
    font-family: "Courier New", monospace;
    background:#e5e5e5;
    margin:0;
    display:flex;
    justify-content:center;
    padding-top:40px;
}

.receipt {
    width:320px;
    background:white;
    padding:20px;
    border-radius:6px;
    border:1px dashed #555;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

.header {
    text-align:center;
    margin-bottom:10px;
}

.header h2 { margin:0; font-size:20px; letter-spacing:1px; }
.line { border-bottom:1px dashed #999; margin:10px 0; }
.info-row { display:flex; justify-content:space-between; font-size:14px; margin:5px 0; }
.total { font-weight:bold; margin-top:10px; font-size:16px; }
.center { text-align:center; margin-top:15px; font-size:13px; color:#666; }
.items { margin-top:10px; }
.items li { margin-bottom:5px; }

/*** SMALL BUTTON STYLE ***/
.btn-container { text-align:center; margin-top:15px; }
.print-btn { display:inline-block; width:auto; padding:6px 12px; background:#21618C; color:white; border:none; cursor:pointer; font-size:13px; border-radius:4px; margin-right:6px; }
.print-btn:hover { opacity:0.85; }
.back-btn { display:inline-block; padding:6px 12px; background:black; color:white; text-decoration:none; font-size:13px; border-radius:4px; }
.back-btn:hover { opacity:0.85; }

/*** HIDE BUTTONS WHEN PRINTING ***/
@media print {
    .print-btn, .back-btn { display:none !important; }
    body { background:white !important; padding:0 !important; }
}
</style>
</head>
<body>

<div class="receipt">
    <div class="header">
        <h2>Bitsy Bakesy</h2>
        <div style="font-size:12px;">Gong Limau, Kemaman</div>
        <div style="font-size:12px;">Tel: +6019-452 5676</div>
    </div>

    <div class="line"></div>

    <div class="info-row">
        <span>Order ID</span>
        <span>#<?php echo $order['orderID']; ?></span>
    </div>

    <div class="info-row">
        <span>Customer</span>
        <span><?php echo $customerDisplay; ?></span>
    </div>

    <div class="info-row">
        <span>Status</span>
        <span><?php echo $order['status']; ?></span>
    </div>

    <div class="info-row">
        <span>Payment</span>
        <span><?php echo $order['paymentMethod']; ?></span>
    </div>

    <div class="info-row">
        <span>Date</span>
        <span><?php echo $order['orderDate']; ?></span>
    </div>

    <div class="line"></div>

    <!-- Items List -->
    <ul class="items">
        <?php foreach($items as $item): ?>
        <li>
            <?php echo htmlspecialchars($item['menuName']); ?> x<?php echo $item['quantity']; ?>
            <?php if($item['unitType']) echo " ({$item['unitType']})"; ?>
        </li>
        <?php endforeach; ?>
    </ul>

    <div class="info-row total">
        <span>Total</span>
        <span>RM <?php echo number_format($order['totalAmount'],2); ?></span>
    </div>

    <div class="center">Thank you for your order!</div>
</div>

<div class="btn-container">
    <button onclick="window.print()" class="print-btn">Print</button>
    <a href="index.php" class="back-btn">Back</a>
</div>

</body>
</html>
