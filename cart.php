<?php
session_start();

// If cart not exist, create empty array
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle remove item
if (isset($_GET['remove'])) {
    $removeID = $_GET['remove'];
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['unit_id'] == $removeID) { // guna unit_id
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            break;
        }
    }
}

// Calculate total price
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>

<style>
body{
    font-family:'Poppins',sans-serif;
    margin:0;
    background:#f8f9fa;
    color:#333;
}
.cart-container{
    max-width:900px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 6px 20px rgba(0,0,0,0.1);
}
h2{
    text-align:center;
    margin-bottom:25px;
    color:#3b6b45;
    font-family:'Pacifico',cursive;
}
.cart-item{
    display:flex;
    align-items:center;
    border-bottom:1px solid #eee;
    padding:15px 0;
    gap:20px;
}
.cart-item img{
    width:110px;
    height:110px;
    object-fit:cover;
    border-radius:12px;
}
.item-details{
    flex:1;
}
.item-details h4{
    margin:0 0 5px 0;
    font-size:18px;
    color:#3b6b45;
}
.item-details p{
    margin:3px 0;
    color:#555;
}
.remove-btn{
    color:white;
    background:#cc0000;
    padding:8px 15px;
    border-radius:8px;
    text-decoration:none;
    font-size:14px;
}
.remove-btn:hover{
    background:#a30000;
}
.total-box{
    text-align:right;
    margin-top:20px;
    font-size:20px;
    font-weight:bold;
    color:#3b6b45;
}

/* Shop More Button */
.shop-more-btn{
    display:block;
    width:100%;
    background:#f1f1f1;
    color:#3b6b45;
    padding:14px;
    border-radius:10px;
    text-align:center;
    font-size:18px;
    margin-top:15px;
    text-decoration:none;
    border:2px solid #3b6b45;
}
.shop-more-btn:hover{
    background:#3b6b45;
    color:white;
}

/* Checkout Button */
.checkout-btn{
    display:block;
    width:100%;
    background:#3b6b45;
    color:white;
    padding:14px;
    border-radius:10px;
    text-align:center;
    font-size:18px;
    margin-top:15px;
    text-decoration:none;
}
.checkout-btn:hover{
    background:#2a553e;
}
</style>
</head>

<body>

<div class="cart-container">
<h2>Your Cart</h2>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Your cart is empty.</p>
    <a href="menu.php" class="shop-more-btn">← Shop More</a>

<?php else: ?>

<?php foreach ($_SESSION['cart'] as $item): ?>
<?php $unitDisplay = $item['unitType'] ?? ''; ?>

<div class="cart-item">
    <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="Menu Image">

    <div class="item-details">
        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
        <p>
            RM <?php echo number_format($item['price'],2); ?>
            × <?php echo $item['quantity']; ?>
            <?php if($unitDisplay !== ''): ?>
                (<?php echo htmlspecialchars($unitDisplay); ?>)
            <?php endif; ?>
        </p>
        <p><strong>Subtotal:</strong>
            RM <?php echo number_format($item['price'] * $item['quantity'],2); ?>
        </p>
    </div>

    <a class="remove-btn" href="cart.php?remove=<?php echo $item['unit_id']; ?>">Remove</a>
</div>

<?php endforeach; ?>

<div class="total-box">
Total: RM <?php echo number_format($total,2); ?>
</div>

<a href="menu.php" class="shop-more-btn">← Shop More</a>

<a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>

<?php endif; ?>

</div>

</body>
</html>
