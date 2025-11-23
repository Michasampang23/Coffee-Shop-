<?php
// Coffee Products (24 items) with descriptions
$products = [
    ['name'=>'Espresso',         'price'=>80,  'category'=>'Coffee', 'desc'=>'Strong, bold shot of coffee.'],
    ['name'=>'Cappuccino',       'price'=>120, 'category'=>'Coffee', 'desc'=>'Espresso with steamed milk foam.'],
    ['name'=>'Latte',            'price'=>130, 'category'=>'Coffee', 'desc'=>'Espresso with steamed milk and light foam.'],
    ['name'=>'Flat White',       'price'=>125, 'category'=>'Coffee', 'desc'=>'Smooth espresso with microfoam milk.'],
    ['name'=>'Americano',        'price'=>90,  'category'=>'Coffee', 'desc'=>'Espresso diluted with hot water.'],
    ['name'=>'Macchiato',        'price'=>110, 'category'=>'Coffee', 'desc'=>'Espresso topped with a small amount of foam.'],
    ['name'=>'Mocha',            'price'=>135, 'category'=>'Coffee', 'desc'=>'Espresso with chocolate and steamed milk.'],
    ['name'=>'Iced Coffee',      'price'=>95,  'category'=>'Coffee', 'desc'=>'Chilled coffee served over ice.'],
    ['name'=>'Affogato',         'price'=>140, 'category'=>'Coffee', 'desc'=>'Espresso poured over ice cream.'],
    ['name'=>'Frappuccino',      'price'=>150, 'category'=>'Coffee', 'desc'=>'Blended iced coffee drink with flavors.'],
    ['name'=>'Caramel Latte',    'price'=>145, 'category'=>'Coffee', 'desc'=>'Latte with caramel syrup.'],
    ['name'=>'Vanilla Latte',    'price'=>145, 'category'=>'Coffee', 'desc'=>'Latte with vanilla flavor.'],
    ['name'=>'Hazelnut Coffee',  'price'=>150, 'category'=>'Coffee', 'desc'=>'Coffee with hazelnut syrup.'],
    ['name'=>'Irish Coffee',     'price'=>160, 'category'=>'Coffee', 'desc'=>'Coffee with whiskey and cream.'],
    ['name'=>'Chocolate Mocha',  'price'=>155, 'category'=>'Coffee', 'desc'=>'Mocha with chocolate syrup.'],
    ['name'=>'Ristretto',        'price'=>85,  'category'=>'Coffee', 'desc'=>'Short, concentrated espresso shot.'],
    ['name'=>'Doppio',           'price'=>100, 'category'=>'Coffee', 'desc'=>'Double shot of espresso.'],
    ['name'=>'Cortado',          'price'=>115, 'category'=>'Coffee', 'desc'=>'Espresso cut with warm milk.'],
    ['name'=>'Long Black',       'price'=>90,  'category'=>'Coffee', 'desc'=>'Espresso poured over hot water.'],
    ['name'=>'Red Eye',          'price'=>95,  'category'=>'Coffee', 'desc'=>'Brewed coffee with a shot of espresso.'],
    ['name'=>'Black Eye',        'price'=>100, 'category'=>'Coffee', 'desc'=>'Strong coffee with double espresso.'],
    ['name'=>'Lungo',            'price'=>105, 'category'=>'Coffee', 'desc'=>'Longer extraction espresso shot.'],
    ['name'=>'Breve',            'price'=>120, 'category'=>'Coffee', 'desc'=>'Espresso with steamed half-and-half milk.'],
    ['name'=>'Vienna',           'price'=>125, 'category'=>'Coffee', 'desc'=>'Espresso topped with whipped cream.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Coffee Catalog</title>
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

<div class="navbar">Coffee Shop Catalog</div>

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
