<?php
session_start();

// Lock access: Cashier only
if(!isset($_SESSION['staffRole']) || $_SESSION['staffRole'] != 'Cashier'){
    header("Location: staff_login.php");
    exit();
}

$staffID = $_SESSION['staffID'];
$staffName = $_SESSION['staffName'];

$conn = mysqli_connect("localhost","root","","bitsy");
if(!$conn){ die("Connection failed: ".mysqli_connect_error()); }

// --- STATS ---
$orderTodayQuery = "SELECT COUNT(*) AS total FROM orders WHERE DATE(orderDate) = CURDATE()";
$orderTodayRes = mysqli_query($conn, $orderTodayQuery);
$orderTodayCount = $orderTodayRes ? mysqli_fetch_assoc($orderTodayRes)['total'] : 0;

$pendingQuery = "SELECT COUNT(*) AS total FROM orders WHERE status='Pending'";
$pendingRes = mysqli_query($conn, $pendingQuery);
$pendingCount = $pendingRes ? mysqli_fetch_assoc($pendingRes)['total'] : 0;

$totalQuery = "SELECT COUNT(*) AS total FROM orders";
$totalRes = mysqli_query($conn, $totalQuery);
$totalOrders = $totalRes ? mysqli_fetch_assoc($totalRes)['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cashier Dashboard | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
header{background:#2e7d32;color:white;padding:15px 30px;display:flex;justify-content:space-between;align-items:center;}
header h1{font-family:'Pacifico',cursive;}
.container{padding:30px;}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:25px;margin-bottom:30px;}
.card{background:white;padding:25px;border-radius:18px;text-align:center;box-shadow:0 4px 16px rgba(0,0,0,0.15);transition:0.3s ease;}
.card:hover{transform:translateY(-6px);box-shadow:0 8px 24px rgba(0,0,0,0.25);}
.card h3{margin:0;font-size:20px;color:#1b5e20;}
.card p{font-size:32px;margin:10px 0 0;font-weight:bold;}
.pos-link{display:inline-block;padding:12px 20px;background:#388e3c;color:white;border-radius:12px;text-decoration:none;font-weight:bold;transition:0.3s;}
.pos-link:hover{background:#2e7d32;}
</style>
</head>
<body>

<header>
    <h1>Bitsy Bakesy</h1>
    <div>Cashier: <?= htmlspecialchars($staffName) ?></div>
</header>

<div class="container">
    <div class="stats">
        <div class="card">
            <h3>Today's Orders</h3>
            <p><?= $orderTodayCount ?></p>
        </div>
        <div class="card">
            <h3>Pending Orders</h3>
            <p><?= $pendingCount ?></p>
        </div>
        <div class="card">
            <h3>Total Orders</h3>
            <p><?= $totalOrders ?></p>
        </div>
    </div>

    <a href="pos.php" class="pos-link">💳 Open POS</a>
</div>

</body>
</html>
