<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bitsy Bakesy Bakery - Home</title>

    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; scroll-behavior: smooth; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #f6f1e7;
        }

        /* NAVBAR */
        .navbar {
            background-color: #3b6b45;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            position: sticky;
            top: 0;
            z-index: 100;
            transition: background 0.3s;
        }
        .navbar.scrolled { background-color: #2f5237; box-shadow: 0 2px 10px rgba(0,0,0,0.2); }
        .navbar-left { display: flex; align-items: center; gap: 15px; }
        .navbar-left img { height: 55px; width: 55px; border-radius: 50%; background-color: white; border: 2px solid white; object-fit: cover; }
        .navbar-left h1 { font-family: 'Pacifico', cursive; font-size: 28px; margin: 0; }

        .nav-links { display: flex; align-items: center; gap: 25px; }
        .nav-links a {
            color: white; text-decoration: none; font-weight: 500; position: relative; transition: 0.3s;
        }
        .nav-links a::after {
            content: ""; position: absolute; bottom: -5px; left: 0;
            width: 0; height: 2px; background-color: #e0ffd5; transition: width 0.3s;
        }
        .nav-links a:hover::after { width: 100%; }

        /* PROFILE */
        .profile { position: relative; display: inline-block; cursor: pointer; }
        .profile-btn { display: flex; align-items: center; gap: 8px; }
        .profile img { width: 35px; height: 35px; border-radius: 50%; }
        .profile span { color: white; font-weight: 500; }
        .dropdown {
            display: none; position: absolute; right: 0; background-color: white; min-width: 160px;
            border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden;
        }
        .dropdown a, .dropdown button {
            color: #3b6b45; padding: 10px 15px; display: block; text-align: left;
            background: none; border: none; font-size: 15px; width: 100%; cursor: pointer;
        }
        .dropdown a:hover, .dropdown button:hover { background-color: #e8f5e9; }

        /* BANNER VIDEO */
        .banner {
            position: relative;
            width: 100%;
            height: 90vh;
            overflow: hidden;
            margin-top: 0;
            padding: 0;
        }
        .banner video {
            position: absolute;
            top: 0;
            left: 50%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: translateX(-50%);
            filter: brightness(70%);
        }
        .banner-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            text-align: center;
            z-index: 2;
        }
        .banner-text h2 {
            font-family: 'Pacifico', cursive;
            font-size: 48px;
            margin: 0;
        }
        .banner-text p { font-size: 18px; }

        /* RIBBON */
        .ribbon {
            background-color: #3b6b45;
            color: white;
            text-align: center;
            padding: 12px 0;
            font-family: 'Pacifico', cursive;
            font-size: 22px;
        }

        /* ABOUT SECTION WITH IMAGE */
        .about {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 40px;
            padding: 80px 20px;
            background-color: #f6f1e7;
        }
        .about img {
            width: 420px;
            height: 320px;
            border-radius: 20px;
            object-fit: cover;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .about-text {
            max-width: 550px;
            text-align: left;
        }
        .about-text h2 {
            font-family: 'Pacifico', cursive;
            color: #3b6b45;
            font-size: 38px;
            margin-bottom: 15px;
        }
        .about-text p {
            color: #4b3d2a;
            font-size: 16px;
            line-height: 1.8;
        }

        /* PRODUCTS */
        .products {
            background-color: #e7dbc3;
            padding: 60px 20px;
            text-align: center;
        }
        .products h2 {
            font-family: 'Pacifico', cursive;
            color: #3b6b45;
            font-size: 36px;
        }
        .product-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 40px;
            margin-top: 30px;
        }
        .product {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 270px;
            padding-bottom: 15px;
            transition: all 0.4s ease;
        }
        .product:hover { transform: translateY(-10px) scale(1.03); }
        .product img {
            width: 100%; height: 200px; object-fit: cover;
            border-top-left-radius: 15px; border-top-right-radius: 15px;
        }
        .product h3 { font-family: 'Pacifico', cursive; color: #3b6b45; margin: 10px 0; }

        /* GALLERY */
        .gallery {
            background-color: #f6f1e7;
            padding: 60px 20px;
            text-align: center;
        }
        .gallery h2 {
            font-family: 'Pacifico', cursive;
            color: #3b6b45;
            font-size: 36px;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .gallery-grid img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
            transition: 0.4s ease;
        }
        .gallery-grid img:hover { transform: scale(1.05); }

        /* TESTIMONIALS */
        .testimonials {
            background-color: #e7dbc3;
            text-align: center;
            padding: 60px 20px;
        }
        .testimonials h2 {
            font-family: 'Pacifico', cursive;
            color: #3b6b45;
            font-size: 36px;
        }
        .review-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 30px;
            margin-top: 30px;
        }
        .review {
            background-color: white;
            border-radius: 15px;
            padding: 25px;
            width: 300px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .review p { color: #4b3d2a; font-size: 15px; line-height: 1.6; }
        .review h4 { color: #3b6b45; margin-top: 10px; }

        /* PROMO */
        .promo {
            background: #3b6b45;
            color: white;
            text-align: center;
            padding: 40px 20px;
        }
        .promo h2 {
            font-family: 'Pacifico', cursive;
            font-size: 34px;
            margin: 0 0 10px 0;
        }
        .promo p { font-size: 18px; margin: 0; }

        /* CONTACT */
        .contact {
            background-color: #e7dbc3;
            text-align: center;
            padding: 60px 20px;
        }
        .contact h2 {
            font-family: 'Pacifico', cursive;
            color: #3b6b45;
            font-size: 36px;
        }
        .contact p { color: #4b3d2a; font-size: 15px; }
        iframe { border-radius: 12px; margin-top: 20px; }

        /* FOOTER */
        footer {
            background-color: #3b6b45;
            color: white;
            text-align: center;
            padding: 25px 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar" id="navbar">
    <div class="navbar-left">
        <img src="bit.png" alt="Bitsy Logo">
        <h1>Bitsy Bakesy</h1>
    </div>
    <div class="nav-links">
        <a href="#home">Home</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="menu.php">Menu</a>
        <?php if (isset($_SESSION['user_name'])): ?>
            <div class="profile" id="profileDropdown">
                <div class="profile-btn">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png">
                    <span><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
                <div class="dropdown" id="dropdownMenu">
                    <a href="my_orders.php">My Orders</a>
                    <a href="profile.php">Profile</a>
                    <form method="POST" action="logout.php">
                        <button type="submit">Logout</button>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<!-- BANNER -->
<div class="banner" id="home">
    <video autoplay muted loop playsinline>
        <source src="images/bitsy-banner.mp4" type="video/mp4">
    </video>
    <div class="banner-text">
        <h2>Freshly Baked Happiness</h2>
        <p>Made with love, right from our oven 💕</p>
    </div>
</div>

<div class="ribbon">Delicious Treats, Sweet Moments</div>

<!-- ABOUT -->
<div class="about" id="about">
    <img src="images/about.webp" alt="Bitsy Bakesy Bakery">
    <div class="about-text">
        <h2>About Bitsy Bakesy</h2>
        <p>Bitsy Bakesy began as a cozy home bakery in Kemaman, driven by a love for baking that brings joy to every home. From fluffy cupcakes to beautifully designed custom cakes, we create treats that not only taste amazing but also warm the heart. Each creation is baked fresh daily with premium ingredients and lots of love 💕</p>
    </div>
</div>

<!-- PRODUCTS -->
<div class="products" id="menu">
    <h2>Our Favourites</h2>
    <div class="product-container">
        <div class="product"><img src="images/cakes.jpg"><h3>Cakes</h3><p>Soft and fluffy with creamy frosting – joy in every bite!</p></div>
        <div class="product"><img src="images/pastries.jpg"><h3>Pastries</h3><p>Golden, sweet, and perfectly glazed to brighten your day.</p></div>
        <div class="product"><img src="images/desserts.jpg"><h3>Desserts</h3><p>Celebrate your moments with our signature custom cakes.</p></div>
    </div>
</div>

<!-- GALLERY -->
<div class="gallery">
    <h2>Sweet Creations</h2>
    <div class="gallery-grid">
        <img src="images/gallery1.jpg">
        <img src="images/gallery2.jpg">
        <img src="images/gallery3.jpg">
        <img src="images/gallery4.jpg">
        <img src="images/gallery5.jpg">
        <img src="images/gallery6.jpg">
    </div>
</div>

<!-- TESTIMONIALS -->
<div class="testimonials">
    <h2>Customer Love</h2>
    <div class="review-container">
        <div class="review"><p>“The cupcakes were heavenly! Soft, moist, and not too sweet. My kids loved them.”</p><h4>— Aina, Shah Alam ⭐⭐⭐⭐⭐</h4></div>
        <div class="review"><p>“Perfectly baked and beautifully packed. You can taste the love in every bite.”</p><h4>— Farah, Kemaman ⭐⭐⭐⭐⭐</h4></div>
        <div class="review"><p>“Best home-baked cake ever! Ordered for my birthday and it was stunning.”</p><h4>— Amalina, Dungun ⭐⭐⭐⭐⭐</h4></div>
    </div>
</div>

<!-- PROMO -->
<div class="promo">
    <h2>🎄 November Specials 🎁</h2>
    <p>Get special november menu now!</p>
</div>

<!-- CONTACT -->
<div class="contact" id="contact">
    <h2>Find Us</h2>
    <p>Lot 7593/1, Jalan Mak Chili, Kg Jabur, Kemaman</p>
    <p>📞 WhatsApp: 011-1234 5678 | 📸 IG & TikTok: @BitsyBakesy</p>
    <iframe src="https://www.google.com/maps?q=Kemaman&output=embed" width="80%" height="300"></iframe>
</div>

<footer>
    <p>&copy; 2025 Bitsy Bakesy Bakery. All Rights Reserved.</p>
    <p>Follow us on Instagram & TikTok @BitsyBakesy</p>
</footer>

<script>
    const navbar = document.getElementById("navbar");
    const profile = document.getElementById("profileDropdown");
    const dropdown = document.getElementById("dropdownMenu");

    window.addEventListener("scroll", () => {
        navbar.classList.toggle("scrolled", window.scrollY > 50);
    });

    if (profile) {
        profile.addEventListener("click", (e) => {
            dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
            e.stopPropagation();
        });
        document.addEventListener("click", (e) => {
            if (!profile.contains(e.target)) dropdown.style.display = "none";
        });
    }
</script>

</body>
</html>
