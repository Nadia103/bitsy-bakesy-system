<?php
session_start();
include('db.php');

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

$staffID   = $_SESSION['staffID'];
$staffName = $_SESSION['staffName'];
$staffRole = $_SESSION['staffRole']; // Admin / Baker / Cashier

// Ambil semua customer
$sql = "SELECT * FROM customer ORDER BY customerName ASC";
$result = $conn->query($sql);
if(!$result) die("Query failed: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>View Customers - Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; margin:0; background:#f4f6f5; color:#333; }
/* HEADER */
header{ background:#2e7d32; color:white; padding:18px 40px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 3px 8px rgba(0,0,0,0.2); position:sticky; top:0; z-index:100;}
header .logo{ display:flex; align-items:center; gap:15px; }
header .logo img{ width:50px; height:50px; border-radius:50%; border:2px solid white; object-fit:cover;}
header .logo h1{ font-family:'Pacifico', cursive; font-size:28px; margin:0;}
.user-dropdown{ position:relative; display:inline-block;}
.user-btn{ background:none; border:none; color:white; cursor:pointer; font-size:16px; display:flex; align-items:center; gap:5px;}
.user-btn img{ width:30px; height:30px; border-radius:50%; border:2px solid white; object-fit:cover;}
.dropdown-content{ display:none; position:absolute; right:0; background:white; color:#333; min-width:150px; box-shadow:0 4px 8px rgba(0,0,0,0.2); border-radius:6px; overflow:hidden; z-index:1000;}
.dropdown-content.show{ display:block; }
.dropdown-content a, .dropdown-content button{ color:#333; padding:10px 15px; display:block; text-decoration:none; background:none; border:none; width:100%; text-align:left; cursor:pointer;}
.dropdown-content a:hover, .dropdown-content button:hover{ background:#f0f4f0;}
.container{ display:flex; min-height:100vh;}
.sidebar{ width:250px; background:#1b5e20; padding:25px 15px; color:white; box-shadow:3px 0 10px rgba(0,0,0,0.2); position:sticky; top:70px; height:calc(100vh - 70px);}
.sidebar a{ display:flex; align-items:center; gap:10px; padding:12px 15px; margin:8px 0; background:rgba(255,255,255,0.07); color:white; text-decoration:none; border-radius:8px; font-weight:500; transition:0.25s ease;}
.sidebar a:hover{ background:#2e7d32; transform:translateX(6px);}
.submenu{ display:none; margin-left:15px;}
.submenu a{ background:rgba(255,255,255,0.15); font-size:14px; padding-left:15px;}
.stock-toggle::after{ content:" ▾"; margin-left:auto;}
.main{ flex:1; padding:40px;}
.main h1{ font-size:28px; color:#1b5e20; margin-bottom:20px;}
table{ width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 4px 16px rgba(0,0,0,0.1);}
th, td{ padding:12px; text-align:center; border-bottom:1px solid #ddd;}
th{ background:#2e7d32; color:white;}
tr:nth-child(even){ background:#f0f4f0;}
tr:hover{ background:#e8f5e9;}
@media(max-width:900px){.container{flex-direction:column;}.sidebar{width:100%; height:auto; position:relative; display:flex; overflow-x:auto;}.sidebar a{flex:1; white-space:nowrap;}}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1><?= $staffRole === 'Admin' ? 'Bitsy Bakesy Admin' : 'Bitsy Bakesy Staff' ?></h1>
    </div>
    <div class="user-dropdown">
        <button class="user-btn" onclick="toggleDropdown()">
            <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="User Icon">
            <?= htmlspecialchars($staffName) ?> ▾
        </button>
        <div class="dropdown-content">
            <a href="staff_profile.php">Profile</a>
            <form action="staff_logout.php" method="POST" style="margin:0;">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">
    <div class="sidebar">
        <?php if($staffRole === 'Admin'): ?>
            <a href="admin_dashboard.php">📊 Dashboard</a>
            <a href="assign_orders.php">📦 Assign Orders</a>
            <a href="view_customers.php">👥 Customers</a>
            <a href="staff_list.php">👩‍💼 Staff</a>
            <a href="sales_report.php">💹 Sales Report</a>
        <?php else: ?>
            <a href="staff_dashboard.php">📊 Dashboard</a>
            <a href="manage_menu.php">🍰 Manage Menu</a>
            <?php if($staffRole === 'Baker'): ?>
                <a href="manage_orders.php">📦 Manage Orders</a>
            <?php endif; ?>

            <a href="javascript:void(0)" onclick="toggleStockMenu()" class="stock-toggle">📦 Stocks</a>
            <div id="stockSubMenu" class="submenu">
                <a href="manage_raw_material.php">🥖 Raw Material Stock</a>
                <a href="manage_product.php">🍰 Products Stock</a>
                <?php if($staffRole === 'Baker'): ?>
                    <a href="production_simple.php">🍰 Production Stock</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="main">
        <h1>👥 View Customers</h1>
        <table>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
            </tr>
            <?php
            if($result && $result->num_rows > 0){
                $no=1;
                while($row=$result->fetch_assoc()){
                    echo "<tr>
                            <td>{$no}</td>
                            <td>".htmlspecialchars($row['customerName'])."</td>
                            <td>".htmlspecialchars($row['customerEmail'])."</td>
                            <td>".htmlspecialchars($row['customerPhoneNo'])."</td>
                          </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='4'>No customers found.</td></tr>";
            }
            $conn->close();
            ?>
        </table>
    </div>
</div>

<script>
function toggleDropdown(){
    document.querySelector(".dropdown-content").classList.toggle("show");
}
window.onclick = function(event) {
    if (!event.target.matches('.user-btn')) {
        let dropdowns = document.getElementsByClassName("dropdown-content");
        for (let i=0;i<dropdowns.length;i++){
            dropdowns[i].classList.remove('show');
        }
    }
}
function toggleStockMenu() {
    const menu = document.getElementById('stockSubMenu');
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}
</script>

</body>
</html>
