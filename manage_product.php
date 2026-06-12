<?php
session_start();
include('db.php');

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

$staffName = $_SESSION['staffName'];
$staffRole = isset($_SESSION['staffRole']) ? $_SESSION['staffRole'] : '';

// Ambil menu + unit
$sql = "
SELECT u.unitID, m.menuName, u.unitType, u.price, u.stockQuantity, u.minLevel
FROM menu m
LEFT JOIN menu_unit u ON m.menuID = u.menuID
ORDER BY m.menuID ASC, u.unitType ASC
";
$result = $conn->query($sql);
if(!$result) die("Query failed: " . $conn->error);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Products Stock | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
*{box-sizing:border-box;}

/* HEADER */
header{
    background:#2e7d32;color:white;padding:18px 30px;
    display:flex;justify-content:space-between;align-items:center;
    box-shadow:0 3px 8px rgba(0,0,0,0.2);
}
.logo{display:flex;align-items:center;gap:12px;}
.logo img{width:50px;height:50px;border-radius:50%;object-fit:cover;}
.logo h1{font-family:'Pacifico',cursive;font-size:28px;margin:0;}

/* CONTAINER & SIDEBAR */
.container{display:flex;}
.sidebar{
    width:250px;background:#1b5e20;padding:25px 15px;color:white;
    height:100vh;position:sticky;top:0;
}
.sidebar a{
    display:flex;align-items:center;gap:10px;padding:12px 15px;margin:8px 0;
    background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;
    transition:0.25s ease;
}
.sidebar a:hover{background:#2e7d32;transform:translateX(6px);}
.submenu{display:none;margin-left:15px;}
.submenu a{background:rgba(255,255,255,0.15);font-size:14px;}
.stock-toggle::after{content:" ▾";margin-left:auto;}

/* USER BOX */
.user-box{
    display:flex;align-items:center;gap:10px;position:relative;cursor:pointer;
}
.user-box img{width:42px;height:42px;border-radius:50%;border:2px solid white;}
.user-box span{font-weight:500;}
.dropdown{
    display:none;position:absolute;top:50px;right:0;background:white;
    min-width:160px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.15);
}
.dropdown a,.dropdown button{
    padding:10px 15px;display:block;width:100%;background:none;border:none;
    text-align:left;color:#2e7d32;cursor:pointer;
}
.dropdown a:hover,.dropdown button:hover{background:#e8f5e9;}

/* MAIN */
.main{flex:1;padding:40px;}
.main h1{font-family:'Pacifico',cursive;color:#2e7d32;margin-bottom:20px;}

/* TABLE */
table{width:100%;border-collapse:collapse;background:white;box-shadow:0 4px 16px rgba(0,0,0,0.1);border-radius:12px;overflow:hidden;}
th,td{padding:12px;text-align:center;border-bottom:1px solid #ddd;}
th{background:#2e7d32;color:white;font-weight:500;}
tr:nth-child(even){background:#f2f2f2;}
tr:hover{background:#e8f5e9;}
.low-stock{color:#e74c3c;font-weight:bold;}
.ok-stock{color:#27ae60;}
.btn{padding:6px 12px;border-radius:5px;color:white;text-decoration:none;margin:2px;display:inline-block;}
.btn-restock{background:#27ae60;}
.btn:hover{opacity:0.9;transform:scale(1.05);}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png">
        <h1>Bitsy Bakesy Staff</h1>
    </div>

    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png">
        <span><?= htmlspecialchars($staffName) ?></span>
        <div class="dropdown" id="dropdownMenu">
            <a href="staff_profile.php">Profile</a>
            <form action="staff_logout.php" method="POST"><button>Logout</button></form>
        </div>
    </div>
</header>

<div class="container">

<div class="sidebar">
    <a href="staff_dashboard.php">📊 Dashboard</a>
    <a href="manage_menu.php">🍰 Manage Menu</a>
    <a href="view_customers.php">👥 Customers</a>

    <a href="javascript:void(0)" class="stock-toggle" onclick="toggleStockMenu()">📦 Stocks</a>
    <div class="submenu" id="stockSubMenu">
        <a href="manage_raw_material.php">🥖 Raw Material Stock</a>
        
        <?php if($staffRole === 'Baker'): ?>
            <a href="production_simple.php">🍰 Production Stock</a>
        <?php endif; ?>
    </div>
</div>

<div class="main">
    <h1>🍰 Manage Products Stock</h1>

    <table>
        <tr>
            <th>No</th>
            <th>Menu Name</th>
            <th>Unit Type</th>
            <th>Price (RM)</th>
            <th>Stock</th>
            <th>Min Level</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        if($result->num_rows > 0){
            $no=1;
            while($row=$result->fetch_assoc()){
                $status = ($row['stockQuantity'] < $row['minLevel'])
                    ? "<span class='low-stock'>⚠️ Low Stock</span>"
                    : "<span class='ok-stock'>✅ Sufficient</span>";

                echo "<tr>
                    <td>$no</td>
                    <td>{$row['menuName']}</td>
                    <td>{$row['unitType']}</td>
                    <td>".number_format($row['price'],2)."</td>
                    <td>{$row['stockQuantity']}</td>
                    <td>{$row['minLevel']}</td>
                    <td>$status</td>
                    <td><a class='btn btn-restock' href='update_stock_menu.php?id={$row['unitID']}'>Restock</a></td>
                </tr>";
                $no++;
            }
        } else {
            echo "<tr><td colspan='8'>No products found.</td></tr>";
        }
        ?>
    </table>
</div>
</div>

<script>
function toggleStockMenu(){
    const sub=document.getElementById("stockSubMenu");
    sub.style.display = sub.style.display==="block"?"none":"block";
}

// User dropdown
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.addEventListener("click", e=>{
    dropdown.style.display = dropdown.style.display==="block"?"none":"block";
    e.stopPropagation();
});
document.addEventListener("click", e=>{
    if(!userBox.contains(e.target)) dropdown.style.display="none";
});
</script>

</body>
</html>
