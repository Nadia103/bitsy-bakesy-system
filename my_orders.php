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

// Dapatkan filter status daripada URL, default "All"
$statusFilter = $_GET['status'] ?? 'All';
$statusSQL = ($statusFilter != 'All') ? "AND status='$statusFilter'" : "";

// Ambil semua order untuk customer ikut filter
$order_sql = "SELECT * FROM orders WHERE customerID = '$customerID' $statusSQL ORDER BY orderDate DESC";
$order_res = mysqli_query($conn, $order_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Orders - Bitsy Bakesy</title>

<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ----------------- GENERAL ----------------- */
* { box-sizing: border-box; scroll-behavior: smooth; }
body { margin:0; font-family:'Poppins',sans-serif; background:#f6f1e7; }

/* ----------------- NAVBAR ----------------- */
.navbar {
    background-color: #3b6b45;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 40px;
    position: sticky;
    top:0;
    z-index:100;
}
.navbar-left { display:flex; align-items:center; gap:15px; }
.navbar-left img { height:55px; width:55px; border-radius:50%; background:white; border:2px solid white; object-fit:cover; }
.navbar-left h1 { font-family:'Pacifico',cursive; font-size:28px; margin:0; }
.nav-links { display:flex; align-items:center; gap:25px; }
.nav-links a { color:white; text-decoration:none; font-weight:500; position:relative; }
.nav-links a::after {
    content:""; position:absolute; bottom:-5px; left:0;
    width:0; height:2px; background-color:#e0ffd5; transition:width 0.3s;
}
.nav-links a:hover::after { width:100%; }
.profile { position:relative; display:inline-block; cursor:pointer; }
.profile-btn { display:flex; align-items:center; gap:8px; }
.profile img { width:35px; height:35px; border-radius:50%; }
.dropdown { display:none; position:absolute; right:0; background:white; min-width:160px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.1); overflow:hidden; }
.dropdown a, .dropdown button { color:#3b6b45; padding:10px 15px; display:block; text-align:left; background:none; border:none; font-size:15px; width:100%; cursor:pointer; }
.dropdown a:hover, .dropdown button:hover { background-color:#e8f5e9; }

/* ----------------- CONTAINER ----------------- */
.container { max-width:900px; margin:40px auto; background:white; padding:30px; border-radius:15px; box-shadow:0 4px 10px rgba(0,0,0,0.1); }
h2 { text-align:center; color:#3b6b45; margin-bottom:15px; }

/* ----------------- FILTER ----------------- */
.filter { text-align:center; margin-bottom:20px; }
.filter a { margin:0 10px; text-decoration:none; padding:6px 12px; background:#3b6b45; color:white; border-radius:8px; transition:0.3s; }
.filter a.active, .filter a:hover { background:#2f5536; }

/* ----------------- TABLE ----------------- */
table { width:100%; border-collapse:collapse; }
th, td { padding:12px 15px; border-bottom:1px solid #ddd; text-align:left; vertical-align:middle; }
th { background:#3b6b45; color:white; }

/* ----------------- BUTTON ----------------- */
.view-btn {
    display:inline-block;
    background:#3b6b45;
    color:white;
    padding:8px 16px;
    border-radius:8px;
    text-decoration:none;
    font-weight:500;
    font-size:14px;
    white-space:nowrap;
    transition:0.3s;
}
.view-btn:hover {
    background:#2f5536;
    transform: scale(1.05);
}

/* ----------------- FOOTER ----------------- */
footer { background-color:#3b6b45; color:white; text-align:center; padding:25px 0; font-size:14px; margin-top:50px; }
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar" id="navbar">
    <div class="navbar-left">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy</h1>
    </div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="menu.php">Menu</a>
        <?php if(isset($_SESSION['user_name'])): ?>
        <div class="profile" id="profileDropdown">
            <div class="profile-btn">
                <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
            </div>
            <div class="dropdown" id="dropdownMenu">
                <a href="my_orders.php">My Orders</a>
                <a href="profile.php">Profile</a>
                <form method="POST" action="logout.php"><button type="submit">Logout</button></form>
            </div>
        </div>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h2>My Orders</h2>

    <div class="filter">
        <?php
        $statuses = ['All','Pending','Completed','Cancelled'];
        foreach($statuses as $status){
            $active = ($statusFilter == $status) ? 'active' : '';
            echo "<a class='$active' href='?status=$status'>$status</a>";
        }
        ?>
    </div>

    <?php if(mysqli_num_rows($order_res) == 0): ?>
        <p style="text-align:center;">No orders found for selected status.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>Order ID</th>
                <th>Total Amount (RM)</th>
                <th>Status</th>
                <th>Payment Method</th>
                <th>Order Date</th>
                <th>Action</th>
            </tr>
            <?php while($order = mysqli_fetch_assoc($order_res)): ?>
            <tr>
                <td><?php echo $order['orderID']; ?></td>
                <td><?php echo number_format($order['totalAmount'],2); ?></td>
                <td><?php echo $order['status']; ?></td>
                <td><?php echo $order['paymentMethod']; ?></td>
                <td><?php echo $order['orderDate']; ?></td>
                <td>
                    <a href="receipt.php?orderID=<?php echo $order['orderID']; ?>" class="view-btn">View Receipt</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2025 Bitsy Bakesy Bakery. All Rights Reserved.</p>
    <p>Follow us on Instagram & TikTok @BitsyBakesy</p>
</footer>

<script>
const profile = document.getElementById("profileDropdown");
const dropdown = document.getElementById("dropdownMenu");
if(profile){
    profile.addEventListener("click", e => {
        dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
        e.stopPropagation();
    });
    document.addEventListener("click", e => {
        if(!profile.contains(e.target)) dropdown.style.display = "none";
    });
}
</script>

</body>
</html>
