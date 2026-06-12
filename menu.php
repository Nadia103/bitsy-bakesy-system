<?php
session_start();
include('db.php');

// ===========================
// SEARCH & CATEGORY FILTER
// ===========================
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'All';

$query = "SELECT * FROM menu WHERE menuName LIKE '%$search%'";
if($category != 'All') $query .= " AND category='$category'";
$query .= " ORDER BY menuName ASC";

$result = mysqli_query($conn, $query);

// ===========================
// ADD TO CART
// ===========================
if(isset($_POST['add_to_cart'])){
    if(!isset($_SESSION['user_name'])){
        header("Location: login.php");
        exit();
    }

    $unitID = $_POST['unit_id'];
    $menuName = $_POST['menu_name'] ?? '';
    $price = $_POST['menu_price'] ?? 0;
    $image = $_POST['menu_image'] ?? '';
    $quantity = max(1,(int)($_POST['quantity'] ?? 1));
    $order_type = $_POST['order_type'] ?? 'ready';
    $pickup_date = $_POST['pickup_date'] ?? '';

    // STOCK CHECK (READY ONLY)
    if($order_type==='ready'){
        $stockCheck = mysqli_query($conn,"SELECT stockQuantity FROM menu_unit WHERE unitID='$unitID'");
        $s = mysqli_fetch_assoc($stockCheck);
        if(!$s || $s['stockQuantity'] < $quantity){
            echo "<script>alert('Sorry, not enough stock for this item.');</script>";
            exit();
        }
    }

    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    // Merge if same item exists
    $found = false;
    foreach($_SESSION['cart'] as &$item){
        if($item['unit_id']==$unitID && $item['order_type']==$order_type && $item['pickup_date']==$pickup_date){
            $item['quantity'] += $quantity;
            $found = true;
            break;
        }
    }

    if(!$found){
        $_SESSION['cart'][] = [
            'unit_id' => $unitID,
            'name' => $menuName,
            'price' => $price,
            'image' => $image,
            'quantity' => $quantity,
            'order_type' => $order_type,
            'pickup_date' => $pickup_date
        ];
    }

    echo "<script>alert('Added to cart!');</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Bitsy Bakesy Menu</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* CSS sama macam sebelum ni */
body{font-family:'Poppins',sans-serif;margin:0;background:#fafafa;color:#333;}
.navbar{background:#3b6b45;color:white;display:flex;justify-content:space-between;align-items:center;padding:15px 40px;box-shadow:0 2px 6px rgba(0,0,0,0.15);position:sticky;top:0;z-index:100;}
.navbar-left{display:flex;align-items:center;gap:15px;}
.navbar-left img{height:50px;width:50px;border-radius:50%;border:2px solid white;}
.navbar-left h1{font-family:'Pacifico',cursive;font-size:26px;margin:0;}
.nav-links{display:flex;align-items:center;gap:25px;}
.nav-links a{color:white;text-decoration:none;font-weight:500;position:relative;transition:0.3s;}
.nav-links a::after{content:"";position:absolute;width:0;height:2px;background:white;left:0;bottom:-3px;transition:0.3s;}
.nav-links a:hover::after{width:100%;}
.cart-icon{font-size:22px;color:white;position:relative;text-decoration:none;}
.cart-count{position:absolute;top:-8px;right:-10px;background:#ff4d4d;color:white;font-size:12px;font-weight:bold;border-radius:50%;padding:2px 6px;}
.profile img{width:30px;height:30px;border-radius:50%;cursor:pointer;}
.profile .dropdown{display:none;position:absolute;background:white;color:black;top:60px;right:40px;border-radius:8px;box-shadow:0 4px 8px rgba(0,0,0,0.2);flex-direction:column;min-width:160px;}
.profile:hover .dropdown{display:flex;}
.profile .dropdown a,.profile .dropdown form button{padding:10px 15px;text-align:left;border:none;background:none;cursor:pointer;text-decoration:none;color:black;width:100%;}
.profile .dropdown a:hover,.profile .dropdown form button:hover{background:#f0f0f0;}
.menu-header{display:flex;justify-content:space-between;padding:40px 50px 20px;flex-wrap:wrap;align-items:center;}
.menu-header h2{color:#3b6b45;font-size:32px;font-weight:600;margin:0;font-family:'Pacifico',cursive;}
.filter-box{display:flex;align-items:center;gap:10px;flex-wrap:wrap;justify-content:flex-end;}
.filter-box input{padding:10px 15px;width:220px;border-radius:10px;border:1px solid #ccc;}
.filter-box button{padding:10px 18px;border:none;background:#3b6b45;color:white;border-radius:10px;cursor:pointer;transition:0.3s;position:relative;}
.filter-box button::after{content:"";position:absolute;width:0;height:2px;background:white;left:0;bottom:2px;transition:0.3s;}
.filter-box button:hover::after{width:100%;}
.category-buttons{display:flex;gap:10px;flex-wrap:wrap;justify-content:flex-end;margin-top:10px;}
.category-buttons a{background:#3b6b45;color:white;text-decoration:none;padding:7px 15px;border-radius:8px;font-size:14px;transition:0.3s;position:relative;}
.category-buttons a::after{content:"";position:absolute;width:0;height:2px;background:white;left:0;bottom:0;border-radius:2px;transition:0.3s;}
.category-buttons a:hover::after,.category-buttons a.active::after{width:100%;}
.menu-container{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:30px;padding:30px 50px 60px;}
.product-card{background:white;border-radius:20px;box-shadow:0 6px 15px rgba(0,0,0,0.08);overflow:hidden;text-align:center;display:flex;flex-direction:column;justify-content:space-between;transition:transform 0.3s,box-shadow 0.3s;}
.product-card:hover{transform:translateY(-6px);box-shadow:0 10px 25px rgba(0,0,0,0.15);}
.product-card img{width:100%;height:210px;object-fit:cover;}
.product-info{padding:20px;display:flex;flex-direction:column;justify-content:space-between;flex-grow:1;min-height:220px;}
.product-info h3{margin:0 0 10px 0;color:#2f4f34;}
.product-info p{flex-grow:1;font-size:14px;color:#555;}
.price{color:#3b6b45;font-weight:bold;font-size:18px;margin-top:10px;}
.btn-order{background:#3b6b45;color:white;border:none;padding:10px 15px;border-radius:10px;cursor:pointer;transition:0.3s;margin-top:12px;position:relative;}
.btn-order::after{content:"";position:absolute;width:0;height:2px;background:white;left:0;bottom:2px;transition:0.3s;}
.btn-order:hover::after{width:100%;}
.modal{display:none;position:fixed;z-index:1000;left:0;top:0;width:100%;height:100%;background:rgba(0,0,0,0.5);}
.modal-content{background:white;margin:8% auto;padding:25px 30px;border-radius:15px;width:90%;max-width:420px;box-shadow:0 6px 20px rgba(0,0,0,0.2);}
.close{float:right;font-size:24px;cursor:pointer;color:#666;}
.close:hover{color:#000;}
.modal-content form{display:flex;flex-direction:column;gap:12px;}
.modal-content input,.modal-content select{padding:10px;border-radius:8px;border:1px solid #ccc;}
footer{background:#3b6b45;color:white;text-align:center;padding:15px;margin-top:40px;font-size:14px;}
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
<div class="navbar-left">
<img src="bit.png" alt="Bitsy Logo">
<h1>Bitsy Bakesy Bakery</h1>
</div>
<div class="nav-links">
<a href="index.php">Home</a>
<a href="menu.php" style="color:#d4f5d0;">Menu</a>
<a href="cart.php" class="cart-icon">🛒
<?php if (!empty($_SESSION['cart'])): ?>
<span class="cart-count"><?php echo array_sum(array_column($_SESSION['cart'],'quantity')); ?></span>
<?php endif; ?>
</a>
<?php if(isset($_SESSION['user_name'])): ?>
<div class="profile">
<img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
<div class="dropdown">
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

<!-- FILTER HEADER -->
<div class="menu-header">
<h2>Our Menu</h2>
<div>
<form method="GET" class="filter-box">
<input type="text" name="search" placeholder="Search product..." value="<?php echo htmlspecialchars($search); ?>">
<button type="submit">Search</button>
</form>
<div class="category-buttons">
<?php
$categories=['All','Cake','Pastry','Dessert','What’s New'];
foreach($categories as $cat){
    $active = ($category==$cat)?'active':'';
    echo "<a href='menu.php?category=$cat' class='$active'>$cat</a>";
}
?>
</div>
</div>
</div>

<!-- PRODUCT GRID -->
<div class="menu-container">
<?php
if($result && mysqli_num_rows($result)>0):
$displayed = [];
while($row=mysqli_fetch_assoc($result)):
    $menuNameFull = $row['menuName'];
    $baseName = explode('(',$menuNameFull)[0];
    $baseName = trim($baseName);
    if(in_array($baseName,$displayed)) continue;
    $displayed[] = $baseName;

    // Ambil semua unit untuk baseName
    $unitQuery = "SELECT mu.* FROM menu_unit mu
                  JOIN menu m ON m.menuID=mu.menuID
                  WHERE m.menuName LIKE '".mysqli_real_escape_string($conn,$baseName)."%' ";
    $uResult = mysqli_query($conn,$unitQuery);
    $variations = [];
    $stockAvailable = false;
    while($x=mysqli_fetch_assoc($uResult)){
        $variations[]=$x;
        if($x['stockQuantity']>0) $stockAvailable = true;
    }

    if(empty($variations)) continue;

    $prices = array_column($variations,'price');
    $minPrice = min($prices);
    $maxPrice = max($prices);
?>
<div class="product-card">
<img src="images/<?php echo htmlspecialchars($row['image']); ?>">
<div class="product-info">
<h3><?php echo htmlspecialchars($baseName); ?></h3>
<p><?php echo htmlspecialchars($row['description']); ?></p>
<div class="price">
RM <?php echo number_format($minPrice,2); ?>
<?php if($minPrice!=$maxPrice) echo " - RM ".number_format($maxPrice,2); ?>
</div>
<?php if(isset($_SESSION['user_name'])): ?>
<button class="btn-order" onclick='openModal(<?php echo json_encode($variations); ?>,"<?php echo htmlspecialchars($baseName); ?>","<?php echo htmlspecialchars($row["image"]); ?>", <?php echo $stockAvailable?1:0; ?>)'>Order</button>
<?php else: ?>
<button class="btn-order" onclick="window.location.href='login.php';">Order</button>
<?php endif; ?>
</div>
</div>
<?php endwhile; endif; ?>
</div>

<!-- MODAL -->
<div id="orderModal" class="modal">
<div class="modal-content">
<span class="close" onclick="closeModal()">&times;</span>
<form method="POST" action="menu.php">
<input type="hidden" name="unit_id" id="modalUnitID">
<input type="hidden" name="menu_name" id="modalMenuName">
<input type="hidden" name="menu_price" id="modalMenuPrice">
<input type="hidden" name="menu_image" id="modalMenuImage">
<label>Unit Type:</label>
<select id="modalUnitSelect" required></select>
<label>Order Type:</label>
<select name="order_type" id="orderTypeSelect" onchange="togglePickupDate()">
<option value="ready">Ready</option>
<option value="preorder">Preorder</option>
<option value="custom">Custom</option>
</select>
<label id="pickupLabel" style="display:none;">Pickup Date</label>
<input type="date" name="pickup_date" id="pickupDateInput" style="display:none;">
<label>Quantity:</label>
<input type="number" name="quantity" min="1" value="1">
<button type="submit" name="add_to_cart" class="btn-order">Add to Cart</button>
</form>
</div>
</div>

<footer>&copy; 2025 Bitsy Bakesy Bakery</footer>

<script>
function openModal(variations,name,image,stockAvailable){
    const select=document.getElementById('modalUnitSelect');
    select.innerHTML='';
    variations.forEach(v=>{
        let option=document.createElement('option');
        option.value=v.unitID;
        option.text=(v.unitType?v.unitType:'Default')+' - RM '+parseFloat(v.price).toFixed(2);
        select.add(option);
    });
    let first=variations[0];
    document.getElementById('modalUnitID').value=first.unitID;
    document.getElementById('modalMenuName').value=name;
    document.getElementById('modalMenuPrice').value=first.price;
    document.getElementById('modalMenuImage').value=image;

    // Disable Ready jika stok habis
    const orderSelect=document.getElementById('orderTypeSelect');
    Array.from(orderSelect.options).forEach(o=>{
        if(o.value==='ready') o.disabled = !stockAvailable;
    });
    orderSelect.value = stockAvailable ? 'ready' : 'preorder';

    select.onchange=function(){
        let chosen=variations.find(v=>v.unitID==select.value);
        document.getElementById('modalUnitID').value=chosen.unitID;
        document.getElementById('modalMenuPrice').value=chosen.price;
    };
    document.getElementById('orderModal').style.display='block';
}
function closeModal(){document.getElementById('orderModal').style.display='none';}
function togglePickupDate(){
    const type=document.getElementById('orderTypeSelect').value;
    const label=document.getElementById('pickupLabel');
    const input=document.getElementById('pickupDateInput');
    const menuName=document.getElementById('modalMenuName').value;
    if(type==='preorder'){label.style.display='block';input.style.display='block';input.required=true;}
    else if(type==='custom'){
        label.style.display='none';
        input.style.display='none';
        const phone="60123456789";
        const msg=`Hi Bitsy Bakesy! 👋 I want to custom order: ${menuName}`;
        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(msg)}`,'_blank');
    }
    else{label.style.display='none';input.style.display='none';input.required=false;}
}
</script>
</body>
</html>
