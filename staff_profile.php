<?php
session_start();
include('db.php'); // sambung ke database

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

$staffID = $_SESSION['staffID'];

// Ambil info staff
$sql = "SELECT * FROM staff WHERE staffID=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staffID);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$staffName = $staff['staffName'];
$staffEmail = $staff['staffEmail'];
$staffRole = $staff['staffRole']; // Admin / Baker / Cashier

// Update staff info
$msg = '';
if(isset($_POST['updateProfile'])){
    $newName = $_POST['staffName'];
    $newEmail = $_POST['staffEmail'];
    $newPassword = $_POST['staffPassword'];

    if(!empty($newPassword)){
        $hashedPass = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateSql = "UPDATE staff SET staffName=?, staffEmail=?, password=? WHERE staffID=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssi", $newName, $newEmail, $hashedPass, $staffID);
    } else {
        $updateSql = "UPDATE staff SET staffName=?, staffEmail=? WHERE staffID=?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssi", $newName, $newEmail, $staffID);
    }

    if($stmt->execute()){
        $msg = "✅ Profile updated successfully!";
        $_SESSION['staffName'] = $newName;
        $staffName = $newName;
        $staffEmail = $newEmail;
    } else {
        $msg = "❌ Update failed!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profile - Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
*{box-sizing:border-box;}

/* ===== HEADER ===== */
header{
    background:#2e7d32;color:white;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 3px 8px rgba(0,0,0,0.2);
}

.brand-logo{
    display:flex;align-items:center;gap:12px;
}
.brand-logo img{
    width:50px;
    height:50px;
    border-radius:50%; /* BULAT */
    object-fit:cover;
}
.brand-logo h1{font-family:'Pacifico',cursive;font-size:28px;margin:0;}

/* ===== USER BOX ===== */
.user-box{
    display:flex;align-items:center;gap:10px;position:relative;cursor:pointer;
}
.user-box img{width:40px;height:40px;border-radius:50%;border:2px solid white;object-fit:cover;transition:0.2s;}
.user-box:hover img{transform:scale(1.05);}
.user-box span{font-weight:500;user-select:none;}

.dropdown{
    display:none;position:absolute;top:50px;right:0;background:white;border-radius:10px;min-width:160px;box-shadow:0 6px 20px rgba(0,0,0,0.2);overflow:hidden;font-size:14px;
}
.dropdown a,.dropdown button{display:block;padding:12px 15px;width:100%;text-align:left;background:none;border:none;color:#2e7d32;text-decoration:none;cursor:pointer;}
.dropdown a:hover,.dropdown button:hover{background:#e8f5e9;}

/* ===== SIDEBAR ===== */
.container{display:flex;min-height:100vh;}
.sidebar{width:250px;background:#1b5e20;padding:20px;color:white;height:100vh;position:sticky;top:0;}
.sidebar a{display:block;padding:12px 15px;margin:8px 0;background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;transition:0.25s;}
.sidebar a:hover{background:#2e7d32;transform:translateX(5px);}
.submenu{display:none;margin-left:15px;}
.submenu a{background:rgba(255,255,255,0.15);font-size:14px;}

/* ===== MAIN ===== */
.main{flex:1;padding:40px;}
form{background:white;padding:30px;border-radius:18px;box-shadow:0 4px 16px rgba(0,0,0,0.1);max-width:600px;margin:auto;}
form input{width:100%;padding:12px;margin:10px 0;border:1px solid #ccc;border-radius:6px;}
form button{background:#2e7d32;color:white;padding:12px 20px;border:none;border-radius:6px;cursor:pointer;margin-top:10px;transition:0.3s;}
form button:hover{background:#43a047;}
.msg{text-align:center;margin:15px 0;font-weight:500;}
</style>
</head>
<body>

<header>
    <div class="brand-logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy Staff</h1>
    </div>

    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="User">
        <span><?= htmlspecialchars($staffName) ?></span>
        <div class="dropdown" id="dropdownMenu">
            <a href="staff_profile.php">Profile</a>
            <form id="logoutForm" action="staff_logout.php" method="POST" style="display:none;"></form>
            <button type="submit" form="logoutForm">Logout</button>
        </div>
    </div>
</header>

<div class="container">

<div class="sidebar">
    <a href="staff_dashboard.php">📊 Dashboard</a>
    <a href="manage_menu.php">🍰 Manage Menu</a>
    <?php if($staffRole === 'Admin'): ?>
        <a href="manage_orders.php">📦 Manage Orders</a>
    <?php endif; ?>
    <a href="view_customers.php">👥 Customers</a>

    <a href="javascript:void(0)" onclick="toggleStockMenu()">📦 Stocks ▾</a>
    <div class="submenu" id="stockSubMenu">
        <a href="manage_raw_material.php">🥖 Raw Material Stock</a>
        <a href="manage_product.php">🍰 Product Stock</a>
        <?php if($staffRole === 'Baker'): ?>
            <a href="production_simple.php">🍰 Production Stock</a>
        <?php endif; ?>
    </div>
</div>

<div class="main">
    <h1>👤 Profile</h1>
    <?php if($msg) echo "<div class='msg'>$msg</div>"; ?>
    <form method="POST" action="">
        <label for="staffName">Name</label>
        <input type="text" name="staffName" id="staffName" value="<?= htmlspecialchars($staffName) ?>" required>

        <label for="staffEmail">Email</label>
        <input type="email" name="staffEmail" id="staffEmail" value="<?= htmlspecialchars($staffEmail) ?>" required>

        <label for="staffPassword">New Password (leave blank if not changing)</label>
        <input type="password" name="staffPassword" id="staffPassword">

        <button type="submit" name="updateProfile">Update Profile</button>
    </form>
</div>
</div>

<script>
function toggleStockMenu(){
    const menu=document.getElementById("stockSubMenu");
    menu.style.display = menu.style.display==="block"?"none":"block";
}

// user dropdown
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.addEventListener("click",e=>{
    dropdown.style.display = (dropdown.style.display==="block")?"none":"block";
    e.stopPropagation();
});
document.addEventListener("click",e=>{
    if(!userBox.contains(e.target)) dropdown.style.display="none";
});
</script>

</body>
</html>
