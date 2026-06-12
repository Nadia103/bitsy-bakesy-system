<?php
session_start();

// Pastikan admin login
if (!isset($_SESSION['staffID']) || $_SESSION['staffRole'] !== 'Admin') {
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) { 
    die("Connection failed: " . mysqli_connect_error()); 
}

$staffName = $_SESSION['staffName'];

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

$salesQuery = "SELECT SUM(totalAmount) AS totalSales FROM orders";
$salesRes = mysqli_query($conn, $salesQuery);
$totalSales = $salesRes ? mysqli_fetch_assoc($salesRes)['totalSales'] ?? 0 : 0;

// --- Ambil semua orders ---
$orderQuery = "
SELECT o.orderID, o.customerID, o.totalAmount, o.status, o.paymentMethod, o.orderDate, o.pickupDate, o.assignedTo,
       c.customerName, s.staffName AS assignedStaff
FROM orders o
JOIN customer c ON o.customerID = c.customerID
LEFT JOIN staff s ON o.assignedTo = s.staffID
ORDER BY o.orderDate DESC";
$orderResult = mysqli_query($conn, $orderQuery);
$orders = $orderResult ? mysqli_fetch_all($orderResult, MYSQLI_ASSOC) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
header{background:#2e7d32;color:white;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 3px 8px rgba(0,0,0,0.2);}
header .logo{display:flex;align-items:center;gap:12px;}
header .logo img{width:50px;height:50px;border-radius:50%;border:2px solid white;}
header .logo h1{font-family:'Pacifico',cursive;font-size:28px;margin:0;}
.user-box{position:relative;display:flex;align-items:center;gap:10px;cursor:pointer;}
.user-box img{width:42px;height:42px;border-radius:50%;border:2px solid white;}
.user-box span{font-weight:500;}
.dropdown{display:none;position:absolute;right:0;top:50px;background:white;min-width:160px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.15);}
.dropdown a,.dropdown button{color:#2e7d32;padding:10px 15px;display:block;text-align:left;background:none;border:none;width:100%;cursor:pointer;}
.dropdown a:hover,.dropdown button:hover{background:#e8f5e9;}
.container{display:flex;min-height:100vh;}
.sidebar{width:250px;background:#1b5e20;padding:25px 15px;color:white;box-shadow:3px 0 10px rgba(0,0,0,0.2);}
.sidebar a{display:flex;align-items:center;gap:10px;padding:12px 15px;margin:8px 0;background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;transition:0.25s ease;}
.sidebar a:hover{background:#2e7d32;}
.main{flex:1;padding:40px;}
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:25px;
    margin-bottom:30px;
}
.card{
    background:white;
    padding:20px 25px;
    border-radius:20px;
    text-align:center;
    box-shadow:0 6px 18px rgba(0,0,0,0.12);
    transition:transform 0.3s ease, box-shadow 0.3s ease;
}
.card:hover{
    transform:translateY(-5px);
    box-shadow:0 10px 22px rgba(0,0,0,0.18);
}
.card-icon{
    font-size:32px;
    margin-bottom:10px;
}
.card h3{
    margin:5px 0;
    font-size:18px;
    color:#1b5e20;
}
.card p{
    font-size:28px;
    font-weight:bold;
    margin:0;
    color:#333;
}
/* Color variants */
.green-card{background:#e8f5e9;}
.orange-card{background:#fff3e0;}
.blue-card{background:#e3f2fd;}
.order-table{width:100%;border-collapse:collapse;margin-top:20px;background:white;border-radius:12px;overflow:hidden;}
.order-table th, .order-table td{padding:12px;border:1px solid #ddd;text-align:center;}
.order-table th{background:#2e7d32;color:white;}
.pending{background:#fff3e0;font-weight:bold;}
.assigned{background:#e8f5e9;}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy Admin</h1>
    </div>
    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="User">
        <span><?= htmlspecialchars($staffName) ?></span>
        <div class="dropdown" id="dropdownMenu">
            <a href="profile.php">Profile</a>
            <form action="staff_logout.php" method="POST"><button type="submit">Logout</button></form>
        </div>
    </div>
</header>

<div class="container">
    <div class="sidebar">
        <a href="admin_dashboard.php">📊 Dashboard</a>
        <a href="assign_orders.php">📦 Assign Orders</a>
        <a href="view_customers.php">👥 Customers</a>
        <a href="staff_list.php">👩‍💼 Staff</a>
        <a href="sales_report.php">💹 Sales Report</a>
    </div>

    <div class="main">
        <div class="stats">
            <div class="card green-card">
                <div class="card-icon">📦</div>
                <h3>Today's Orders</h3>
                <p><?= $orderTodayCount ?></p>
            </div>
            <div class="card orange-card">
                <div class="card-icon">⏳</div>
                <h3>Pending Orders</h3>
                <p><?= $pendingCount ?></p>
            </div>
            <div class="card blue-card">
                <div class="card-icon">📝</div>
                <h3>Total Orders</h3>
                <p><?= $totalOrders ?></p>
            </div>
            <div class="card green-card">
                <div class="card-icon">💰</div>
                <h3>Total Sales (RM)</h3>
                <p>RM<?= number_format($totalSales,2) ?></p>
            </div>
        </div>

        <h2 style="margin-top:40px;">All Orders</h2>
        <table class="order-table">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Order Date</th>
                <th>Pickup Date</th>
                <th>Assigned To</th>
            </tr>
            <?php if(!empty($orders)): ?>
                <?php foreach($orders as $o): ?>
                    <?php 
                        $rowClass = ($o['status']=='Pending') ? 'pending' : '';
                        $assigned = $o['assignedStaff'] ?? 'Unassigned';
                    ?>
                    <tr class="<?= $rowClass ?>">
                        <td><?= $o['orderID'] ?></td>
                        <td><?= htmlspecialchars($o['customerName']) ?></td>
                        <td>RM<?= number_format($o['totalAmount'],2) ?></td>
                        <td><?= $o['status'] ?></td>
                        <td><?= $o['paymentMethod'] ?></td>
                        <td><?= $o['orderDate'] ?></td>
                        <td><?= ($o['pickupDate']=='0000-00-00')?'N/A':$o['pickupDate'] ?></td>
                        <td><?= htmlspecialchars($assigned) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8">No orders found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.addEventListener("click",e=>{dropdown.style.display=dropdown.style.display==="block"?"none":"block";e.stopPropagation();});
document.addEventListener("click",e=>{if(!userBox.contains(e.target)) dropdown.style.display="none";});
</script>

</body>
</html>
