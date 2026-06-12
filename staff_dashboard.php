<?php
session_start();

// Pastikan staff login
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

// Sambung ke DB
$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$staffID   = $_SESSION['staffID'];
$staffName = $_SESSION['staffName'];
$staffRole = $_SESSION['staffRole']; // Admin / Baker / Cashier

// --- STATS ---
function countRow($conn, $query){
    $res = mysqli_query($conn, $query);
    if(!$res) die(mysqli_error($conn));
    return mysqli_fetch_assoc($res)['total'];
}

$orderTodayCount = countRow($conn, "SELECT COUNT(*) AS total FROM orders WHERE DATE(orderDate) = CURDATE()");
$pendingCount    = countRow($conn, "SELECT COUNT(*) AS total FROM orders WHERE status='Pending'");
$totalOrders     = countRow($conn, "SELECT COUNT(*) AS total FROM orders");

// --- PICKUP ORDERS ---
// Ambil semua orders dengan pickupDate di masa depan
$pickupOrders = [];
$pickupDates  = [];

$sqlPickup = "
SELECT 
    o.orderID,
    oi.pickupDate,
    COALESCE(c.customerName,'Walk-in') AS customerName,
    mn.menuName,
    oi.quantity
FROM orders o
JOIN order_items oi ON o.orderID = oi.orderID
JOIN menu_unit mu ON oi.unitID = mu.unitID
JOIN menu mn ON mu.menuID = mn.menuID
LEFT JOIN customer c ON o.customerID = c.customerID
WHERE o.orderType IS NULL
  AND oi.pickupDate IS NOT NULL
  AND oi.pickupDate != '0000-00-00'
  AND oi.pickupDate >= CURDATE()
ORDER BY oi.pickupDate ASC, o.orderID ASC
";

$resultPickup = mysqli_query($conn, $sqlPickup);
if ($resultPickup) {
    while ($row = mysqli_fetch_assoc($resultPickup)) {
        $dateOnly = $row['pickupDate'];

        if (!isset($pickupOrders[$dateOnly][$row['orderID']])) {
            $pickupOrders[$dateOnly][$row['orderID']] = [
                'customerName' => $row['customerName'],
                'items' => []
            ];
        }

        $pickupOrders[$dateOnly][$row['orderID']]['items'][] = [
            'menuName' => $row['menuName'],
            'quantity' => $row['quantity']
        ];

        $pickupDates[] = $dateOnly;
    }
}

$pickupDates = array_values(array_unique($pickupDates));
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Dashboard | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
*{box-sizing:border-box;}
header{background:#2e7d32;color:white;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 3px 8px rgba(0,0,0,0.2);}
.logo{display:flex;align-items:center;gap:12px;}
.logo img{width:50px;height:50px;border-radius:50%;}
.logo h1{font-family:'Pacifico',cursive;font-size:28px;margin:0;user-select:none;}
.container{display:flex;min-height:100vh;}
.sidebar{width:250px;background:#1b5e20;padding:25px 15px;color:white;position:sticky;top:0;height:100vh;}
.sidebar a{display:block;align-items:center;gap:10px;padding:12px 15px;margin:8px 0;background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;transition:0.25s ease;}
.sidebar a:hover{background:#2e7d32;transform:translateX(6px);}
.submenu{display:none;margin-left:15px;}
.submenu a{background:rgba(255,255,255,0.15);font-size:14px;}
.user-box{display:flex;align-items:center;gap:10px;position:relative;cursor:pointer;}
.user-box .user-img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid white;transition: transform 0.2s ease;}
.user-box:hover .user-img{transform: scale(1.1);}
.user-box span{font-weight:500;color:white;user-select:none;}
.dropdown{display:none;position:absolute;top:50px;right:0;background:white;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.15);min-width:180px;z-index:100;overflow:hidden;font-size:14px;}
.dropdown a, .dropdown button{display:block;padding:10px 15px;color:#2e7d32;text-decoration:none;width:100%;text-align:left;background:none;border:none;cursor:pointer;transition:0.2s ease;}
.dropdown a:hover, .dropdown button:hover{background:#f0f4f0;}
.dropdown button{text-align:left;}
.main{flex:1;padding:40px;}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:25px;}
.card{background:white;padding:25px;border-radius:18px;text-align:center;box-shadow:0 4px 16px rgba(0,0,0,0.15);}
.card h3{margin:0;color:#1b5e20;}
.card p{font-size:32px;font-weight:bold;margin:10px 0 0;}
.staff-card{margin-top:40px;background:white;padding:25px;border-radius:18px;box-shadow:0 4px 16px rgba(0,0,0,0.15);}
#calendarTable{width:100%;border-collapse:collapse;margin-top:10px;overflow:hidden;border-radius:12px;}
#calendarTable th{background:#2e7d32;color:white;padding:12px;font-size:15px;}
#calendarTable td{padding:10px;border:1px solid #ddd;height:40px;cursor:pointer;transition:0.2s;text-align:center;}
#calendarTable td:hover{background:#dcedc8;}
.today{background:#a5d6a7 !important;color:black;font-weight:bold;}
.pickup{background:#ffeb3b !important;font-weight:bold;color:black;}
.order-summary{background:#f0f4f0;padding:15px;margin:12px 0;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,0.1);}
.popup{display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);}
.popup-content{background:white;width:450px;margin:100px auto;padding:20px;border-radius:12px;}
.order{background:#fffde7;padding:10px;margin:10px 0;border-radius:8px;}
button{padding:8px 15px;border:none;border-radius:6px;background:#2e7d32;color:white;cursor:pointer;}
</style>
</head>
<body>

<header>
<div class="logo">
<img src="bit.png" alt="Bitsy Logo">
<h1>Bitsy Bakesy Staff</h1>
</div>

<div class="user-box" id="userBox">
<img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" alt="User Icon" class="user-img">
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
<div class="sidebar">
<a href="manage_menu.php">🍰 Manage Menu</a>
<?php if($staffRole === 'Admin'): ?>
    <a href="manage_orders.php">📦 Manage Orders</a>
<?php endif; ?>
<a href="view_customers.php">👥 Customers</a>
<?php if($staffRole === 'Baker'): ?>
    <a href="manage_orders.php">📦 Manage Orders</a>
<?php endif; ?>
<a href="#" onclick="toggleStockMenu()">📦 Stocks ▾</a>
<div class="submenu" id="stockSubMenu">
    <a href="manage_raw_material.php">🥖 Raw Material Stock</a>
    <a href="manage_product.php">🍰 Products Stock</a>
    <?php if($staffRole === 'Baker'): ?>
        <a href="production_simple.php">🍰 Production Stock</a>
    <?php endif; ?>
</div>
</div>

<div class="main">
<div class="stats">
<div class="card"><h3>Today's Orders</h3><p><?= $orderTodayCount ?></p></div>
<div class="card"><h3>Pending Orders</h3><p><?= $pendingCount ?></p></div>
<div class="card"><h3>Total Orders</h3><p><?= $totalOrders ?></p></div>
</div>

<div class="staff-card">
<h2>📅 Orders Calendar</h2>
<table id="calendarTable"></table>
</div>

<div class="staff-card">
<h2>📋 Pickup Orders</h2>
<?php if(empty($pickupOrders)): ?>
<p>No upcoming pickups.</p>
<?php else: ?>
<?php foreach($pickupOrders as $date => $ordersByID): ?>
<div class="order-summary">
    <strong>Pickup Date: <?= $date ?></strong>
    <ul>
    <?php foreach($ordersByID as $order): ?>
        <?php if(!empty($order['items'])): ?>
            <?php foreach($order['items'] as $item): ?>
            <li><?= htmlspecialchars($order['customerName']) ?> – <?= htmlspecialchars($item['menuName']) ?> x<?= $item['quantity'] ?></li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><?= htmlspecialchars($order['customerName']) ?> – - x1</li>
        <?php endif; ?>
    <?php endforeach; ?>
    </ul>
</div>
<?php endforeach; ?>
<?php endif; ?>
</div>

</div>
</div>

<div class="popup" id="popup">
<div class="popup-content">
<h3 id="popupDate"></h3>
<div id="popupOrders"></div>
<button onclick="closePopup()">Close</button>
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

// ===== Calendar =====
const pickupDates = <?= json_encode($pickupDates) ?>;
const pickupOrders = <?= json_encode($pickupOrders) ?>;
const calendar=document.getElementById("calendarTable");
const today=new Date();
const todayStr=today.toISOString().split("T")[0];
const y=today.getFullYear(), m=today.getMonth();
const monthNames=["January","February","March","April","May","June","July","August","September","October","November","December"];
const days=["Sun","Mon","Tue","Wed","Thu","Fri","Sat"];

let first=new Date(y,m,1);
let last=new Date(y,m+1,0);

let html=`<tr><th colspan="7">${monthNames[m]} ${y}</th></tr>`;
html+="<tr>"+days.map(d=>`<th>${d}</th>`).join("")+"</tr><tr>";

for(let i=0;i<first.getDay();i++) html+="<td></td>";
for(let d=1;d<=last.getDate();d++){
    let cur=new Date(y,m,d);
    let ds=cur.toISOString().split("T")[0];
    let cls="";
    if(ds===todayStr) cls+=" today";
    if(pickupDates.includes(ds)) cls+=" pickup";
    html+=`<td class="${cls}" data-date="${ds}" onclick="showPopup('${ds}')">${d}</td>`;
    if(cur.getDay()===6) html+="</tr><tr>";
}
html+="</tr>";
calendar.innerHTML=html;

function showPopup(date){
    if(!pickupOrders[date]) return;
    document.getElementById("popupDate").innerText="Pickup Orders – "+date;
    let out="";
    Object.values(pickupOrders[date]).forEach(order=>{
        out+=`<div class='order'><b>${order.customerName}</b><ul>`;
        order.items.forEach(i=>{
            out+=`<li>${i.menuName} x${i.quantity}</li>`;
        });
        out+="</ul></div>";
    });
    document.getElementById("popupOrders").innerHTML=out;
    document.getElementById("popup").style.display="block";
}
function closePopup(){
    document.getElementById("popup").style.display="none";
}
</script>

</body>
</html>
