<?php
session_start();

// ------------- PROTECT PAGE (REQUIRE LOGIN) -------------
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get user role
$user_role = strtolower(trim($_SESSION['role'])); // 'admin' or 'staff'
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Bean Street Cafe Dashboard</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: #f3e6d7;
    color: #3a2c1a;
}

/* NAVBAR */
.navbar {
    background: #4d3b27;
    padding: 15px 20px;
    color: white;
    display: flex;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 10;
}

.navbar h1 {
    margin: 0 0 0 10px;
}

.navbar-right {
    margin-left: auto;
    display: flex;
    align-items: center;
    gap: 15px;
}

.logout-btn {
    padding: 8px 16px;
    background: #d3a676;
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    transition: 0.3s;
}

.logout-btn:hover {
    background: #b68857;
}

/* BURGER MENU BUTTON */
.menu-btn {
    background: none; /* No background */
    border: none;     /* No border */
    cursor: pointer;  
    font-size: 28px;  /* Three lines size */
    color: white;
    padding: 0;
}

/* SIDEBAR */
.sidebar {
    width: 0;
    height: 100%;
    background: #4d3b27;
    position: fixed;
    left: 0;
    top: 0;
    overflow-x: hidden;
    transition: 0.3s;
    padding-top: 60px;
    z-index: 20;
}

.sidebar a {
    display: block;
    padding: 15px 25px;
    color: white;
    font-size: 18px;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.2);
}

.sidebar a:hover {
    background: #6b4e30;
}

.close-btn {
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 24px;
    color: white;
    cursor: pointer;
}

.close-btn:hover {
    color: #ffce8a;
}

/* HERO */
.hero {
    position: relative;
}

.hero img {
    width: 100%;
    height: 400px;
    object-fit: cover;
    filter: brightness(65%);
}

.hero-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    color: white;
}

.hero-text h1 {
    font-size: 50px;
    margin-bottom: 10px;
}

.hero-btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px;
    background: #d3a676;
    color: #3a2c1a;
    font-weight: 600;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.3s;
}

.hero-btn:hover {
    background: #b68857;
    color: white;
}

/* MENU */
.menu {
    padding: 50px 40px;
    text-align: center;
}

.products {
    display: flex;
    justify-content: center;
    gap: 30px;
    margin-top: 35px;
    flex-wrap: wrap;
}

.product-card {
    display: block;
    background: white;
    width: 260px;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
    cursor: pointer;
    transition: 0.3s;
    text-decoration: none;
    color: inherit;
}

.product-card:hover {
    transform: scale(1.05);
}

.image-container {
    position: relative;
}

.image-container img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 8px;
}

/* Button on product card image */
.card-btn {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(77, 59, 39, 0.85);
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 6px;
    cursor: pointer;
    opacity: 0;
    transition: 0.3s;
}

.image-container:hover .card-btn {
    opacity: 1;
}

/* FOOTER */
footer {
    background: #3a2c1a;
    color: #d3a676;
    padding: 20px;
    text-align: center;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .navbar ul {
        display: none;
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div id="sideBar" class="sidebar">
    <span class="close-btn" onclick="toggleSidebar()">✖</span>
    
    <?php if($user_role === 'admin'): ?>
        <a href="pos.php">Main POS</a>
        <a href="inventory.php">Inventory List</a>
        <a href="users.php">Users List</a>
        <a href="sales_report.php">Transaction History / Sales Report</a>
    <?php elseif($user_role === 'staff'): ?>
        <a href="pos.php">Main POS</a>
        <a href="inventory.php">Inventory</a>
    <?php endif; ?>
</div>

<!-- NAVBAR -->
<div class="navbar">
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <h1>☕Bean Street Cafe Dashboard</h1>

    <div class="navbar-right">
        <span>Welcome, <b><?php echo $_SESSION['username']; ?></b></span>
        <a class="logout-btn" href="logout.php">Logout</a>
    </div>
</div>

<!-- HERO -->
<div class="hero">
    <img src="bean.jpg" alt="Coffee Hero" />
    <div class="hero-text">
        <h1>Dashboard</h1>
        <p>Manage Bean Street Cafe Operations</p>
    </div>
</div>

<!-- INVENTORY CARDS -->
<div class="menu">
    <h2>Menu</h2>
    <div class="products">
        <a href="invent_coffee.php" class="product-card">
            <div class="image-container">
                <img src="https://images.unsplash.com/photo-1509042239860-f550ce710b93" alt="Coffee">
                <button class="card-btn">View Coffee</button>
            </div>
            <h3>For Coffee</h3>
            <p>Strong and bold flavor.</p>
        </a>

        <a href="invent_food.php" class="product-card">
            <div class="image-container">
                <img src="food.jpg" alt="Foods">
                <button class="card-btn">View Foods</button>
            </div>
            <h3>For Foods</h3>
            <p>Best pastries & snacks.</p>
        </a>

        <a href="invent_noncoffee.php" class="product-card">
            <div class="image-container">
                <img src="milktea.jpg" alt="Milktea">
                <button class="card-btn">View Milktea</button>
            </div>
            <h3>For Milktea</h3>
            <p>Delicious milktea blends.</p>
        </a>
    </div>
</div>

<!-- FOOTER -->
<footer>
    © 2025 Bean Street Cafe — Dashboard
</footer>

<script>
function toggleSidebar() {
    const bar = document.getElementById("sideBar");
    bar.style.width = bar.style.width === "250px" ? "0" : "250px";
}
</script>

</body>
</html>
