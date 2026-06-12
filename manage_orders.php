<?php
session_start();
include('db.php');

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

$staffID = $_SESSION['staffID'];
$staffRole = $_SESSION['staffRole'];
$staffName = $_SESSION['staffName'] ?? "Staff";

// Sambung ke DB
$conn = new mysqli("localhost", "root", "", "bitsy");
if ($conn->connect_error) { die("Connection failed: ".$conn->connect_error); }

// Ambil orders
if($staffRole === 'Baker'){
    $sql = "SELECT o.orderID, o.customerID, o.totalAmount, o.status, o.paymentMethod, o.orderDate, o.pickupDate,
                   c.customerName
            FROM orders o
            JOIN customer c ON o.customerID = c.customerID
            WHERE o.assignedTo = ?
            ORDER BY o.orderDate DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $staffID);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT o.orderID, o.customerID, o.totalAmount, o.status, o.paymentMethod, o.orderDate, o.pickupDate, o.assignedTo,
                   c.customerName, s.staffName AS assignedStaff
            FROM orders o
            JOIN customer c ON o.customerID = c.customerID
            LEFT JOIN staff s ON o.assignedTo = s.staffID
            ORDER BY o.orderDate DESC";
    $result = $conn->query($sql);
}
$orders = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Orders | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
/* ===== Header ===== */
header{
    background:#2e7d32;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 3px 8px rgba(0,0,0,0.2);
}
header .logo{display:flex;align-items:center;gap:12px;}
header .logo img{width:50px;height:50px;border-radius:50%;border:2px solid white;}
header .logo h1{font-family:'Pacifico',cursive;font-size:28px;margin:0;color:white;}
.user-box{position:relative;display:flex;align-items:center;gap:10px;cursor:pointer;}
.user-box img{width:40px;height:40px;border-radius:50%;border:2px solid white;}
.user-box span{font-weight:500;color:white;}
.dropdown{display:none;position:absolute;right:0;top:50px;background:white;min-width:180px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.15);}
.dropdown a,.dropdown button{color:#2e7d32;padding:10px 15px;display:block;text-align:left;background:none;border:none;width:100%;cursor:pointer;}
.dropdown a:hover,.dropdown button:hover{background:#f0f4f0;}

/* ===== Container & Sidebar ===== */
.container{display:flex;min-height:100vh;}
.sidebar{
    width:250px;
    background:#1b5e20;
    padding:25px 15px;
    color:white;
    position:sticky;
    top:0;
    height:100vh;
    box-shadow:3px 0 10px rgba(0,0,0,0.2);
}
.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 15px;
    margin:8px 0;
    background:rgba(255,255,255,0.07);
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-weight:500;
    transition:0.25s ease;
}
.sidebar a:hover{
    background:#2e7d32;
    transform:translateX(6px);
}
.submenu{display:none;margin-left:15px;}
.submenu a{
    background:rgba(255,255,255,0.15);
    font-size:14px;
    padding:10px 15px;
    border-radius:6px;
    transition:0.2s;
}
.submenu a:hover{background:#43a047;}
.main{flex:1;padding:40px;}
h2{color:#2e7d32;text-align:center;margin-bottom:20px;}

/* ===== Order Table ===== */
.order-table{width:100%;border-collapse:collapse;margin-top:20px;background:white;border-radius:12px;overflow:hidden;}
.order-table th, .order-table td{padding:12px;border:1px solid #ddd;text-align:center;}
.order-table th{background:#2e7d32;color:white;}
.status-pending{color:#e67e22;font-weight:bold;}
.status-inprocess{color:#f39c12;font-weight:bold;}
.status-completed{color:#27ae60;font-weight:bold;}
.status-cancelled{color:#e74c3c;font-weight:bold;}
.btn-update{padding:5px 10px;background:#3498db;color:white;border:none;border-radius:5px;cursor:pointer;}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy Staff</h1>
    </div>
    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="User">
        <span><?= htmlspecialchars($staffName) ?></span>
        <div class="dropdown" id="dropdownMenu">
            <a href="staff_profile.php">Profile</a>
            <form action="staff_logout.php" method="POST"><button type="submit">Logout</button></form>
        </div>
    </div>
</header>

<div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="staff_dashboard.php">🏠 Dashboard</a>
        <a href="manage_menu.php">🍰 Manage Menu</a>
        <a href="view_customers.php">👥 Customers</a>
        <?php if($staffRole==='Admin'): ?>
            <a href="assign_orders.php">📦 Assign Orders</a>
            <a href="staff_list.php">👩‍💼 Staff</a>
            <a href="sales_report.php">💹 Sales Report</a>
        <?php else: ?>
            
        <?php endif; ?>
        <a href="#" onclick="toggleStockMenu()">📦 Stocks ▾</a>
        <div class="submenu" id="stockSubMenu">
            <a href="manage_raw_material.php">🥖 Raw Material Stock</a>
            <a href="manage_product.php">🍰 Products Stock</a>
            <?php if($staffRole==='Baker'): ?>
                <a href="production_simple.php">🍰 Production Stock</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main">
        <h2>Manage Orders</h2>
        <table class="order-table">
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total (RM)</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Order Date</th>
                <th>Pickup Date</th>
                <?php if($staffRole==='Admin') echo "<th>Assigned To</th>"; ?>
                <?php if($staffRole==='Baker') echo "<th>Action</th>"; ?>
            </tr>
            <?php if(!empty($orders)): ?>
                <?php foreach($orders as $o):
                    $statusClass = "status-".strtolower(str_replace(" ","",$o['status']));
                ?>
                <tr>
                    <td><?= $o['orderID'] ?></td>
                    <td><?= htmlspecialchars($o['customerName']) ?></td>
                    <td><?= number_format($o['totalAmount'],2) ?></td>
                    <td class="<?= $statusClass ?>"><?= $o['status'] ?></td>
                    <td><?= htmlspecialchars($o['paymentMethod']) ?></td>
                    <td><?= htmlspecialchars($o['orderDate']) ?></td>
                    <td><?= ($o['pickupDate']=='0000-00-00')?'N/A':$o['pickupDate'] ?></td>
                    <?php if($staffRole==='Admin'): ?>
                        <td><?= htmlspecialchars($o['assignedStaff'] ?? 'Unassigned') ?></td>
                    <?php elseif($staffRole==='Baker'): ?>
                        <td>
                            <form method="POST" action="update_order_status.php" style="margin:0;">
                                <input type="hidden" name="orderID" value="<?= $o['orderID'] ?>">
                                <select name="status" required>
                                    <option value="">--Select--</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Process">In Process</option>
                                    <option value="Completed">Completed</option>
                                    <option value="Cancelled">Cancelled</option>
                                </select>
                                <button type="submit" class="btn-update">Update</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="<?= ($staffRole==='Admin')?8:7 ?>">No orders found.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<script>
function toggleStockMenu(){
    let m=document.getElementById("stockSubMenu");
    m.style.display = m.style.display==="block"?"none":"block";
}
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.addEventListener("click",e=>{dropdown.style.display=dropdown.style.display==="block"?"none":"block";e.stopPropagation();});
document.addEventListener("click",e=>{if(!userBox.contains(e.target)) dropdown.style.display="none";});
</script>

</body>
</html>
