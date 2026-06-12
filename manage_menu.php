<?php
session_start();
include('db.php');

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

$staffName = $_SESSION['staffName'];
$staffRole = $_SESSION['staffRole']; // Admin / Baker / Cashier

// Ambil semua menu + unitType + price
$query = "
    SELECT m.menuID, m.menuName, m.category, m.menuType, m.image, u.unitType, u.price
    FROM menu m
    LEFT JOIN menu_unit u ON m.menuID = u.menuID
    ORDER BY m.menuID ASC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Menu | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* ===== GLOBAL ===== */
body, h1, h2, h3, h4, h5, h6, p, a, td, th, button, input {
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    color: #333;
    margin:0;
    padding:0;
}

h1 {
    font-family: 'Pacifico', cursive;
    font-weight: normal; /* Sama dengan dashboard */
    font-size: 28px;
    color: white;        /* Header putih */
    margin:0;
    user-select:none;
    letter-spacing: 0px;
}

h2{color:#2e7d32;text-align:center;margin-bottom:20px;}

/* ===== Body & Layout ===== */
body{background:#f4f6f5;}
header{
    background:#2e7d32;
    color:white;
    padding:18px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 3px 8px rgba(0,0,0,0.2);
}
header .logo{display:flex;align-items:center;gap:12px;}
header .logo img{width:50px;height:50px;border-radius:50%;object-fit:cover;}

/* ===== User Box ===== */
.user-box{
    display:flex;
    align-items:center;
    gap:10px;
    position:relative;
    cursor:pointer;
}
.user-box .user-img{
    width:40px;
    height:40px;
    border-radius:50%;
    object-fit:cover;
    border:2px solid white;
    transition: transform 0.2s ease;
}
.user-box:hover .user-img{transform: scale(1.1);}
.user-box span{font-weight:500;color:white;user-select:none;}
.dropdown{
    display:none;
    position:absolute;
    top:50px;
    right:0;
    background:white;
    border-radius:10px;
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
    min-width:180px;
    z-index:100;
    overflow:hidden;
    font-size:14px;
}
.dropdown a, .dropdown button{
    display:block;
    padding:10px 15px;
    color:#2e7d32;
    text-decoration:none;
    width:100%;
    text-align:left;
    background:none;
    border:none;
    cursor:pointer;
    transition:0.2s ease;
}
.dropdown a:hover, .dropdown button:hover{background:#f0f4f0;}

/* ===== Layout ===== */
.container{display:flex;min-height:100vh;}
.sidebar{width:250px;background:#1b5e20;padding:25px 15px;color:white;position:sticky;top:0;height:100vh;}
.sidebar a{display:block;align-items:center;gap:10px;padding:12px 15px;margin:8px 0;background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;transition:0.25s ease;}
.sidebar a:hover{background:#2e7d32;transform:translateX(6px);}
.submenu{display:none;margin-left:15px;}
.submenu a{background:rgba(255,255,255,0.15);font-size:14px;}

.main{flex:1;padding:40px;}

/* ===== Add Menu Button ===== */
.add-menu-btn{
    display:inline-block;
    background:#2e7d32;
    color:white;
    padding:8px 15px;
    border-radius:5px;
    text-decoration:none;
    margin-bottom:15px;
    transition:0.3s;
}
.add-menu-btn:hover{background:#43a047;}

/* ===== Table ===== */
table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 3px 8px rgba(0,0,0,0.1);}
th, td { padding:12px; text-align:center; border-bottom:1px solid #ddd;}
th { background:#2e7d32; color:white;}
tr:hover { background:#f1f1f1; }
img { width:70px; height:70px; border-radius:8px; object-fit:cover; }

/* ===== Buttons ===== */
.action-btns{display:inline-flex; gap:6px; justify-content:center;}
button, .btn { padding:6px 12px; border:none; border-radius:5px; cursor:pointer; transition:0.3s; text-decoration:none; color:white; }
.btn-update { background:#2e7d32; }
.btn-update:hover { background:#43a047; }
.btn-delete { background:#c62828; }
.btn-delete:hover { background:#e53935; }

/* ===== Responsive ===== */
@media(max-width:900px){
    .container{flex-direction:column;}
    .sidebar{width:100%; display:flex; overflow-x:auto;}
    .sidebar a{flex:1; white-space:nowrap;}
}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy Staff</h1>
    </div>
    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" class="user-img">
        <span><?= htmlspecialchars($staffName) ?></span>
        <div class="dropdown" id="dropdownMenu">
            <a href="staff_profile.php">Profile</a>
            <form method="POST" action="staff_logout.php">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">

<!-- ===== Sidebar ===== -->
<div class="sidebar">
    <a href="staff_dashboard.php">🏠 Dashboard</a>
    <a href="view_customers.php">👥 Customers</a>
    <?php if($staffRole === 'Admin' || $staffRole === 'Baker'): ?>
        <a href="manage_orders.php">📦 Manage Orders</a>
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

<!-- ===== Main ===== -->
<div class="main">
<h2>Manage Menu</h2>

<a href="add_menu.php" class="add-menu-btn">➕ Add Menu</a>

<table>
    <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Category</th>
        <th>Unit Type</th>
        <th>Price (RM)</th>
        <th>Action</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
    <tr>
        <td><?= $row['menuID'] ?></td>
        <td><img src="images/<?= $row['image'] ?>" alt="<?= $row['menuName'] ?>"></td>
        <td><?= $row['menuName'] ?></td>
        <td><?= $row['category'] ?></td>
        <td><?= $row['unitType'] ?: '-' ?></td>
        <td><?= number_format($row['price'],2) ?></td>
        <td class="action-btns">
            <a href="update_menu.php?id=<?= $row['menuID'] ?>" class="btn btn-update">Update</a>
            <a href="delete_menu.php?id=<?= $row['menuID'] ?>" class="btn btn-delete" onclick="return confirm('Confirm delete?');">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>

</div>
</div>

<script>
function toggleStockMenu(){
    let m=document.getElementById("stockSubMenu");
    m.style.display = m.style.display==="block"?"none":"block";
}

// User dropdown
const userBox = document.getElementById("userBox");
const dropdown = document.getElementById("dropdownMenu");
userBox.addEventListener("click", function(e){
    dropdown.style.display = dropdown.style.display==="block"?"none":"block";
    e.stopPropagation();
});
document.addEventListener("click", function(e){
    if(!userBox.contains(e.target)){
        dropdown.style.display="none";
    }
});
</script>

</body>
</html>
