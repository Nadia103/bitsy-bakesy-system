<?php
// ========================
// ERROR REPORTING
// ========================
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ========================
// SESSION CHECK
// ========================
session_start();
if (!isset($_SESSION['staffID'])) {
    header("Location: staff_login.php");
    exit();
}

// ========================
// DATABASE CONNECTION
// ========================
$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$staffName = $_SESSION['staffName'];
$staffRole = $_SESSION['staffRole'];
$error = "";

// ========================
// HANDLE FORM SUBMIT
// ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $menuName   = trim($_POST['menuName'] ?? '');
    $description= trim($_POST['description'] ?? '');
    $category   = trim($_POST['category'] ?? '');
    $unitType   = trim($_POST['unitType'] ?? '');
    $price      = floatval($_POST['price'] ?? 0);

    // UPLOAD IMAGE
    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "images/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $imageName = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $error = "Failed to upload image.";
        }
    }

    // VALIDASI
    if ($menuName == "" || $description == "" || $category == "" || $unitType == "" || $price <= 0) {
        $error = "Please fill in all fields.";
    }

    if ($error == "") {

        // INSERT INTO menu
        $stmt = $conn->prepare("INSERT INTO menu (menuName, description, category, image) VALUES (?, ?, ?, ?)");
        if (!$stmt) $error = "Prepare failed (menu): " . $conn->error;
        else {
            $stmt->bind_param("ssss", $menuName, $description, $category, $imageName);
            if ($stmt->execute()) {
                $menuID = $conn->insert_id;

                // INSERT INTO menu_unit
                $stmt2 = $conn->prepare("INSERT INTO menu_unit (menuID, unitType, price) VALUES (?, ?, ?)");
                if (!$stmt2) $error = "Prepare failed (menu_unit): " . $conn->error;
                else {
                    $stmt2->bind_param("isd", $menuID, $unitType, $price);
                    if ($stmt2->execute()) {
                        header("Location: manage_menu.php");
                        exit();
                    } else {
                        $error = "Failed to add menu unit: " . $stmt2->error;
                    }
                    $stmt2->close();
                }

            } else {
                $error = "Failed to add menu: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add New Menu | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family:'Poppins',sans-serif; background:#f4f6f5; margin:0; }
header{background:#2e7d32;color:white;padding:15px 25px;display:flex;justify-content:space-between;align-items:center;}
header .logo { display:flex; align-items:center; gap:12px; }
header .logo img{ width:50px; height:50px; border-radius:50%; object-fit:cover; }
header h1{font-family:'Pacifico',cursive;margin:0;font-size:28px;color:white;}
.user-box{display:flex;align-items:center;gap:10px;position:relative;cursor:pointer;}
.user-box .user-img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid white;}
.dropdown{display:none;position:absolute;top:50px;right:0;background:white;border-radius:10px;box-shadow:0 6px 20px rgba(0,0,0,0.15);min-width:160px;z-index:100;}
.dropdown a, .dropdown button{display:block;padding:10px 15px;color:#2e7d32;text-decoration:none;width:100%;background:none;border:none;text-align:left;cursor:pointer;}
.dropdown a:hover, .dropdown button:hover{background:#f0f4f0;}
.container{display:flex;min-height:100vh;}
.sidebar{width:240px;background:#1b5e20;color:white;padding:20px;}
.sidebar a{display:block;padding:12px 15px;margin:8px 0;color:white;text-decoration:none;border-radius:8px;background:rgba(255,255,255,0.07);}
.main{flex:1;padding:30px;}
.card{background:white;padding:30px;border-radius:10px;max-width:600px;margin:auto;}
label{font-weight:600;display:block;margin-top:10px;}
input, select, textarea{width:100%;padding:10px;margin:6px 0;border-radius:5px;border:1px solid #ccc;}
textarea{resize:vertical;}
button{width:100%;padding:12px;background:#2e7d32;color:white;border:none;border-radius:5px;margin-top:10px;cursor:pointer;}
button:hover{background:#43a047;}
.error{color:red;text-align:center;margin-bottom:10px;}
a.back-link{display:block;margin-top:10px;text-align:center;color:#2e7d32;text-decoration:none;}
a.back-link:hover{text-decoration:underline;}
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
</div>

<div class="main">
<h2>Add New Menu</h2>
<div class="card">

<?php if ($error) echo "<div class='error'>$error</div>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label>Menu Name</label>
    <input type="text" name="menuName" required>

    <label>Description</label>
    <textarea name="description" required></textarea>

    <label>Category</label>
    <select name="category" required>
        <option value="">-- Select Category --</option>
        <option value="Cake">Cake</option>
        <option value="What's New">What's New</option>
        <option value="Pastry">Pastry</option>
        <option value="Dessert">Dessert</option>
    </select>

    <label>Unit Type</label>
    <select name="unitType" required>
        <option value="">-- Select Unit Type --</option>
        <option value="slice">Slice</option>
        <option value="piece">Piece</option>
        <option value="box">Box</option>
        <option value="set">Set</option>
    </select>

    <label>Price (RM)</label>
    <input type="number" step="0.01" name="price" required>

    <label>Image</label>
    <input type="file" name="image">

    <button type="submit">Add Menu</button>
</form>

<a href="manage_menu.php" class="back-link">← Back to Manage Menu</a>

</div>
</div>
</div>

<script>
const userBox=document.getElementById("userBox");
const dropdown=document.getElementById("dropdownMenu");
userBox.onclick=e=>{
    dropdown.style.display=dropdown.style.display==="block"?"none":"block";
    e.stopPropagation();
};
document.onclick=()=>dropdown.style.display="none";
</script>

</body>
</html>
