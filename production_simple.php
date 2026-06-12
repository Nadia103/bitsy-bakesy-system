<?php
session_start();

/* ==============================
   ACCESS CONTROL — BAKER ONLY
   ============================== */
if (!isset($_SESSION['staffID']) || $_SESSION['staffRole'] !== 'Baker') {
    header("Location: staff_login.php");
    exit();
}

/* ==============================
   DATABASE CONNECTION
   ============================== */
$conn = new mysqli("localhost", "root", "", "bitsy");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$staffID   = $_SESSION['staffID'];
$staffName = $_SESSION['staffName'];
$staffRole = $_SESSION['staffRole'];

$message = "";
$selectedMenuName = $_POST['menuName'] ?? '';

/* ==============================
   HANDLE PRODUCTION
   ============================== */
if (isset($_POST['produce'])) {

    $menuName = $conn->real_escape_string($_POST['menuName']);
    $produceQty = intval($_POST['quantity']);
    $selectedMenuName = $menuName;

    // ambil SATU recipe berdasarkan menuName (buang pcs)
    $recipeSQL = "
        SELECT r.recipeID, r.yieldQty
        FROM recipe r
        JOIN menu m ON r.menuID = m.menuID
        WHERE TRIM(REGEXP_REPLACE(m.menuName, '\\\\(.*\\\\)', '')) = '$menuName'
        LIMIT 1
    ";
    $recipeRes = $conn->query($recipeSQL);

    if ($recipeRes->num_rows == 0) {
        $message = "<p style='color:red'>❌ Recipe not found.</p>";
    } else {
        $recipe = $recipeRes->fetch_assoc();
        $recipeID = $recipe['recipeID'];
        $yieldQty = $recipe['yieldQty'];
        $multiplier = $produceQty / $yieldQty;

        // check stock
        $checkSQL = "
            SELECT s.id, s.item_name, s.quantity AS stockQty, r.quantityPerBatch
            FROM recipe_item r
            JOIN stock s ON r.rawMaterialID = s.id
            WHERE r.recipeID = $recipeID
              AND r.quantityPerBatch > 0
        ";
        $items = $conn->query($checkSQL);

        $insufficient = [];
        $lowStock = [];

        while ($row = $items->fetch_assoc()) {
            $neededQty = ceil($row['quantityPerBatch'] * $multiplier);

            if ($row['stockQty'] < $neededQty) {
                $insufficient[] = [
                    'name' => $row['item_name'],
                    'needed' => $neededQty,
                    'available' => $row['stockQty']
                ];
            }

            if ($row['stockQty'] <= 5) {
                $lowStock[] = [
                    'name' => $row['item_name'],
                    'stock' => $row['stockQty']
                ];
            }
        }

        if (!empty($insufficient)) {
            $message = "<p style='color:red'>❌ Insufficient stock for:<br>";
            foreach ($insufficient as $item) {
                $message .= "- <b>{$item['name']}</b>: Needed {$item['needed']}, Available {$item['available']}<br>";
            }
            $message .= "</p>";
        } else {
            // deduct stock
            $items->data_seek(0);
            while ($row = $items->fetch_assoc()) {
                $deductQty = ceil($row['quantityPerBatch'] * $multiplier);
                $conn->query("
                    UPDATE stock
                    SET quantity = GREATEST(quantity - $deductQty, 0)
                    WHERE id = {$row['id']}
                ");
            }

            // update menu stock
            $conn->query("
                UPDATE menu
                SET stockQuantity = stockQuantity + $produceQty
                WHERE menuID = (SELECT menuID FROM menu WHERE TRIM(REGEXP_REPLACE(menuName, '\\\\(.*\\\\)', '')) = '$menuName' LIMIT 1)
            ");

            $message = "<p style='color:green'>✅ Production successful<br>Produced: $produceQty unit(s)</p>";

            if (!empty($lowStock)) {
                $message .= "<p style='color:orange'>⚠ Low stock alert:<br>";
                foreach ($lowStock as $item) {
                    $message .= "- <b>{$item['name']}</b>: Remaining {$item['stock']}<br>";
                }
                $message .= "</p>";
            }
        }
    }
}

/* ==============================
   AJAX FETCH RECIPE
   ============================== */
if (isset($_GET['getRecipe'])) {

    $menuName = $conn->real_escape_string($_GET['getRecipe']);

    // Ambil SATU recipe saja
    $recipeIDRes = $conn->query("
        SELECT r.recipeID
        FROM recipe r
        JOIN menu m ON r.menuID = m.menuID
        WHERE TRIM(REGEXP_REPLACE(m.menuName, '\\\\(.*\\\\)', '')) = '$menuName'
        LIMIT 1
    ");
    if ($recipeIDRes->num_rows == 0) {
        echo "<p>No recipe found for this menu.</p>";
        exit;
    }
    $recipeID = $recipeIDRes->fetch_assoc()['recipeID'];

    $recipeSQL = "
        SELECT s.item_name, r.quantityPerBatch, s.quantity AS stockQty
        FROM recipe_item r
        JOIN stock s ON r.rawMaterialID = s.id
        WHERE r.recipeID = $recipeID
          AND r.quantityPerBatch > 0
    ";
    $res = $conn->query($recipeSQL);

    if ($res->num_rows > 0) {
        echo "<table border='1' cellpadding='5' style='width:100%; border-collapse:collapse; margin-top:10px;'>";
        echo "<tr><th>Raw Material</th><th>Quantity per batch</th><th>Available Stock</th></tr>";
        while ($row = $res->fetch_assoc()) {
            $needed = $row['quantityPerBatch'];
            $stock = $row['stockQty'];
            $color = ($stock < $needed) ? 'style="background:#fdd"' : '';
            echo "<tr $color>
                    <td>{$row['item_name']}</td>
                    <td>{$needed}</td>
                    <td>{$stock}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No recipe found for this menu.</p>";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Production - Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
/* --- Styles sama macam asal awak --- */
body{font-family:'Poppins',sans-serif;margin:0;background:#f4f6f5;color:#333;}
*{box-sizing:border-box;}
header{background:#2e7d32;color:white;padding:18px 30px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 3px 8px rgba(0,0,0,0.2);}
.logo{display:flex;align-items:center;gap:12px;}
.logo img{width:50px;height:50px;border-radius:50%;}
.logo h1{font-family:'Pacifico',cursive;font-size:28px;margin:0;user-select:none;}
.container{display:flex;min-height:100vh;}
.sidebar{width:250px;background:#1b5e20;padding:25px 15px;color:white;position:sticky;top:0;height:100vh;}
.sidebar a{display:block;padding:12px 15px;margin:8px 0;background:rgba(255,255,255,0.07);color:white;text-decoration:none;border-radius:8px;font-weight:500;transition:0.25s ease;}
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
.container-form{max-width:600px;margin:auto;background:white;padding:25px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.15);}
h2{text-align:center;color:#2e7d32;margin-bottom:20px;}
label{font-weight:bold;}
select,input{width:100%;padding:8px;margin-top:6px;margin-bottom:15px;}
button{width:100%;padding:10px;background:#2e7d32;color:white;border:none;border-radius:5px;cursor:pointer;font-size:15px;}
button:hover{background:#1b5e20;}
.msg{text-align:center;margin-bottom:15px;}
.role{text-align:center;font-size:13px;color:#666;margin-bottom:10px;}
#recipeArea{margin-top:15px;}
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
    <a href="staff_dashboard.php">🏠 Dashboard</a>
    <a href="manage_menu.php">🍰 Manage Menu</a>
    <a href="view_customers.php">👥 Customers</a>
    <a href="#" onclick="toggleStockMenu()">📦 Stocks ▾</a>
    <div class="submenu" id="stockSubMenu">
        <a href="manage_raw_material.php">🥖 Raw Material Stock</a>
        <a href="manage_product.php">🍰 Products Stock</a>
    </div>
</div>

<div class="main">
<div class="container-form">
<h2>👩‍🍳 Production</h2>
<div class="role">Access: Baker only</div>
<div class="msg"><?= $message ?></div>

<form method="POST" id="productionForm">
    <label>Menu</label>
    <select name="menuName" id="menuSelect" required>
        <option value="">-- Select Menu --</option>
        <?php
        $menuSQL = "
            SELECT DISTINCT TRIM(REGEXP_REPLACE(menuName, '\\\\(.*\\\\)', '')) AS cleanName
            FROM menu
            GROUP BY cleanName
            ORDER BY cleanName
        ";
        $menuRes = $conn->query($menuSQL);
        while ($menu = $menuRes->fetch_assoc()) {
            $sel = ($menu['cleanName'] == $selectedMenuName) ? 'selected' : '';
            echo "<option value='{$menu['cleanName']}' $sel>{$menu['cleanName']}</option>";
        }
        ?>
    </select>

    <div id="recipeArea"></div>

    <label>Quantity to Produce</label>
    <input type="number" name="quantity" min="1" required>

    <button type="submit" name="produce">Produce</button>
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

// AJAX fetch recipe
function fetchRecipe(menuName){
    if(!menuName){ document.getElementById('recipeArea').innerHTML=''; return; }
    var xhr=new XMLHttpRequest();
    xhr.open("GET","?getRecipe="+encodeURIComponent(menuName),true);
    xhr.onload=function(){ if(xhr.status==200) document.getElementById('recipeArea').innerHTML=xhr.responseText; };
    xhr.send();
}
document.getElementById('menuSelect').addEventListener('change', function(){ fetchRecipe(this.value); });
<?php if($selectedMenuName): ?>
window.onload=function(){ fetchRecipe("<?= $selectedMenuName ?>"); }
<?php endif; ?>
</script>
</body>
</html>
