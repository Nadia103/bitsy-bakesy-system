<?php
session_start();

// Sambung ke database
$conn = mysqli_connect("localhost", "root", "", "bitsy");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['staffEmail'] ?? '');
    $password = trim($_POST['staffPassword'] ?? '');

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM staff WHERE staffEmail = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            if ($password === $row['staffPassword']) {
                // Set session
                $_SESSION['staffID'] = $row['staffID'];
                $_SESSION['staffName'] = $row['staffName'];
                $_SESSION['staffRole'] = $row['staffRole'];

                // Redirect berdasarkan role
                switch($row['staffRole']) {
                    case 'Admin':
                        header("Location: admin_dashboard.php");
                        break;
                    case 'Cashier':
                        header("Location: cashier_dashboard.php");
                        break;
                    default:
                        header("Location: staff_dashboard.php");
                        break;
                }
                exit();
            } else {
                $error = "Invalid email or password!";
            }
        } else {
            $error = "Invalid email or password!";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Staff Login | Bitsy Bakesy</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
body {
    margin:0;
    font-family: 'Poppins', sans-serif;
    height: 100vh;
    width: 100vw;
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    background: linear-gradient(120deg, #e8f4ea);
    background-size: 400% 400%;
    animation: gradientBG 15s ease infinite;
    position: relative;
}

/* Floating icons */
.floating-icon {
    position: absolute;
    width: 50px;
    height: 50px;
    top: 100%;
    pointer-events: none;
    opacity: 0.7;
    z-index: 1;
    animation-name: floatUp;
    animation-timing-function: linear;
    animation-iteration-count: infinite;
}
@keyframes floatUp {
    0% { transform: translateY(0) rotate(0deg); }
    100% { transform: translateY(-120vh) rotate(360deg); }
}

@keyframes gradientBG {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Login card */
.login-box {
    position: relative;
    z-index: 10;
    background: white;
    padding: 40px 30px;
    border-radius: 15px;
    width: 360px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    text-align: center;
    animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
    from { opacity:0; transform: translateY(-10px); }
    to { opacity:1; transform: translateY(0); }
}

/* Logo */
.logo-title {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 15px;
}
.logo-title img {
    width: 70px;
    height: 70px;
    margin-bottom: 10px;
    transition: transform 0.5s ease;
}
.logo-title img:hover { transform: rotate(-5deg) scale(1.05); }

h2 { font-family: 'Pacifico', cursive; color: #3b6b45; margin: 0 0 15px; font-size: 28px; }
.subtitle { font-size: 14px; color: #555; margin-bottom: 15px; }

/* Inputs */
.login-box form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}
.login-box input {
    width: 80%;
    max-width: 300px;
    padding: 12px 15px;
    border-radius: 8px;
    border:1px solid #ccc;
    font-size: 14px;
    transition: all 0.3s ease;
    text-align: center;
}
.login-box input:focus {
    border-color: #3b6b45;
    box-shadow: 0 0 6px rgba(59,107,69,0.4);
    outline: none;
}

/* Button */
.login-box button {
    width: 80%;
    max-width: 300px;
    padding: 12px;
    background: #3b6b45;
    color: white;
    border: none;
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

/* Footer links */
.login-box p { font-size: 13px; margin-top:10px; }
.login-box a { color:#3b6b45; text-decoration:none; }
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
        <h2>Bitsy Staff Login</h2>
    </div>
    <p class="subtitle">Welcome back! Please login to continue 🍰</p>
    <?php if(!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
        <input type="email" name="staffEmail" placeholder="Enter Email" required>
        <input type="password" name="staffPassword" placeholder="Enter Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="create_staff_account.php">Sign Up</a></p>
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
