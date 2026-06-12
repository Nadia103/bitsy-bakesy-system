<?php
session_start();

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "bitsy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$staffName = $_SESSION['staffName'];
$staffRole = $_SESSION['staffRole']; // Admin / Baker / Cashier

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $item = $conn->query("SELECT * FROM stock WHERE id = '$id'")->fetch_assoc();
    if (!$item) {
        echo "<script>alert('Item not found'); window.location='manage_raw_material.php';</script>";
        exit;
    }
}

if (isset($_POST['use_stock'])) {
    $id = intval($_POST['id']);
    $quantity_used = intval($_POST['quantity_used']);

    $result = $conn->query("SELECT quantity FROM stock WHERE id='$id'");
    $current = $result->fetch_assoc()['quantity'];

    $new_quantity = max($current - $quantity_used, 0);

    $conn->query("UPDATE stock SET quantity='$new_quantity' WHERE id='$id'");
    $conn->query("INSERT INTO stock_out_log (item_id, quantity_used, date_used, used_by)
                  VALUES ('$id', '$quantity_used', NOW(), 'staff')");

    echo "<script>alert('Stock updated successfully!'); window.location='manage_raw_material.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Use Stock | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f4f6f5; margin:0; }
header{background:#2e7d32;color:white;padding:15px 25px;display:flex;justify-content:space-between;align-items:center;}
header .logo { display:flex; align-items:center; gap:12px; }
header .logo img{ width:50px; height:50px; border-radius:50%; object-fit:cover; }
header h1{font-family:'Pacifico',cursive;margin:0;font-size:28px;color:white;}

/* User box */
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
    min-width:160px;
    z-index:100;
    overflow:hidden;
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
    transition:0.2s;
}
.dropdown a:hover, .dropdown button:hover{background:#f0f4f0;}

/* Layout */
.container{display:flex; min-height:100vh;}
.sidebar{width:240px;background:#1b5e20;color:white;padding:20px;box-shadow:3px 0 10px rgba(0,0,0,0.2);}
.sidebar a{display:block;padding:12px 15px;margin:8px 0;color:white;text-decoration:none;border-radius:8px;background:rgba(255,255,255,0.07);transition:0.25s;}
.sidebar a:hover{background:#2e7d32;transform:translateX(6px);}
.submenu{display:none;margin-left:15px;}
.submenu a{background:rgba(255,255,255,0.15);font-size:14px;}

/* Main */
.main{flex:1;padding:30px;}
h2{color:#2e7d32;text-align:center;margin-bottom:20px;}
.card {
    background:white; 
    padding:30px; 
    border-radius:10px; 
    box-shadow:0 3px 8px rgba(0,0,0,0.2); 
    max-width:450px; 
    margin:auto;
}

/* Form */
label { display:block; font-weight:600; margin-top:10px; }
input[type=number] { width:100%; padding:8px; margin-top:5px; border:1px solid #ccc; border-radius:6px; }

/* Button hijau */
button[name="use_stock"] { 
    margin-top:20px; 
    width:100%; 
    padding:10px; 
    border:none; 
    background:#2e7d32; /* hijau */
    color:white; 
    border-radius:6px; 
    cursor:pointer; 
    transition:0.3s;
}
button[name="use_stock"]:hover { 
    background:#43a047; /* hijau hover */
}

.back-link { display:block; margin-top:15px; text-align:center; color:#3498db; text-decoration:none;}
.back-link:hover { text-decoration:underline; }
.error { color:red; text-align:center; margin-top:10px; }

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

<!-- Sidebar -->
<div class="sidebar">
    <a href="staff_dashboard.php">🏠 Dashboard</a>
    <a href="manage_menu.php">🍰 Manage Menu</a>
    <?php if($staffRole === 'Admin' || $staffRole === 'Baker'): ?>
        <a href="manage_orders.php">📦 Manage Orders</a>
    <?php endif; ?>
    <a href="view_customers.php">👥 Customers</a>
    <a href="#" onclick="toggleStockMenu()">📦 Stocks ▾</a>
    <div class="submenu" id="stockSubMenu">
        <a href="manage_raw_material.php">🥖 Raw Material Stock</a>
        <a href="manage_product.php">🍰 Products Stock</a>
        <?php if($staffRole==='Baker'): ?>
            <a href="production_simple.php">🍰 Production Stock</a>
        <?php endif; ?>
    </div>
</div>

<!-- Main -->
<div class="main">
<h2>Use Stock - <?= htmlspecialchars($item['item_name']) ?></h2>

<div class="card">
    <form method="POST">
        <input type="hidden" name="id" value="<?= $item['id'] ?>">

        <label>Current Quantity:</label>
        <input type="number" value="<?= $item['quantity'] ?>" disabled>

        <label for="quantity_used">Quantity to Use:</label>
        <input type="number" id="quantity_used" name="quantity_used" min="1" required>

        <button type="submit" name="use_stock">Update Stock</button>
        <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>

       
    </form>
</div>
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
