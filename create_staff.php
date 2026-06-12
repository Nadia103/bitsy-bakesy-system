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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Staff | Bitsy Bakesy</title>
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

/* MAIN */
.main{
    flex:1;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:40px;
}

/* FORM */
.form-box{
    background:white;
    padding:35px 40px;
    width:450px;
    border-radius:18px;
    box-shadow:0 5px 18px rgba(0,0,0,0.12);
}

.form-box h2{
    margin-top:0;
    margin-bottom:15px;
    color:#1b5e20;
}

.form-box label{
    font-weight:500;
    display:block;
    margin-bottom:5px;
    margin-top:15px;
}

.form-box input,.form-box select{
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #cfd8dc;
    font-size:15px;
    transition:0.25s;
}

.form-box input:focus,.form-box select:focus{
    border-color:#2e7d32;
    outline:none;
    box-shadow:0 0 0 3px rgba(46,125,50,0.2);
}

.btn-submit{
    width:100%;
    margin-top:25px;
    padding:12px 0;
    background:#2e7d32;
    color:white;
    border:none;
    border-radius:8px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.25s;
}

.btn-submit:hover{
    background:#1b5e20;
}
</style>
</head>
<body>

<header>
    <div class="logo">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Admin</h1>
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
        <div class="form-box">
            <h2>Create Staff Account</h2>

            <form action="process_create_staff.php" method="POST">
                <label>Full Name</label>
                <input type="text" name="staffName" required>

                <label>Email</label>
                <input type="email" name="staffEmail" required>

                <label>Password</label>
                <input type="password" name="staffPassword" required>

                <label>Role</label>
                <select name="staffRole" required>
                    <option value="">-- Select Role --</option>
                    <option value="Cashier">Cashier</option>
                    <option value="Baker">Baker</option>
                    <option value="Admin">Admin</option>
                </select>

                <button class="btn-submit" type="submit">Create Account</button>
            </form>
        </div>
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
