<?php
// Non-Coffee Milk-Based Drinks / Non-Coffee Products (24 items)
$products = [
    ['name'=>'Chocolate Drink',       'price'=>120, 'desc'=>'Rich chocolate blended with milk.'],
    ['name'=>'Vanilla Cream',         'price'=>120, 'desc'=>'Smooth vanilla-flavored creamy drink.'],
    ['name'=>'Strawberry Cream',      'price'=>130, 'desc'=>'Strawberry blended with milk for a sweet treat.'],
    ['name'=>'Taro Latte',            'price'=>135, 'desc'=>'Sweet taro root drink with creamy milk.'],
    ['name'=>'Matcha Latte',          'price'=>140, 'desc'=>'Green tea powder blended with milk.'],
    ['name'=>'Caramel Cream',         'price'=>145, 'desc'=>'Caramel flavored creamy drink.'],
    ['name'=>'Cookies & Cream',       'price'=>150, 'desc'=>'Crushed cookies blended with milk.'],
    ['name'=>'Hazelnut Cream',        'price'=>150, 'desc'=>'Nutty and sweet creamy drink.'],
    ['name'=>'Coffee-Free Mocha',     'price'=>140, 'desc'=>'Chocolatey creamy drink without coffee.'],
    ['name'=>'Honey Milk',            'price'=>120, 'desc'=>'Sweet honey blended with milk.'],
    ['name'=>'Almond Cream',          'price'=>145, 'desc'=>'Almond flavored creamy drink.'],
    ['name'=>'Choco Banana',          'price'=>135, 'desc'=>'Banana blended with chocolate and milk.'],
    ['name'=>'Mango Cream',           'price'=>130, 'desc'=>'Mango blended with creamy milk.'],
    ['name'=>'Coconut Cream',         'price'=>130, 'desc'=>'Coconut flavored milk drink.'],
    ['name'=>'Red Velvet Drink',      'price'=>140, 'desc'=>'Sweet red velvet flavored creamy drink.'],
    ['name'=>'Peach Cream',           'price'=>130, 'desc'=>'Peach flavored creamy milk drink.'],
    ['name'=>'Blueberry Cream',       'price'=>135, 'desc'=>'Blueberry blended with milk.'],
    ['name'=>'Oreo Cream',            'price'=>150, 'desc'=>'Cookies blended with milk and cream.'],
    ['name'=>'Caramel Apple',         'price'=>140, 'desc'=>'Apple blended with caramel and milk.'],
    ['name'=>'Banana Milk',           'price'=>125, 'desc'=>'Banana mixed with milk.'],
    ['name'=>'Strawberry Banana',     'price'=>135, 'desc'=>'Strawberry and banana with creamy milk.'],
    ['name'=>'Mango Tango',           'price'=>140, 'desc'=>'Mango drink with creamy texture.'],
    ['name'=>'Cocoa Delight',         'price'=>145, 'desc'=>'Chocolatey creamy milk drink.'],
    ['name'=>'Almond Frappe',         'price'=>150, 'desc'=>'Almond flavored blended creamy drink.'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Coffee Shop Dessert Menu</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
body{margin:0;font-family:'Poppins',sans-serif;background:#f3e6d7;color:#3a2c1a;}
.navbar{background:#4d3b27;color:white;padding:15px;text-align:center;font-size:22px;font-weight:600;}
.container{padding:20px;}
.back-btn{display:inline-block;margin-bottom:20px;padding:8px 16px;background:#4d3b27;color:white;border-radius:6px;text-decoration:none;font-weight:600;}
.back-btn:hover{background:#d3a676;color:white;}
.products{display:flex;flex-wrap:wrap;gap:20px;justify-content:center;}
.product-card{width:200px;background:white;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);padding:12px;text-align:left;cursor:pointer;transition:0.2s;}
.product-card:hover{transform:translateY(-4px);box-shadow:0 8px 20px rgba(0,0,0,0.15);}
.product-card h3{margin:6px 0 4px;font-size:18px;}
.product-card p{margin:0;color:#666;font-size:14px;}
.product-desc{margin-top:6px;font-size:13px;color:#444;}
</style>
</head>
<body>

<div class="navbar">Coffee Shop Dessert Menu</div>

<div class="container">
    <a class="back-btn" href="dashboard.php">← Back to Dashboard</a>

    <div class="products" id="product-list">
        <?php foreach($products as $p): ?>
        <div class="product-card" onclick="alert('Product: <?php echo $p['name']; ?>\nPrice: ₱<?php echo $p['price']; ?>\nDescription: <?php echo $p['desc']; ?>')">
            <h3><?php echo $p['name']; ?></h3>
            <p>₱<?php echo $p['price']; ?></p>
            <div class="product-desc"><?php echo $p['desc']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>