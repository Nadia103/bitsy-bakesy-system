<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account - Bitsy Bakesy Bakery</title>
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
    background: #fef6f0;
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

/* Form box */
.create-account-box {
    position: relative;
    z-index: 10;
    background: white;
    padding: 35px 30px;
    border-radius: 15px;
    width: 380px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    text-align: center;
    animation: fadeIn 0.5s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px);}
    to { opacity: 1; transform: translateY(0);}
}

/* Logo + title */
.create-account-box .logo-title {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 10px;
}
.create-account-box .logo-title img {
    width: 70px;
    height: 70px;
    margin-bottom: 10px;
}
.create-account-box .logo-title h2 {
    font-family: 'Pacifico', cursive;
    color: #3b6b45;
    margin: 0;
    font-size: 28px;
}

/* Subtitle */
.create-account-box .subtitle {
    font-size: 14px;
    color: #555;
    margin-bottom: 15px;
}

/* Form */
.create-account-box form {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}
.create-account-box input[type="text"],
.create-account-box input[type="email"],
.create-account-box input[type="tel"],
.create-account-box input[type="password"] {
    width: 80%;
    max-width: 300px;
    padding: 12px 15px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: all 0.3s ease;
}
.create-account-box input:focus {
    border-color: #3b6b45;
    box-shadow: 0 0 5px rgba(59,107,69,0.5);
    outline: none;
}

/* Button */
.create-account-box button {
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
.create-account-box button:hover {
    background: #2e5234;
    transform: scale(1.05);
}

/* Footer small text */
.create-account-box p {
    font-size: 13px;
    margin-top: 10px;
}
.create-account-box a {
    color: #3b6b45;
    text-decoration: none;
}
.create-account-box a:hover { text-decoration: underline; }
</style>
</head>
<body>

<!-- Floating Icons -->
<img src="images/cake.png" class="floating-icon">
<img src="images/croissant.png" class="floating-icon">
<img src="images/tart.png" class="floating-icon">
<img src="images/cinnamonroll.png" class="floating-icon">
<img src="images/choc.png" class="floating-icon">

<div class="create-account-box">
    <div class="logo-title">
        <img src="bit.png" alt="Bitsy Logo">
        <h2>Bitsy Bakesy</h2>
    </div>
    <p class="subtitle">Create your sweet account 🍰</p>
    <form action="process_signup.php" method="POST">
        <input type="text" id="name" name="name" placeholder="Full Name" required>
        <input type="email" id="email" name="email" placeholder="Email" required>
        <input type="tel" id="phone" name="phone" pattern="[0-9]{10,15}" placeholder="Phone Number" required>
        <input type="password" id="password" name="password" placeholder="Password" required>
        <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
        <button type="submit">Create Account</button>
        <p>Already have an account? <a href="login.php">Login</a></p>
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
