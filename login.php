<?php
session_start();
include('db.php');

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT customerID, customerName, customerEmail, customerPassword, customerPhoneNo FROM customer WHERE customerEmail = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($pass, $row['customerPassword'])) {
                $_SESSION['user_id'] = $row['customerID'];
                $_SESSION['user_name'] = $row['customerName'];
                $_SESSION['user_phone'] = $row['customerPhoneNo'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Wrong password!";
            }
        } else {
            $error = "No user found with that email!";
        }
    } else {
        $error = "Query preparation failed: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    margin:0;
    padding:0;
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    width: 100vw;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    background: #e8f4ea;
    position: relative;
}

/* Floating icons */
.floating-icon {
    position: absolute;
    width: 50px;
    height: 50px;
    top: 100%;
    pointer-events: none;
    opacity: 0.8;
    z-index: 1;
    animation-name: floatUp;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}
@keyframes floatUp {
    0% { transform: translateY(0) rotate(0deg); }
    100% { transform: translateY(-120vh) rotate(360deg); }
}

/* Login box */
.login-box {
    position: relative;
    z-index: 10;
    background: white;
    padding: 35px 30px;
    border-radius: 15px;
    width: 340px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    text-align: center;
    animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px);}
    to { opacity: 1; transform: translateY(0);}
}

/* Logo above title */
.login-box .logo-title {
    display: flex;
    flex-direction: column; /* Logo atas tulisan */
    align-items: center;
    margin-bottom: 15px;
}
.login-box .logo-title img {
    width: 70px;
    height: 70px;
    margin-bottom: 10px;
}
.login-box .logo-title h2 {
    font-family: 'Pacifico', cursive;
    color: #3b6b45;
    margin: 0;
    font-size: 28px;
}

/* Subtitle */
.login-box .subtitle {
    font-size: 14px;
    color: #555;
    margin-bottom: 15px;
}

/* Form inputs */
.login-box form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}
.login-box input[type="email"],
.login-box input[type="password"] {
    width: 80%;
    max-width: 300px;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: all 0.3s ease;
}
.login-box input:focus {
    border-color: #3b6b45;
    box-shadow: 0 0 5px rgba(59,107,69,0.5);
    outline: none;
}

/* Button */
.login-box button {
    width: 100%;
    background: #3b6b45;
    color: white;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}
.login-box button:hover {
    background: #2e5234;
    transform: scale(1.05);
}

/* Error */
.login-box .error {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Links */
.login-box p {
    font-size: 13px;
    margin-top: 10px;
}
.login-box a {
    color: #3b6b45;
    text-decoration: none;
}
.login-box a:hover { text-decoration: underline; }
</style>
</head>
<body>

<!-- Floating Icons -->
<img src="images/cake.png" class="floating-icon">
<img src="images/croissant.png" class="floating-icon">
<img src="images/tart.png" class="floating-icon">
<img src="images/cinnamonroll.png" class="floating-icon">
<img src="images/choc.png" class="floating-icon">

<div class="login-box">
    <div class="logo-title">
        <img src="bit.png" alt="Bitsy Logo">
        <h2>Bitsy Bakesy</h2>
    </div>
    <p class="subtitle">Welcome back! Please login to continue 🍰</p>
    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="create_account.php">Sign Up</a></p>
    </form>
</div>

<script>
// Randomize floating icon position & speed
const icons = document.querySelectorAll('.floating-icon');
icons.forEach(icon => {
    icon.style.left = Math.random() * 90 + "vw";
    icon.style.animationDuration = (6 + Math.random()*4) + "s";
});
</script>

</body>
</html>
