<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_role = strtolower(trim($_SESSION['role']));

include "db_connect.php";

/* ===============================
   FETCH PRODUCTS FROM DATABASE
================================= */
$products = [];
$result = $conn->query("SELECT id, name, price, category FROM products ORDER BY category, name");

if(!$result){
    die("SQL Error (Products): " . $conn->error);
}

while($row = $result->fetch_assoc()){
    $row['category'] = trim($row['category']);
    $products[] = $row;
}

/* ===============================
   CHECKOUT: SAVE TO SALESREPORT
================================= */
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'checkout'){
    
    $data = json_decode(file_get_contents('php://input'), true);

    if(!isset($data['cart']) || !is_array($data['cart'])){
        echo json_encode(['status' => 'error', 'message' => 'Invalid cart']);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO salereport (dates, item, quantity, price, total, username, category) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");

    if(!$stmt){
        echo json_encode(['status'=>'error','message'=>'Prepare failed: '.$conn->error]);
        exit();
    }

    $today = date("Y-m-d");

    foreach($data['cart'] as $c){
        $item = $c['name'];
        $qty  = (int)$c['qty'];
        $price = (float)$c['price'];
        $total = $qty * $price;
        $cat = $c['category'];

        $stmt->bind_param("ssiddss", $today, $item, $qty, $price, $total, $username, $cat);
        $stmt->execute();
    }

    echo json_encode(['status' => 'success']);
    exit();
}

/* ===============================
   CATEGORY FILTERS
================================= */
$categories = ["Coffee", "Non-Coffee", "Food"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Coffee POS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

:root{
    --brown:#4d3b27;
    --accent:#d3a676;
    --accent-dark:#b68857;
    --bg:#f3e6d7;
    --text:#3a2c1a;
}

*{box-sizing:border-box}
body {
    margin: 0;
    font-family: 'Poppins', sans-serif;
    background: var(--bg);
    color: var(--text);
}

/* NAVBAR */
.navbar {
    background: #4d3b27;
    padding: 15px 20px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between; /* keeps menu and logout on sides */
    position: sticky;
    top: 0;
    z-index: 10;
    position: relative;
}

.navbar-title {
    position: absolute;
    left: 50%;
    transform: translateX(-50%);
    margin: 0;
    font-size: 20px;
}

.navbar h1 { margin:0; font-size:20px; }
.menu-btn { font-size: 24px; background:none; border:none; color:white; cursor:pointer; }
.logout { padding: 8px 14px; background: var(--accent); color:white; text-decoration:none; border-radius:6px; }

/* SIDEBAR */
.sidebar {
    position: fixed;
    left: -260px;
    top: 0;
    width: 260px;
    height: 100%;
    background: var(--brown);
    padding-top: 60px;
    transition: 0.28s;
    z-index: 999;
}
.sidebar.open { left: 0; }
.sidebar .close-btn { position:absolute; top:12px; right:16px; color:white; font-size:22px; cursor:pointer; }
.sidebar a { display:block; padding:14px 20px; color:white; text-decoration:none; font-size:16px; border-bottom:1px solid rgba(255,255,255,0.08);}
.sidebar a:hover{ background: rgba(255,255,255,0.04); }

/* LAYOUT */
.container {
    display:flex;
    gap:20px;
    padding:20px;
    align-items:flex-start;
}

/* LEFT: categories vertical menu */
.left-sidebar {
    flex: 0 0 140px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    font-weight: 600;
    color: var(--brown);
}
.left-sidebar .category-title {
    cursor: pointer;
    padding: 8px 12px;
    border-radius: 6px;
    transition: 0.2s;
    background: #f3e6d7;
}
.left-sidebar .category-title:hover {
    background: var(--accent);
    color: #fff;
}

/* RIGHT: products area */
.right { flex:1; }

/* Search & filters */
.top-controls { display:flex; gap:12px; margin-bottom:14px; align-items:center; }
.search { flex:1; display:flex; align-items:center; gap:8px; }
.search input { width:100%; padding:10px 12px; border-radius:8px; border:1px solid rgba(0,0,0,0.08); background:white; }

/* Product grid */
#product-list { display:flex; flex-wrap:wrap; gap:18px; }
.product-card {
    width:200px; background:white; border-radius:8px; padding:12px; box-shadow:0 4px 12px rgba(0,0,0,0.06); text-align:center;
}
.product-card h3 { margin:6px 0 4px; font-size:18px }
.product-card p { margin:0; color: #666; font-size:14px; }
.product-card button {
    margin-top:10px;
    padding:8px 12px;
    background:var(--accent);
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-weight:600;
}
.product-card button:hover{ background:var(--accent-dark); color:white }

/* RIGHT: cart */
.cart {
    width:320px;
    background:white;
    padding:18px;
    border-radius:10px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    height: calc(100vh - 120px);
    overflow:auto;
    position:sticky;
    top:20px;
}
.cart h2{ margin-top:0 }
.cart-item {
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:8px;
    margin-bottom:10px;
    padding-bottom:8px;
    border-bottom:1px dashed rgba(0,0,0,0.05);
}
.qty-controls { display:flex; align-items:center; gap:6px; }
.qty-controls button { width:26px; height:26px; border-radius:4px; border:none; background:var(--accent); cursor:pointer; font-weight:600; }
.qty-controls span { min-width:24px; text-align:center; display:inline-block; }
.cart .total { margin-top:12px; font-size:18px; font-weight:700; }
.checkout-btn { margin-top:12px; width:100%; padding:10px; border:none; background: #5cb85c; color:white; border-radius:8px; cursor:pointer; font-weight:700; }

/* Receipt modal */
#receipt-modal { display:none; position:fixed; inset:0; background: rgba(0,0,0,0.45); justify-content:center; align-items:center; z-index:1200; }
#receipt-content { width:90%; max-width:420px; background:white; padding:18px; border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,0.25); }
#receipt-content h3{ margin-top:0 }
#receipt-content .receipt-body { max-height:360px; overflow:auto; }

@media (max-width: 900px){
    .container{ flex-direction:column; padding:12px }
    .cart { position:relative; width:100%; height:auto; }
    .left-sidebar { flex-direction:row; gap:12px; margin-bottom:12px; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div id="sideBar" class="sidebar">
    <span class="close-btn" onclick="toggleSidebar()">✖</span>
    
    <?php if($user_role === 'admin'): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="inventory.php">Inventory List</a>
        <a href="users.php">Users List</a>
        <a href="sales_report.php">Transaction History / Sales Report</a>

    <?php elseif($user_role === 'staff'): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="inventory.php">Inventory</a>
    <?php endif; ?>
</div>


<!-- navbar -->
<div class="navbar">
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <h1 class="navbar-title">Main POS</h1>
    <a class="logout" href="logout.php">Logout</a>
</div>


<!-- main -->
<div class="container">
    <!-- LEFT: categories -->
    <div class="left-sidebar"><div class="category-title" onclick="filter('all')">All</div>
        <?php foreach($categories as $c): ?>
            <div class="category-title" onclick="filter('<?php echo $c; ?>')"><?php echo $c; ?></div>
        <?php endforeach; ?>
        
    </div>

    <!-- RIGHT: products + search -->
    <div class="right">
        <div class="top-controls">
            <div class="search">
                <input id="searchInput" type="text" placeholder="🔎 Search product..." oninput="onSearch()" />
            </div>
        </div>
        <div id="product-list"></div>
    </div>

    <!-- cart -->
    <div class="cart">
        <h2>Cart</h2>
        <div id="cart-items"></div>
        <div class="total">Total: ₱<span id="total">0</span></div>
        <button class="checkout-btn" onclick="checkout()">Checkout</button>
    </div>
</div>

<!-- receipt modal -->
<div id="receipt-modal">
    <div id="receipt-content">
        <h3>Receipt</h3>
        <div id="receipt-meta" style="font-size:13px;color:#666;margin-bottom:8px;"></div>
        <div class="receipt-body" id="receipt-body"></div>
        <div style="display:flex;gap:8px;margin-top:12px;">
            <button onclick="printEntirePage()" style="flex:1;padding:10px;border:none;background:var(--accent);color:var(--text);border-radius:6px;font-weight:600;">Print</button>
            <button onclick="closeReceipt()" style="flex:1;padding:10px;border:none;background:#d9534f;color:white;border-radius:6px;font-weight:600;">Cancel</button>
        </div>
    </div>
</div>

<script>
let products = <?php echo json_encode($products); ?>;
let cart = [];
let currentCategory = 'all';
let searchQuery = '';

function toggleSidebar(){ 
    document.getElementById('sideBar').classList.toggle('open'); 
}


function renderProducts(){
    const list = document.getElementById('product-list');
    list.innerHTML = '';
    const cats = (currentCategory === 'all') ? ['Coffee','Non-Coffee','Food'] : [currentCategory];
    const q = searchQuery.trim().toLowerCase();

    cats.forEach(cat=>{
        const catProducts = products.filter(p => p.category === cat && (p.name.toLowerCase().includes(q) || p.category.toLowerCase().includes(q)));
        catProducts.forEach(p=>{
            const card = document.createElement('div');
            card.className='product-card';
            card.innerHTML = `
                <h3>${escapeHtml(p.name)}</h3>
                <p>₱${p.price}</p>
                <button onclick="addToCart(${p.id})">Add</button>
            `;
            list.appendChild(card);
        });
    });
}

function onSearch(){ searchQuery = document.getElementById('searchInput').value; renderProducts(); }
function filter(cat){ currentCategory = cat; renderProducts(); }

function addToCart(id){
    const p = products.find(x=>x.id==id);
    if(!p) return;
    const existing = cart.find(x=>x.id==id);
    if(existing){ existing.qty++; } else { cart.push({ id:p.id,name:p.name,price:parseFloat(p.price),category:p.category,qty:1 }); }
    renderCart();
}

function changeQty(id,delta){
    const it = cart.find(x=>x.id==id);
    if(!it) return;
    it.qty += delta;
    if(it.qty<=0) cart = cart.filter(x=>x.id!=id);
    renderCart();
}

function removeFromCart(id){ cart = cart.filter(x=>x.id!=id); renderCart(); }

function renderCart(){
    const box=document.getElementById('cart-items');
    box.innerHTML='';
    let total=0;
    cart.forEach(item=>{
        const lineTotal=item.price*item.qty;
        total+=lineTotal;
        const div=document.createElement('div');
        div.className='cart-item';
        div.innerHTML=`
            <div style="flex:1">
                <div style="font-weight:600">${escapeHtml(item.name)}</div>
                <div style="font-size:13px;color:#666">₱${item.price} each</div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px">
                <div class="qty-controls">
                    <button onclick="changeQty(${item.id}, -1)">−</button>
                    <span>${item.qty}</span>
                    <button onclick="changeQty(${item.id}, 1)">+</button>
                </div>
                <div style="font-weight:600">₱${(lineTotal).toFixed(2)}</div>
                <div><button style="margin-top:6px" onclick="removeFromCart(${item.id})">Remove</button></div>
            </div>
        `;
        box.appendChild(div);
    });
    document.getElementById('total').textContent=total.toFixed(2);
}

function checkout(){
    if(cart.length===0){ alert("Cart is empty!"); return; }
    fetch('pos.php?action=checkout',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({cart:cart})})
    .then(r=>r.json())
    .then(data=>{
        if(data.status==='success'){
            const receiptBody=document.getElementById('receipt-body');
            const meta=document.getElementById('receipt-meta');
            const dt=new Date();
            meta.innerHTML=`Date: ${dt.toLocaleString()} &nbsp; | &nbsp; Cashier: <?php echo htmlspecialchars($username); ?>`;
            let html='<div style="margin-bottom:8px;"><hr>';
            cart.forEach(it=>{ html+=`<div style="display:flex;justify-content:space-between;padding:6px 0"><div>${escapeHtml(it.name)} x ${it.qty}</div><div>₱${(it.price*it.qty).toFixed(2)}</div></div>`; });
            html+='<hr>';
            const total=cart.reduce((s,i)=>s+(i.price*i.qty),0);
            html+=`<div style="font-weight:700;margin-top:8px">Total: ₱${total.toFixed(2)}</div></div>`;
            receiptBody.innerHTML=html;
            document.getElementById('receipt-modal').style.display='flex';
            cart=[]; renderCart();
        } else { alert('Checkout failed: '+(data.message||'Unknown')); }
    })
    .catch(err=>{ alert('Network error: '+err); });
}

function printEntirePage(){ window.print(); }
function closeReceipt(){ document.getElementById('receipt-modal').style.display='none'; }
function escapeHtml(str){ if(!str) return ''; return String(str).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/'/g,'&#39;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

renderProducts();
renderCart();
</script>
</body>
</html>
