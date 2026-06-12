<?php
session_start();
if (!isset($_SESSION['staffRole']) || $_SESSION['staffRole'] != 'Admin') {
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$result = mysqli_query($conn, "SELECT * FROM staff ORDER BY staffID DESC");

$staffName = $_SESSION['staffName'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Management | Bitsy Bakesy</title>
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
.sidebar a{display:flex;align-items:center;gap:10px;padding:12px 15px;margin:8px 0;background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;transition:0.25s;}
.sidebar a:hover{background:#2e7d32;}

.main{flex:1;padding:35px;display:flex;justify-content:center;align-items:flex-start;}

.page-box{
    width:100%;
    max-width:1150px;
    background:white;
    padding:30px;
    border-radius:18px;
    box-shadow:0 4px 16px rgba(0,0,0,0.15);
}

.top-bar{display:flex;justify-content:space-between;align-items:center;margin-bottom:15px;}
h2{color:#1b5e20;margin:0;}
.add-btn{
    background:#2e7d32;color:white;padding:10px 18px;border-radius:8px;
    text-decoration:none;font-weight:500;transition:.3s;
}
.add-btn:hover{background:#1b5e20;}

table{width:100%;border-collapse:collapse;margin-top:20px;}
th{background:#2e7d32;color:white;padding:12px;text-align:left;font-size:15px;}
td{padding:12px;border-bottom:1px solid #ddd;font-size:15px;}
tr:hover{background:#e8f5e9;}

.role{padding:6px 14px;border-radius:20px;font-size:13px;font-weight:600;display:inline-block;}
.Admin{background:#c8e6c9;color:#1b5e20;}
.Cashier{background:#bbdefb;color:#0d47a1;}
.Baker{background:#ffe0b2;color:#e65100;}
.staff{background:#e0e0e0;color:#424242;}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy Admin</h1>
    </div>
    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png">
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
        <div class="page-box">
            <div class="top-bar">
                <h2>👩‍🍳 Staff Management</h2>
                <a class="add-btn" href="create_staff.php">+ Add New Staff</a>
            </div>

            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Created</th>
                </tr>
                <?php while($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?= $row['staffID'] ?></td>
                    <td><?= htmlspecialchars($row['staffName']) ?></td>
                    <td><?= htmlspecialchars($row['staffEmail']) ?></td>
                    <td><span class="role <?= $row['staffRole'] ?>"><?= $row['staffRole'] ?></span></td>
                    <td><?= date("d M Y", strtotime($row['created_at'])) ?></td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<script>
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.addEventListener("click",e=>{dropdown.style.display=dropdown.style.display==="block"?"none":"block";e.stopPropagation();});
document.addEventListener("click",()=>dropdown.style.display="none");
</script>
</body>
</html>

<?php mysqli_close($conn); ?>
