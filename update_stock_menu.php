<?php
session_start();
$conn = new mysqli("localhost", "root", "", "bitsy");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Check ID
if(!isset($_GET['id']) || empty($_GET['id'])) die("Unit ID required.");

$unitID = intval($_GET['id']);

$result = $conn->query("
SELECT u.*, m.menuName 
FROM menu_unit u
JOIN menu m ON m.menuID = u.menuID
WHERE u.unitID = $unitID LIMIT 1
");

if(!$result || $result->num_rows == 0) die("Unit not found.");
$unit = $result->fetch_assoc();

// Update
if(isset($_POST['update_stock'])){
    $change = intval($_POST['stock_change']);
    $newStock = max(0, $unit['stockQuantity'] + $change);
    $newMin = max(0, intval($_POST['minLevel']));

    $conn->query("
        UPDATE menu_unit 
        SET stockQuantity = $newStock, minLevel = $newMin
        WHERE unitID = $unitID
    ");

    echo "<script>alert('Stock updated successfully!');window.location='manage_product.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Update Stock - <?= htmlspecialchars($unit['menuName'].' ('.$unit['unitType'].')') ?></title>
<style>
body{font-family:Arial;background:#f8fafc;padding:20px;}
.container{max-width:400px;margin:40px auto;background:white;padding:20px;border-radius:10px;box-shadow:0 4px 10px rgba(0,0,0,0.1);}
input{width:100%;padding:10px;margin:10px 0;border-radius:6px;border:1px solid #ccc;}
button{width:100%;padding:10px;background:#27ae60;color:white;border:none;border-radius:6px;font-size:16px;cursor:pointer;}
button:hover{opacity:0.9;}
</style>
</head>
<body>
<div class="container">
<h2>Update Stock</h2>
<p><b><?= htmlspecialchars($unit['menuName'].' ('.$unit['unitType'].')') ?></b></p>
<p>Current Stock: <b><?= $unit['stockQuantity'] ?></b></p>
<p>Min Level: <b><?= $unit['minLevel'] ?></b></p>

<form method="POST">
    <label>Update Stock</label>
    <input type="number" name="stock_change" value="0" required>

    <label>Min Level</label>
    <input type="number" name="minLevel" value="<?= $unit['minLevel'] ?>" required>

    <button type="submit" name="update_stock">Update</button>
</form>

<p style="text-align:center;margin-top:10px;">
    <a href="manage_product.php">← Back</a>
</p>
</div>
</body>
</html>
