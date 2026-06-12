<?php
session_start();

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

// Ambil nama dan role staff dari session
$staffName = $_SESSION['staffName'];
$staffRole = isset($_SESSION['staffRole']) ? $_SESSION['staffRole'] : ''; // <-- tambah ini

// Sambungan ke database
$conn = new mysqli("localhost", "root", "", "bitsy");
if ($conn->connect_error) { 
    die("Connection failed: " . $conn->connect_error); 
}

// Dapatkan semua raw material
$sql = "SELECT * FROM stock WHERE category = 'Raw Material'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Raw Materials - Bitsy Bakesy</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* RESET & BASE */
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
*{box-sizing:border-box;}

/* HEADER */
header{
    background:#2e7d32;
    color:white;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 3px 8px rgba(0,0,0,0.2);
    position:sticky; top:0; z-index:100;
}
header .logo{
    display:flex;
    align-items:center;
    gap:12px;
}
header .logo img{
    width:50px; height:50px;
    border-radius:50%; border:2px solid white;
    object-fit:cover;
    animation: floatLogo 4s ease-in-out infinite;
}
header .logo h1{
    font-family:'Pacifico', cursive;
    font-size:28px; margin:0;
}
.user-box{position:relative;display:flex;align-items:center;gap:10px;cursor:pointer;}
.user-box img{width:42px;height:42px;border-radius:50%;border:2px solid white;object-fit:cover;}
.user-box span{font-weight:500;}
.dropdown{display:none;position:absolute;right:0;top:50px;background:white;min-width:160px;border-radius:8px;box-shadow:0 4px 10px rgba(0,0,0,0.15);}
.dropdown a,.dropdown button{color:#2e7d32;padding:10px 15px;display:block;text-align:left;background:none;border:none;width:100%;cursor:pointer;}
.dropdown a:hover,.dropdown button:hover{background:#e8f5e9;}

/* ANIMATION */
@keyframes floatLogo{
    0%,100%{transform:translateY(0);}
    50%{transform:translateY(-6px);}
}

/* LAYOUT */
.container{display:flex;min-height:100vh;}

/* SIDEBAR */
.sidebar{
    width:250px;background:#1b5e20;padding:25px 15px;color:white;
    box-shadow:3px 0 10px rgba(0,0,0,0.2);position:sticky;top:70px;height:calc(100vh-70px);
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

/* MAIN */
.main{flex:1;padding:40px;background:#f4f6f5;}
.main h1{font-family:'Pacifico', cursive;color:#2e7d32;margin-bottom:20px;}
.actions{text-align:right;margin-bottom:20px;}
.actions a{display:inline-block;padding:8px 15px;color:white;background:#3498db;text-decoration:none;border-radius:5px;margin-left:5px;transition:0.2s;}
.actions a:hover{background:#2980b9;transform:scale(1.05);}

/* TABLE */
table{width:100%;border-collapse:collapse;background:white;box-shadow:0 4px 16px rgba(0,0,0,0.1);border-radius:12px;overflow:hidden;}
th,td{padding:12px;text-align:center;border-bottom:1px solid #ddd;}
th{background:#2e7d32;color:white;font-weight:500;}
tr:nth-child(even){background:#f2f2f2;}
.low-stock{color:#e74c3c;font-weight:bold;}
.ok-stock{color:#27ae60;}
.btn{display:inline-block;padding:6px 12px;color:white;text-decoration:none;border-radius:5px;margin:2px;transition:0.2s;}
.btn-restock{background:#27ae60;}
.btn-use{background:#e67e22;}
.btn:hover{opacity:0.9;transform:scale(1.05);}
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
    <div class="sidebar">
        <a href="staff_dashboard.php">📊 Dashboard</a>
        <a href="manage_menu.php">🍰 Manage Menu</a>
        <a href="manage_orders.php">📦 Manage Orders</a>
        <a href="view_customers.php">👥 Customers</a>
        <div class="menu-item">
            <a href="javascript:void(0)" onclick="toggleStockMenu()" class="stock-toggle">📦 Stocks</a>
            <div id="stockSubMenu" class="submenu">
                <a href="manage_product.php">🍰 Products Stock</a>
                <?php if($staffRole === 'Baker'): ?>
                    <a href="production_simple.php">🍰 Production Stock</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="main">
        <h1>🥖 Manage Raw Materials</h1>

        <div class="actions">
            <a href="add_stock.php">+ Add New Raw Material</a>
        </div>

        <table>
            <tr>
                <th>No</th>
                <th>Item Name</th>
                <th>Quantity</th>
                <th>Minimum Level</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php
            if ($result && $result->num_rows > 0) {
                $no = 1;
                while($row = $result->fetch_assoc()) {
                    $status = ($row['quantity'] < $row['minimum_level'])
                        ? "<span class='low-stock'>⚠️ Low Stock</span>"
                        : "<span class='ok-stock'>✅ Sufficient</span>";

                    echo "<tr>
                            <td>{$no}</td>
                            <td>{$row['item_name']}</td>
                            <td>{$row['quantity']}</td>
                            <td>{$row['minimum_level']}</td>
                            <td>$status</td>
                            <td>
                                <a href='update_stock.php?id={$row['id']}' class='btn btn-restock'>Restock</a>
                                <a href='stock_out.php?id={$row['id']}' class='btn btn-use'>Use Stock</a>
                            </td>
                          </tr>";
                    $no++;
                }
            } else {
                echo "<tr><td colspan='6'>No raw material data available.</td></tr>";
            }

            $conn->close();
            ?>
        </table>
    </div>
</div>

<script>
function toggleStockMenu() {
    const menu = document.getElementById('stockSubMenu');
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
}

// user dropdown
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.addEventListener("click",e=>{
    dropdown.style.display = dropdown.style.display==="block"?"none":"block";
    e.stopPropagation();
});
document.addEventListener("click",e=>{
    if(!userBox.contains(e.target)) dropdown.style.display="none";
});
</script>

</body>
</html>
