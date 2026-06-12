<?php
session_start();

if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) { 
    die("Connection failed: " . mysqli_connect_error()); 
}

$staffID   = $_SESSION['staffID'];
$staffName = $_SESSION['staffName'];
$staffRole = $_SESSION['staffRole'];

$error = "";

// Dapatkan menuID
$menuID = $_GET['id'] ?? null;
if (!$menuID) {
    header("Location: manage_menu.php");
    exit();
}

// Ambil data menu + unit
$stmt = $conn->prepare("
    SELECT m.*, u.unitType, u.price AS unitPrice
    FROM menu m
    LEFT JOIN menu_unit u ON m.menuID = u.menuID
    WHERE m.menuID = ? LIMIT 1
");
$stmt->bind_param("i", $menuID);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    header("Location: manage_menu.php");
    exit();
}
$menu = $result->fetch_assoc();
$stmt->close();

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menuName = trim($_POST['menuName'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $unitType = trim($_POST['unitType'] ?? '');
    $price = floatval($_POST['price'] ?? 0);

    $imageName = $menu['image'];

    if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
        $targetDir = "images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $error = "Failed to upload image.";
        }
    }

    if ($menuName == "" || $price <= 0 || $category == "" || $unitType == "") {
        $error = "Please fill in all fields correctly.";
    }

    if ($error == "") {
        // Update menu
        $stmt = $conn->prepare("UPDATE menu SET menuName=?, description=?, category=?, image=? WHERE menuID=?");
        $stmt->bind_param("ssssi", $menuName, $description, $category, $imageName, $menuID);
        $stmt->execute();
        $stmt->close();

        // Update menu_unit (hanya unitType + price)
        $stmt2 = $conn->prepare("UPDATE menu_unit SET unitType=?, price=? WHERE menuID=?");
        $stmt2->bind_param("sdi", $unitType, $price, $menuID);
        $stmt2->execute();
        $stmt2->close();

        header("Location: manage_menu.php");
        exit();
    }
}

$allUnits = ['slice', 'whole', 'piece', 'box', 'set'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Update Menu | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
*{box-sizing:border-box;}
header{background:#2e7d32;color:white;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 3px 8px rgba(0,0,0,0.2);}
.logo{display:flex;align-items:center;gap:12px;}
.logo img{width:50px;height:50px;border-radius:50%;}
.logo h1{font-family:'Pacifico',cursive;font-size:28px;margin:0;user-select:none;}
.user-box{display:flex;align-items:center;gap:10px;position:relative;cursor:pointer;}
.user-box .user-img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid white;transition: transform 0.2s ease;}
.user-box:hover .user-img{transform: scale(1.1);}
.user-box span{font-weight:500;color:white;user-select:none;}
.dropdown{display:none;position:absolute;top:50px;right:0;background:white;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.15);min-width:180px;z-index:100;overflow:hidden;font-size:14px;}
.dropdown a, .dropdown button{display:block;padding:10px 15px;color:#2e7d32;text-decoration:none;width:100%;text-align:left;background:none;border:none;cursor:pointer;transition:0.2s ease;}
.dropdown a:hover, .dropdown button:hover{background:#f0f4f0;}
.dropdown button{text-align:left;}
.container{display:flex;min-height:100vh;}
.sidebar{width:250px;background:#1b5e20;padding:25px 15px;color:white;position:sticky;top:0;height:100vh;}
.sidebar a{display:block;align-items:center;gap:10px;padding:12px 15px;margin:8px 0;background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;transition:0.25s ease;}
.sidebar a:hover{background:#2e7d32;transform:translateX(6px);}
.submenu{display:none;margin-left:15px;}
.submenu a{background:rgba(255,255,255,0.15);font-size:14px;}
.main{flex:1;padding:40px;}
.card{background:white;padding:25px;border-radius:18px;box-shadow:0 4px 16px rgba(0,0,0,0.15);max-width:600px;margin:auto;}
.card h2{text-align:center;color:#2e7d32;}
input, select, textarea{width:100%;padding:10px;margin:10px 0;border-radius:6px;border:1px solid #ccc;}
button{background:#2e7d32;color:white;padding:12px;width:100%;border:none;border-radius:6px;cursor:pointer;margin-top:10px;}
button:hover{background:#43a047;}
.error{color:red;text-align:center;}
img.menu-img{display:block;margin:10px auto;max-width:120px;border-radius:10px;}
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
            <form method="POST" action="staff_logout.php"><button type="submit">Logout</button></form>
        </div>
    </div>
</header>

<div class="container">
<div class="sidebar">
    <a href="staff_dashboard.php">📊 Dashboard</a>
    <a href="manage_menu.php">🍰 Manage Menu</a>
    <?php if($staffRole==='Admin' || $staffRole==='Baker'): ?>
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

<div class="main">
    <div class="card">
        <h2>Update Menu</h2>
        <?php if ($error) echo "<div class='error'>$error</div>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Menu Name</label>
            <input type="text" name="menuName" value="<?= htmlspecialchars($menu['menuName']); ?>" required>

            <label>Description</label>
            <textarea name="description"><?= htmlspecialchars($menu['description']); ?></textarea>

            <label>Category</label>
            <input type="text" name="category" value="<?= htmlspecialchars($menu['category']); ?>" required>

            <label>Unit Type</label>
            <select name="unitType" required>
            <?php
            foreach ($allUnits as $u) {
                $sel = ($u == $menu['unitType']) ? "selected" : "";
                echo "<option value='$u' $sel>$u</option>";
            }
            ?>
            </select>

            <label>Price (RM)</label>
            <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($menu['unitPrice']); ?>" required>

            <?php if ($menu['image']) { ?>
            <label>Current Image</label>
            <img src="images/<?= htmlspecialchars($menu['image']); ?>" class="menu-img">
            <?php } ?>

            <label>Upload New Image</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Update Menu</button>
        </form>
        
    </div>
</div>

</div>

<script>
function toggleStockMenu(){
    let m=document.getElementById("stockSubMenu");
    m.style.display = m.style.display==="block"?"none":"block";
}

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
<?php $conn->close(); ?>
