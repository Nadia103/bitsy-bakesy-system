<?php
session_start();

// Admin only
if (!isset($_SESSION['staffID']) || $_SESSION['staffRole'] !== 'Admin') {
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }

$staffName = $_SESSION['staffName'];
$reportType = $_GET['type'] ?? 'daily';

/* ================= CHART DATA & TOTAL STATS ================= */
$labels = [];
$data = [];
$totalOrders = 0;
$totalSales = 0;

if ($reportType == 'daily') {
    $chartQuery = "
        SELECT DATE(orderDate) AS label, SUM(totalAmount) AS total, COUNT(*) AS orders
        FROM orders
        WHERE DATE(orderDate) = CURDATE()
        GROUP BY DATE(orderDate)
        ORDER BY DATE(orderDate)
    ";
    $totalQuery = "
        SELECT COUNT(*) AS totalOrders, SUM(totalAmount) AS totalSales
        FROM orders
        WHERE DATE(orderDate) = CURDATE()
    ";
} elseif ($reportType == 'monthly') {
    $chartQuery = "
        SELECT DATE_FORMAT(orderDate,'%Y-%m') AS label, SUM(totalAmount) AS total, COUNT(*) AS orders
        FROM orders
        GROUP BY YEAR(orderDate), MONTH(orderDate)
        ORDER BY YEAR(orderDate), MONTH(orderDate)
    ";
    $totalQuery = "
        SELECT COUNT(*) AS totalOrders, SUM(totalAmount) AS totalSales
        FROM orders
        WHERE YEAR(orderDate) = YEAR(CURDATE()) AND MONTH(orderDate) = MONTH(CURDATE())
    ";
} else { // yearly
    $chartQuery = "
        SELECT YEAR(orderDate) AS label, SUM(totalAmount) AS total, COUNT(*) AS orders
        FROM orders
        GROUP BY YEAR(orderDate)
        ORDER BY YEAR(orderDate)
    ";
    $totalQuery = "
        SELECT COUNT(*) AS totalOrders, SUM(totalAmount) AS totalSales
        FROM orders
        WHERE YEAR(orderDate) = YEAR(CURDATE())
    ";
}

$chartRes = mysqli_query($conn, $chartQuery);
while ($row = mysqli_fetch_assoc($chartRes)) {
    $labels[] = $row['label'];
    $data[] = $row['total'];
}

$totalRes = mysqli_query($conn, $totalQuery);
$totalStats = mysqli_fetch_assoc($totalRes);
$totalOrders = $totalStats['totalOrders'] ?? 0;
$totalSales = $totalStats['totalSales'] ?? 0;

$tableQuery = "";
if($reportType == 'daily'){
    $tableQuery = "
    SELECT o.orderID, o.totalAmount, o.status, o.paymentMethod, o.orderDate, o.pickupDate, c.customerName
    FROM orders o
    JOIN customer c ON o.customerID = c.customerID
    WHERE DATE(o.orderDate) = CURDATE()
    ORDER BY o.orderDate DESC
    ";
} elseif($reportType == 'monthly'){
    $tableQuery = "
    SELECT o.orderID, o.totalAmount, o.status, o.paymentMethod, o.orderDate, o.pickupDate, c.customerName
    FROM orders o
    JOIN customer c ON o.customerID = c.customerID
    WHERE YEAR(o.orderDate) = YEAR(CURDATE()) AND MONTH(o.orderDate) = MONTH(CURDATE())
    ORDER BY o.orderDate DESC
    ";
} else { // yearly
    $tableQuery = "
    SELECT o.orderID, o.totalAmount, o.status, o.paymentMethod, o.orderDate, o.pickupDate, c.customerName
    FROM orders o
    JOIN customer c ON o.customerID = c.customerID
    WHERE YEAR(o.orderDate) = YEAR(CURDATE())
    ORDER BY o.orderDate DESC
    ";
}

$tableResult = mysqli_query($conn, $tableQuery);
$orders = mysqli_fetch_all($tableResult, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Report | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f4f6f5; margin:0; }

header{
    background:#2e7d32;color:white;padding:15px 25px;
    display:flex;justify-content:space-between;align-items:center;
}
header .logo { display:flex; align-items:center; gap:12px; }
header .logo img{ width:50px; height:50px; border-radius:50%; object-fit:cover; }
header h1{ font-family:'Pacifico',cursive;margin:0;font-size:28px;color:white; }

/* User box */
.user-box{ display:flex; align-items:center; gap:10px; position:relative; cursor:pointer; }
.user-box .user-img{ width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid white; transition:0.2s; }
.user-box:hover .user-img{ transform:scale(1.1); }
.user-box span{ color:white; font-weight:500; user-select:none; }
.dropdown{ display:none; position:absolute; top:50px; right:0; background:white; border-radius:10px; box-shadow:0 6px 20px rgba(0,0,0,0.15); min-width:160px; overflow:hidden; z-index:100; }
.dropdown a, .dropdown button{ display:block; padding:10px 15px; width:100%; text-align:left; border:none; background:none; cursor:pointer; color:#2e7d32; text-decoration:none; transition:0.2s; }
.dropdown a:hover, .dropdown button:hover{ background:#f0f4f0; }

/* Layout */
.container{ display:flex; min-height:100vh; }
.sidebar{ width:250px; background:#1b5e20; color:white; padding:25px 15px; font-family:'Poppins',sans-serif; }
.sidebar a{ display:block; padding:12px 15px; margin:8px 0; color:white; text-decoration:none; background:rgba(255,255,255,0.07); border-radius:8px; font-weight:500; transition:0.25s ease; font-family:'Poppins',sans-serif; }
.sidebar a.active,.sidebar a:hover{ background:#2e7d32; transform:translateX(6px); }

.main{ flex:1; padding:30px; }
.filter{ margin-bottom:20px; }
select{ padding:8px; border-radius:6px; }

.stats{ display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:20px; }
.card{ background:white; padding:20px; border-radius:16px; box-shadow:0 6px 16px rgba(0,0,0,0.1); }
.card h3{ margin:0; color:#1b5e20; }
.card p{ font-size:26px; font-weight:bold; margin-top:10px; }

.order-table{ width:100%; border-collapse:collapse; margin-top:30px; background:white; }
.order-table th,.order-table td{ padding:12px; border:1px solid #ddd; text-align:center; }
.order-table th{ background:#2e7d32; color:white; }
tr:hover{ background:#f1f1f1; }

button, .btn{ padding:6px 12px; border:none; border-radius:5px; cursor:pointer; text-decoration:none; color:white; }
.btn-update{ background:#2e7d32; } .btn-update:hover{ background:#43a047; }
.btn-delete{ background:#c62828; } .btn-delete:hover{ background:#e53935; }

@media(max-width:900px){
    .container{ flex-direction:column; }
    .sidebar{ width:100%; display:flex; overflow-x:auto; }
    .sidebar a{ flex:1; white-space:nowrap; }
}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy Admin</h1>
    </div>
    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" class="user-img">
        <span><?= htmlspecialchars($staffName) ?></span>
        <div class="dropdown" id="dropdownMenu">
            <a href="profile.php">Profile</a>
            <form method="POST" action="staff_logout.php"><button type="submit">Logout</button></form>
        </div>
    </div>
</header>

<div class="container">

<!-- Sidebar -->
<div class="sidebar">
    <a href="admin_dashboard.php">📊 Dashboard</a>
    <a href="assign_orders.php">📦 Assign Orders</a>
    <a href="view_customers.php">👥 Customers</a>
    <a href="staff_list.php">👩‍💼 Staff</a>
    <a href="sales_report.php" class="active">💹 Sales Report</a>
</div>

<!-- Main -->
<div class="main">
<form method="GET" class="filter">
    <label>Report Type:
        <select name="type" onchange="this.form.submit()">
            <option value="daily" <?= $reportType=='daily'?'selected':'' ?>>Daily</option>
            <option value="monthly" <?= $reportType=='monthly'?'selected':'' ?>>Monthly</option>
            <option value="yearly" <?= $reportType=='yearly'?'selected':'' ?>>Yearly</option>
        </select>
    </label>
</form>

<div class="stats">
    <div class="card">
        <h3>Total Orders</h3>
        <p><?= $totalOrders ?></p>
    </div>
    <div class="card">
        <h3>Total Sales (RM)</h3>
        <p>RM<?= number_format($totalSales,2) ?></p>
    </div>
</div>

<div class="card" style="margin-top:30px;">
    <h3>Sales Trend</h3>
    <canvas id="salesChart"></canvas>
</div>

<table class="order-table">
<tr>
    <th>Order ID</th>
    <th>Customer</th>
    <th>Total (RM)</th>
    <th>Status</th>
    <th>Payment</th>
    <th>Order Date</th>
    <th>Pickup Date</th>
</tr>
<?php if($orders): foreach($orders as $o): ?>
<tr>
    <td><?= $o['orderID'] ?></td>
    <td><?= htmlspecialchars($o['customerName']) ?></td>
    <td><?= number_format($o['totalAmount'],2) ?></td>
    <td><?= $o['status'] ?></td>
    <td><?= $o['paymentMethod'] ?></td>
    <td><?= $o['orderDate'] ?></td>
    <td><?= ($o['pickupDate']=='0000-00-00')?'N/A':$o['pickupDate'] ?></td>
</tr>
<?php endforeach; else: ?>
<tr><td colspan="7">No data found</td></tr>
<?php endif; ?>
</table>

</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('salesChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Sales (RM)',
            data: <?= json_encode($data) ?>,
            backgroundColor: 'rgba(46,125,50,0.7)',
            borderColor: 'rgba(46,125,50,1)',
            borderWidth: 1
        }]
    },
    options: { responsive:true, scales:{ y:{ beginAtZero:true } } }
});

const userBox = document.getElementById("userBox");
const dropdown = document.getElementById("dropdownMenu");
userBox.addEventListener("click", e => { dropdown.style.display = dropdown.style.display==="block"?"none":"block"; e.stopPropagation(); });
document.addEventListener("click", e => { if(!userBox.contains(e.target)) dropdown.style.display="none"; });
</script>

</body>
</html>
