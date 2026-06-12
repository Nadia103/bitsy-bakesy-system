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

/* ===== GET BAKERS ===== */
$bakers = mysqli_fetch_all(
    mysqli_query($conn,"SELECT staffID, staffName FROM staff WHERE staffRole='Baker' ORDER BY staffName"),
    MYSQLI_ASSOC
);

/* ===== ASSIGN ORDER ===== */
$success = $error = "";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $orderID = $_POST['orderID'] ?? '';
    $bakerID = $_POST['bakerID'] ?? '';

    if($orderID && $bakerID){
        $stmt = $conn->prepare("UPDATE orders SET assignedTo=? WHERE orderID=?");
        $stmt->bind_param("ii",$bakerID,$orderID);
        if($stmt->execute()){
            $success = "Order #$orderID assigned successfully.";
        } else {
            $error = "Failed to assign order.";
        }
        $stmt->close();
    } else {
        $error = "Please select a baker.";
    }
}

/* ===== PENDING (UNASSIGNED) ===== */
$pendingOrders = mysqli_fetch_all(mysqli_query($conn,"SELECT o.orderID, o.totalAmount, o.pickupDate, c.customerName
    FROM orders o
    JOIN customer c ON o.customerID=c.customerID
    WHERE o.status='Pending' AND o.assignedTo IS NULL
    ORDER BY o.orderDate ASC"), MYSQLI_ASSOC);

/* ===== ASSIGNED ===== */
$assignedOrders = mysqli_fetch_all(mysqli_query($conn,"SELECT o.orderID, o.totalAmount, o.pickupDate, c.customerName, s.staffName AS baker
    FROM orders o
    JOIN customer c ON o.customerID=c.customerID
    JOIN staff s ON o.assignedTo=s.staffID
    WHERE o.status='Pending'
    ORDER BY o.orderDate ASC"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Assign Orders | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
/* ===== Header ===== */
header{
    background:#2e7d32;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;
    box-shadow:0 3px 8px rgba(0,0,0,0.2);
}
header .logo{display:flex;align-items:center;gap:12px;}
header .logo img{width:50px;height:50px;border-radius:50%;border:2px solid white;}
header .logo h1{font-family:'Pacifico',cursive;margin:0;font-size:28px;color:white;}

.user-box{position:relative;display:flex;align-items:center;gap:10px;cursor:pointer;}
.user-box img{width:40px;height:40px;border-radius:50%;border:2px solid white;}
.user-box span{font-weight:500;color:white;}
.dropdown{display:none;position:absolute;right:0;top:50px;background:white;min-width:180px;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.15);}
.dropdown a,.dropdown button{padding:10px 15px;display:block;width:100%;border:none;background:none;text-align:left;color:#2e7d32;cursor:pointer;}
.dropdown a:hover,.dropdown button:hover{background:#f0f4f0;}

/* ===== Container & Sidebar ===== */
.container{display:flex;min-height:100vh;}
.sidebar{
    width:250px;background:#1b5e20;padding:25px 15px;color:white;box-shadow:3px 0 10px rgba(0,0,0,0.2);
    font-family:'Poppins',sans-serif;
}
.sidebar a{
    display:flex;align-items:center;gap:10px;
    padding:12px 15px;margin:8px 0;color:white;text-decoration:none;
    background:rgba(255,255,255,0.07);border-radius:8px;font-weight:500;
    transition:0.25s ease;
}
.sidebar a:hover,.sidebar a.active{background:#2e7d32;transform:translateX(6px);}
.submenu{display:none;margin-left:15px;}
.submenu a{
    background:rgba(255,255,255,0.15);
    font-size:14px;padding:10px 15px;border-radius:6px;transition:0.2s;
}
.submenu a:hover{background:#43a047;}

.main{flex:1;padding:40px;}
h2{color:#2e7d32;text-align:center;margin-bottom:20px;}

/* ===== Cards ===== */
.card{
    background:white;padding:20px;border-radius:16px;
    box-shadow:0 6px 16px rgba(0,0,0,0.1);
    margin-bottom:30px;
}

/* ===== Table ===== */
table{width:100%;border-collapse:collapse;margin-top:15px;}
th,td{padding:12px;border:1px solid #ddd;text-align:center;}
th{background:#2e7d32;color:white;}
select,button{padding:8px;border-radius:6px;border:1px solid #ccc;}
button{background:#2e7d32;color:white;border:none;cursor:pointer;}
button:hover{background:#1b5e20;}

.success{color:green;margin-bottom:15px;}
.error{color:red;margin-bottom:15px;}
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
            <form action="staff_logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <a href="admin_dashboard.php">📊 Dashboard</a>
        <a href="assign_orders.php" class="active">📦 Assign Orders</a>
        <a href="view_customers.php">👥 Customers</a>
        <a href="staff_list.php">👩‍💼 Staff</a>
        <a href="sales_report.php">💹 Sales Report</a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>

        <!-- PENDING -->
        <div class="card">
            <h2>📦 Pending & Unassigned Orders</h2>
            <?php if(!$pendingOrders): ?>
                <p>No pending orders.</p>
            <?php else: ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total (RM)</th>
                    <th>Pickup Date</th>
                    <th>Assign Baker</th>
                </tr>
                <?php foreach($pendingOrders as $o): ?>
                <tr>
                    <td><?= $o['orderID'] ?></td>
                    <td><?= htmlspecialchars($o['customerName']) ?></td>
                    <td><?= number_format($o['totalAmount'],2) ?></td>
                    <td><?= $o['pickupDate']=='0000-00-00'?'N/A':$o['pickupDate'] ?></td>
                    <td>
                        <form method="POST" style="display:flex;gap:6px;justify-content:center;">
                            <input type="hidden" name="orderID" value="<?= $o['orderID'] ?>">
                            <select name="bakerID" required>
                                <option value="">Select</option>
                                <?php foreach($bakers as $b): ?>
                                <option value="<?= $b['staffID'] ?>"><?= htmlspecialchars($b['staffName']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button>Assign</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>

        <!-- ASSIGNED -->
        <div class="card">
            <h2>✅ Already Assigned Orders</h2>
            <?php if(!$assignedOrders): ?>
                <p>No assigned orders.</p>
            <?php else: ?>
            <table>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total (RM)</th>
                    <th>Pickup Date</th>
                    <th>Baker</th>
                </tr>
                <?php foreach($assignedOrders as $a): ?>
                <tr>
                    <td><?= $a['orderID'] ?></td>
                    <td><?= htmlspecialchars($a['customerName']) ?></td>
                    <td><?= number_format($a['totalAmount'],2) ?></td>
                    <td><?= $a['pickupDate']=='0000-00-00'?'N/A':$a['pickupDate'] ?></td>
                    <td><?= htmlspecialchars($a['baker']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.addEventListener("click",e=>{
    dropdown.style.display=dropdown.style.display==="block"?"none":"block";
    e.stopPropagation();
});
document.addEventListener("click",e=>{
    if(!userBox.contains(e.target)) dropdown.style.display="none";
});
</script>

</body>
</html>
