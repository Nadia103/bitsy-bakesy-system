<?php
session_start();
$conn = mysqli_connect("localhost","root","","bitsy");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

// Redirect kalau tak login
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$customerID = $_SESSION['user_id'];

// Handle form submit
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = mysqli_real_escape_string($conn, $_POST['customerName']);
    $email = mysqli_real_escape_string($conn, $_POST['customerEmail']);
    $phone = mysqli_real_escape_string($conn, $_POST['customerPhoneNo']);
    $password = $_POST['customerPassword']; // raw password input

    if(!empty($password)){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $update_sql = "UPDATE customer SET customerName='$name', customerEmail='$email', customerPhoneNo='$phone', customerPassword='$hashedPassword' WHERE customerID='$customerID'";
    } else {
        $update_sql = "UPDATE customer SET customerName='$name', customerEmail='$email', customerPhoneNo='$phone' WHERE customerID='$customerID'";
    }

    if(mysqli_query($conn, $update_sql)){
        $msg = "Profile updated successfully!";
    } else {
        $msg = "Error updating profile: " . mysqli_error($conn);
    }
}

// Ambil data customer semasa
$sql = "SELECT * FROM customer WHERE customerID='$customerID'";
$res = mysqli_query($conn, $sql);
$customer = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f9f9f9;}
/* ===== Navbar ===== */
.navbar{background:#3b6b45;color:white;display:flex;justify-content:space-between;align-items:center;padding:15px 40px;}
.navbar-left{display:flex;align-items:center;gap:15px;}
.navbar-left img{height:60px;width:60px;border-radius:50%;border:2px solid white;}
.navbar-left h1{font-family:'Pacifico',cursive;font-size:26px;margin:0;}
.nav-links{display:flex;align-items:center;gap:25px;}
.nav-links a{color:white;text-decoration:none;font-weight:500;transition:0.3s;}
.nav-links a:hover{color:#d4f5d0;}
.cart-icon{font-size:22px;color:white;position:relative;text-decoration:none;}
.cart-count{position:absolute;top:-8px;right:-10px;background:#ff4d4d;color:white;font-size:12px;font-weight:bold;border-radius:50%;padding:2px 6px;}
.profile img{width:35px;height:35px;border-radius:50%;cursor:pointer;}
.profile .dropdown{display:none;position:absolute;background:white;color:black;top:50px;right:0;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.2);flex-direction:column;min-width:150px;}
.profile:hover .dropdown{display:flex;}
.profile .dropdown a,.profile .dropdown form button{padding:10px 15px;text-align:left;border:none;background:none;cursor:pointer;text-decoration:none;color:black;width:100%;}
.profile .dropdown form button:hover,.profile .dropdown a:hover{background:#f0f0f0;}
/* ===== Container ===== */
.container{max-width:600px;margin:50px auto;background:white;padding:30px;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
h2{text-align:center;color:#3b6b45;margin-bottom:25px;}
form{display:flex;flex-direction:column;gap:15px;}
label{font-weight:500;}
input{padding:10px;border-radius:6px;border:1px solid #ccc;font-size:16px;}
button, .back-btn{background:#3b6b45;color:white;padding:12px;border:none;border-radius:8px;cursor:pointer;font-size:16px;transition:0.3s;text-align:center;text-decoration:none;display:inline-block;}
button:hover, .back-btn:hover{background:#2f5536;}
.msg{text-align:center;color:green;font-weight:500;margin-bottom:15px;}
footer{background:#3b6b45;color:white;text-align:center;padding:15px;margin-top:40px;}
</style>
</head>
<body>

<!-- ===== Navbar ===== -->
<div class="navbar">
    <div class="navbar-left">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy Bakery</h1>
    </div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="cart.php" class="cart-icon">
            🛒
            <?php if (!empty($_SESSION['cart'])): ?>
                <span class="cart-count"><?php echo array_sum(array_column($_SESSION['cart'], 'quantity')); ?></span>
            <?php endif; ?>
        </a>
        <?php if(isset($_SESSION['user_name'])): ?>
            <div class="profile">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Profile">
                <div class="dropdown">
                    <a href="my_order.php">My Orders</a>
                    <a href="profile.php">Profile</a>
                    <form method="POST" action="logout.php"><button type="submit">Logout</button></form>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<!-- ===== Profile Form ===== -->
<div class="container">
    <h2>My Profile</h2>
    <a href="index.php" class="back-btn">← Back to Homepage</a>

    <?php if(isset($msg)) echo "<p class='msg'>$msg</p>"; ?>
    <form method="POST" action="">
        <label>Full Name</label>
        <input type="text" name="customerName" value="<?php echo htmlspecialchars($customer['customerName']); ?>" required>

        <label>Email</label>
        <input type="email" name="customerEmail" value="<?php echo htmlspecialchars($customer['customerEmail']); ?>" required>

        <label>Phone Number</label>
        <input type="text" name="customerPhoneNo" value="<?php echo htmlspecialchars($customer['customerPhoneNo']); ?>" required>

        <label>Password (leave blank to keep current)</label>
        <input type="password" name="customerPassword">

        <button type="submit">Save Changes</button>
    </form>
</div>

<!-- ===== Footer ===== -->
<footer>&copy; 2025 Bitsy Bakesy Bakery</footer>

</body>
</html>
