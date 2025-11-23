<?php
// Coffee Shop Dessert Products (24 items) with descriptions
$products = [
    ['name'=>'Chocolate Cake',       'price'=>180, 'desc'=>'Rich chocolate sponge with chocolate frosting.'],
    ['name'=>'Cheesecake',           'price'=>200, 'desc'=>'Creamy cheesecake with a graham cracker crust.'],
    ['name'=>'Tiramisu',             'price'=>220, 'desc'=>'Classic Italian dessert with coffee-soaked layers.'],
    ['name'=>'Brownie',              'price'=>150, 'desc'=>'Fudgy chocolate brownie with walnuts.'],
    ['name'=>'Macaron',              'price'=>90,  'desc'=>'Delicate French almond cookie sandwich with filling.'],
    ['name'=>'Fruit Tart',           'price'=>180, 'desc'=>'Buttery tart filled with custard and fresh fruits.'],
    ['name'=>'Cupcake',              'price'=>120, 'desc'=>'Soft cupcake topped with creamy frosting.'],
    ['name'=>'Ice Cream Sundae',     'price'=>160, 'desc'=>'Scoops of ice cream with toppings and syrup.'],
    ['name'=>'Crepe',                'price'=>150, 'desc'=>'Thin crepe filled with cream and chocolate sauce.'],
    ['name'=>'Donut',                'price'=>80,  'desc'=>'Soft fried donut with sugar or glaze.'],
    ['name'=>'Panna Cotta',          'price'=>200, 'desc'=>'Silky Italian dessert with berry sauce.'],
    ['name'=>'Chocolate Mousse',     'price'=>180, 'desc'=>'Smooth chocolate mousse topped with whipped cream.'],
    ['name'=>'Apple Pie',            'price'=>170, 'desc'=>'Classic pie with spiced apple filling.'],
    ['name'=>'Banana Split',         'price'=>160, 'desc'=>'Banana with ice cream, chocolate, and whipped cream.'],
    ['name'=>'Carrot Cake',          'price'=>180, 'desc'=>'Moist cake with carrot, spices, and cream cheese frosting.'],
    ['name'=>'Cheese Danish',        'price'=>140, 'desc'=>'Flaky pastry filled with sweetened cream cheese.'],
    ['name'=>'Chocolate Chip Cookie','price'=>90,  'desc'=>'Soft baked cookie with chocolate chips.'],
    ['name'=>'Lemon Tart',           'price'=>180, 'desc'=>'Tangy lemon custard in a buttery tart shell.'],
    ['name'=>'Eclair',               'price'=>160, 'desc'=>'Choux pastry filled with cream and chocolate glaze.'],
    ['name'=>'Muffin',               'price'=>120, 'desc'=>'Soft baked muffin with chocolate or fruits.'],
    ['name'=>'Trifle',               'price'=>200, 'desc'=>'Layered dessert with cake, custard, fruits, and cream.'],
    ['name'=>'Baklava',              'price'=>190, 'desc'=>'Sweet pastry with layers of nuts and honey syrup.'],
    ['name'=>'Chewy Brownie',        'price'=>160, 'desc'=>'Soft and chewy chocolate brownie.'],
    ['name'=>'Matcha Cake',          'price'=>200, 'desc'=>'Green tea flavored sponge cake with cream.'],
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
