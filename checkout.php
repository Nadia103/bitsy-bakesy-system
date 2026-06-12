<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_name'])) {
    header("Location: login.php");
    exit();
}

$user_name = $_SESSION['user_name'];

// Dapatkan customerID dan phone
$user_query = mysqli_query($conn, "SELECT customerID, customerPhoneNo FROM customer WHERE customerName='$user_name' LIMIT 1");
$user_data = mysqli_fetch_assoc($user_query);
$customerID = $user_data['customerID'] ?? 0;
$user_phone = $user_data['customerPhoneNo'] ?? '';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo "<script>alert('Your cart is empty!');window.location='menu.php';</script>";
    exit();
}

if (isset($_POST['place_order'])) {
    $phone = $_POST['phone'];
    $payment = $_POST['paymentMethod'];
    $total = 0;

    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Ambil pickup_date dari cart, kalau preorder
    $pickup = $cart[0]['pickup_date'] ?? null;

    // Insert ke table orders
    $order_sql = "INSERT INTO orders (customerID, totalAmount, status, paymentMethod, orderDate, pickupDate)
                  VALUES ('$customerID', '$total', 'Pending', '$payment', NOW(), '$pickup')";
    $result = mysqli_query($conn, $order_sql);
    if (!$result) {
        die("Error placing order: " . mysqli_error($conn));
    }

    $orderID = mysqli_insert_id($conn);
    $today = date('Y-m-d');

    // Insert order_items & update stock jika ready
    foreach ($cart as $item) {
        $unitID = $item['unit_id']; // wajib ikut menu_unit
        $quantity = $item['quantity'];
        $price = $item['price'];
        $pickupDate = $item['pickup_date'] ?? null;

        // Insert order_items
        $item_sql = "INSERT INTO order_items (orderID, unitID, quantity, pickupDate, price)
                     VALUES ('$orderID', '$unitID', '$quantity', '$pickupDate', '$price')";
        $item_result = mysqli_query($conn, $item_sql);
        if (!$item_result) {
            die("Error inserting order item: " . mysqli_error($conn));
        }

        // Update stock jika ready stock (pickup hari ini atau kosong)
        $unitQuery = mysqli_query($conn, "SELECT stockQuantity FROM menu_unit WHERE unitID='$unitID'");
        $unitData = mysqli_fetch_assoc($unitQuery);
        $currentStock = $unitData['stockQuantity'] ?? 0;

        $isReady = empty($pickupDate); // ready stock takde pickup date
        if ($isReady) {
            $newStock = max($currentStock - $quantity, 0);
            mysqli_query($conn, "UPDATE menu_unit SET stockQuantity=$newStock WHERE unitID='$unitID'");
        }
    }

    unset($_SESSION['cart']);
    echo "<script>alert('Order placed successfully!');window.location='my_orders.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout - Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body { font-family: 'Poppins', sans-serif; background:#fafafa; margin:0; color:#333; }
.container { max-width:700px; margin:50px auto; background:white; padding:30px; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.1); }
h2 { font-family:'Pacifico', cursive; color:#3b6b45; margin-bottom:25px; }
label { display:block; margin-top:15px; margin-bottom:5px; font-weight:500; }
input[type=text], select { width:100%; padding:10px; border-radius:8px; border:1px solid #ccc; }
.summary { margin-top:30px; }
.summary h3 { color:#3b6b45; margin-bottom:15px; }
table { width:100%; border-collapse:collapse; margin-bottom:15px; }
th, td { text-align:left; padding:10px; border-bottom:1px solid #ddd; }
th { background:#f5f5f5; }
button { background:#3b6b45; color:white; border:none; padding:12px 20px; border-radius:10px; cursor:pointer; font-size:16px; transition:0.3s; }
button:hover { background:#2e5234; }
</style>
</head>
<body>
<div class="container">
<h2>Checkout</h2>
<form method="POST">
<label>Customer Name</label>
<input type="text" name="customerName" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" disabled>

<label>Phone</label>
<input type="text" name="phone" value="<?php echo htmlspecialchars($user_phone); ?>" required>

<label>Payment Method</label>
<select name="paymentMethod" required>
<option value="">Select Payment</option>
<option>Cash</option>
<option>Online Banking</option>
<option>QR Pay</option>
</select>

<div class="summary">
<h3>Order Summary</h3>
<table>
<tr><th>Item</th><th>Qty</th><th>Pickup Date</th><th>Subtotal</th></tr>
<?php 
$grand_total = 0; 
foreach($cart as $item): 
    $subtotal = $item['price'] * $item['quantity']; 
    $grand_total += $subtotal; 
?>
<tr>
<td><?php echo htmlspecialchars($item['name']); ?></td>
<td><?php echo $item['quantity']; ?></td>
<td><?php echo $item['pickup_date'] ?: '-'; ?></td>
<td>RM <?php echo number_format($subtotal,2); ?></td>
</tr>
<?php endforeach; ?>
</table>
<p><b>Total: RM <?php echo number_format($grand_total,2); ?></b></p>
</div>

<button type="submit" name="place_order">Place Order</button>
</form>
</div>
</body>
</html>
