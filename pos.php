<?php
session_start();

// Lock access: Cashier only
if (!isset($_SESSION['staffRole']) || $_SESSION['staffRole'] != 'Cashier') {
    header("Location: staff_login.php");
    exit();
}

$staffID = $_SESSION['staffID'];
$staffName = $_SESSION['staffName'];

$conn = mysqli_connect("localhost","root","","bitsy");
if(!$conn){ die("Connection failed: ".mysqli_connect_error()); }

// Fetch menu with image & price
$menuRes = mysqli_query($conn, "
    SELECT m.menuID, m.menuName, m.image, mu.price
    FROM menu m
    JOIN menu_unit mu ON m.menuID = mu.menuID
    ORDER BY m.menuName ASC
");

if (!$menuRes) { die("Query Failed: " . mysqli_error($conn)); }

// Fetch customers
$customerRes = mysqli_query($conn, "SELECT * FROM customer ORDER BY customerName ASC");
if (!$customerRes) { die("Query Failed: " . mysqli_error($conn)); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>POS | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
/* ===== Font Global ===== */
body, input, select, button, h2, h3, label, table, td, th, div{
    font-family: 'Poppins', sans-serif;
}

/* ===== Body & Layout ===== */
body{
    margin:0;
    background:#f9f9f9;
}
header{
    background:#2e7d32;
    color:white;
    padding:10px 30px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 6px rgba(0,0,0,0.2);
}
.header-left{
    display:flex;
    align-items:center;
    gap:15px;
}
header img.logo{
    height:50px;
    width:50px;
    border-radius:50%;
    object-fit:cover;
    background:white;
    padding:5px;
}
/* ===== Pacifico font for Bitsy Bakesy POS title ===== */
header h2{
    font-family:'Pacifico',cursive;
    font-weight:400;
    font-size:32px;
    letter-spacing:1px;
    margin:0;
    color:white;
}

/* ===== User box ===== */
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
    transition:0.2s;
}
.user-box:hover .user-img{ transform:scale(1.1); }
.user-box span{ color:white; font-weight:500; user-select:none; }
.dropdown{
    display:none;
    position:absolute;
    top:50px;
    right:0;
    background:white;
    border-radius:10px;
    box-shadow:0 6px 20px rgba(0,0,0,0.15);
    min-width:140px;
    overflow:hidden;
    z-index:100;
}
.dropdown a, .dropdown form button{
    display:block;
    padding:10px 15px;
    width:100%;
    text-align:left;
    border:none;
    background:none;
    cursor:pointer;
    color:#2e7d32;
    text-decoration:none;
    transition:0.2s;
}
.dropdown a:hover, .dropdown form button:hover{ background:#f0f4f0; }

/* ===== Container ===== */
.container{
    display:flex;
    padding:20px;
    gap:20px;
    flex-wrap:wrap;
}

/* ===== Menu Panel ===== */
.menu-panel{
    flex:2;
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(150px,1fr));
    gap:15px;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.menu-item{
    display:flex;
    flex-direction:column;
    align-items:center;
    text-align:center;
    border:1px solid #eee;
    border-radius:10px;
    padding:10px;
    transition:0.3s;
    background:#fff8f0;
}
.menu-item:hover{
    transform:translateY(-4px);
    box-shadow:0 6px 12px rgba(0,0,0,0.15);
}
.menu-item img{
    width:100px;
    height:100px;
    object-fit:cover;
    border-radius:10px;
    margin-bottom:10px;
}
.menu-item div{
    font-weight:500;
}
.menu-item button{
    background:#2e7d32;
    color:white;
    border:none;
    padding:6px 12px;
    border-radius:6px;
    cursor:pointer;
    margin-top:8px;
    font-weight:600;
}
.menu-item button:hover{background:#27632a;}

/* ===== Cart Panel ===== */
.cart-panel{
    flex:1;
    min-width:300px;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.cart-panel h3{
    margin-top:0;
    font-weight:600;
}
label{
    font-weight:600;
    margin-top:10px;
    display:block;
}
select, input{
    padding:6px;
    border-radius:6px;
    border:1px solid #ccc;
    margin:5px 0;
    width:100%;
    font-weight:400;
}
.cart-table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}
.cart-table th, .cart-table td{
    padding:8px;
    border-bottom:1px solid #ddd;
    text-align:center;
    font-weight:500;
}
.total{
    font-weight:700;
    text-align:right;
    margin:10px 0;
}
.checkout-btn{
    width:100%;
    padding:12px;
    background:#388e3c;
    font-size:16px;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
    margin-top:10px;
}
.checkout-btn:hover{background:#2e7d32;}
</style>
</head>
<body>

<header>
    <div class="header-left">
        <img src="bit.png" alt="Bitsy Logo" class="logo">
        <h2>Bitsy Bakesy POS</h2>
    </div>
    <div class="user-box" id="userBox">
        <img src="https://cdn-icons-png.flaticon.com/512/3177/3177440.png" class="user-img" alt="User Icon">
        <span><?= htmlspecialchars($staffName) ?></span>
        <div class="dropdown" id="dropdownMenu">
            <a href="profile.php">Profile</a>
            <form method="POST" action="staff_logout.php">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
</header>

<div class="container">

<!-- MENU -->
<div class="menu-panel">
<?php while($menu = mysqli_fetch_assoc($menuRes)) { ?>
    <div class="menu-item">
        <img src="images/<?= htmlspecialchars($menu['image'] ?: 'placeholder.png') ?>" 
             alt="<?= htmlspecialchars($menu['menuName']) ?>" 
             onerror="this.src='images/placeholder.png';">
        <div><?= htmlspecialchars($menu['menuName']) ?></div>
        <div>RM <?= number_format($menu['price'],2) ?></div>
        <button onclick="addToCart(
            <?= $menu['menuID'] ?>,
            '<?= addslashes($menu['menuName']) ?>',
            <?= $menu['price'] ?>
        )">Add</button>
    </div>
<?php } ?>
</div>

<!-- CART -->
<div class="cart-panel">
<h3>Cart</h3>

<form method="POST" action="pos_process.php" id="posForm">

<label>Customer</label>
<select name="customerID" required>
    <option value="0">Walk-in</option>
    <?php while($cust = mysqli_fetch_assoc($customerRes)) { ?>
        <option value="<?= $cust['customerID'] ?>">
            <?= htmlspecialchars($cust['customerName']) ?>
        </option>
    <?php } ?>
</select>

<table class="cart-table" id="cartTable">
<thead>
<tr>
    <th>Item</th>
    <th>Qty</th>
    <th>Price</th>
    <th>X</th>
</tr>
</thead>
<tbody></tbody>
</table>

<div class="total">
Total: RM <span id="totalAmount">0.00</span>
</div>

<label>Payment Method</label>
<select name="paymentMethod" required>
    <option value="Cash">Cash</option>
    <option value="QR Pay">QR Pay</option>
    <option value="Online Banking">Online Banking</option>
</select>

<button class="checkout-btn">Checkout & Receipt</button>
</form>
</div>

</div>

<script>
let cart=[];

function addToCart(id,name,price){
    let item = cart.find(i=>i.id===id);
    if(item) item.qty++;
    else cart.push({id,name,price,qty:1});
    renderCart();
}

function removeFromCart(id){
    cart = cart.filter(i=>i.id!==id);
    renderCart();
}

function updateQty(id,val){
    let item = cart.find(i=>i.id===id);
    if(item){ item.qty=parseInt(val); renderCart(); }
}

function renderCart(){
    const tbody=document.querySelector('#cartTable tbody');
    tbody.innerHTML='';
    let total=0;

    cart.forEach(item=>{
        total += item.price*item.qty;
        tbody.innerHTML += `
        <tr>
            <td>${item.name}</td>
            <td><input type="number" value="${item.qty}" min="1" onchange="updateQty(${item.id},this.value)"></td>
            <td>RM ${(item.price*item.qty).toFixed(2)}</td>
            <td><button type="button" onclick="removeFromCart(${item.id})">X</button></td>
        </tr>`;
    });

    document.getElementById('totalAmount').innerText = total.toFixed(2);

    document.querySelectorAll('.hiddenItem').forEach(e=>e.remove());
    const form=document.getElementById('posForm');

    cart.forEach(item=>{
        form.innerHTML += `
        <input class="hiddenItem" type="hidden" name="items[${item.id}][menuID]" value="${item.id}">
        <input class="hiddenItem" type="hidden" name="items[${item.id}][qty]" value="${item.qty}">
        <input class="hiddenItem" type="hidden" name="items[${item.id}][price]" value="${item.price}">
        `;
    });
}

// ===== User dropdown =====
const userBox = document.getElementById("userBox");
const dropdown = document.getElementById("dropdownMenu");
userBox.addEventListener("click", e => {
    dropdown.style.display = dropdown.style.display==="block"?"none":"block";
    e.stopPropagation();
});
document.addEventListener("click", e => {
    if(!userBox.contains(e.target)) dropdown.style.display="none";
});
</script>

</body>
</html>
