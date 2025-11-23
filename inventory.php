<?php
session_start();
include "db_connect.php";

// ------------------- CATEGORIES -------------------
$categories = ["Coffee", "Food", "Non-Coffee", "Others"];

// ------------------- UTIL -------------------
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$errors = [];
$success = "";

// ------------------- GET USER ROLE -------------------
$user_role = strtolower(trim($_SESSION['role'])); // admin or staff

// ------------------- HANDLE POST ACTIONS -------------------
if($_SERVER['REQUEST_METHOD']==='POST'){
    $action = $_POST['action'] ?? '';
    $category = trim($_POST['category'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = trim($_POST['price'] ?? '');
    $stock = intval($_POST['stock'] ?? 0);
    $id = intval($_POST['id'] ?? 0);

    if($name==='') $errors[]="Name is required.";
    if($price==='' || !is_numeric($price)) $errors[]="Price must be a number.";

    if(empty($errors)){
        if($action==='add'){
            $stmt = $conn->prepare("INSERT INTO products (category,name,description,price,stock,created_at,updated_at) VALUES (?,?,?,?,?,NOW(),NOW())");
            $stmt->bind_param("sssdi",$category,$name,$description,$price,$stock);
            if($stmt->execute()) $success="Product added successfully.";
            else $errors[]="Database error: ".$stmt->error;
            $stmt->close();
        }elseif($action==='edit'){
            $stmt = $conn->prepare("UPDATE products SET category=?, name=?, description=?, price=?, stock=?, updated_at=NOW() WHERE id=?");
            $stmt->bind_param("sssdis", $category, $name, $description, $price, $stock, $id);
            if($stmt->execute()) $success="Product updated successfully.";
            else $errors[]="Database error: ".$stmt->error;
            $stmt->close();
        }elseif($action==='delete'){
            $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
            $stmt->bind_param("i",$id);
            if($stmt->execute()) $success="Product deleted successfully.";
            else $errors[]="Database error: ".$stmt->error;
            $stmt->close();
        }
    }
}

// ------------------- FETCH PRODUCTS -------------------
$filter_sql = "";
if(isset($_GET['filter'])){
    if($_GET['filter']==='low') $filter_sql = "WHERE stock BETWEEN 1 AND 5";
    elseif($_GET['filter']==='out') $filter_sql = "WHERE stock = 0";
}
$search_sql="";
if(isset($_GET['search']) && $_GET['search']!==''){
    $search = $conn->real_escape_string($_GET['search']);
    $search_sql = ($filter_sql==='' ? "WHERE" : "AND")." name LIKE '%$search%'";
}

$sql = "SELECT * FROM products $filter_sql $search_sql ORDER BY id DESC";
$products = $conn->query($sql);
if(!$products) { die("SQL Error: ".$conn->error); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Inventory</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
:root{
    --brown-dark:#4d3b27;
    --brown-mid:#6b4e30;
    --accent:#d3a676;
    --bg:#f3e6d7;
    --text:#3a2c1a;
}
*{box-sizing:border-box;margin:0;padding:0;font-family:'Poppins',sans-serif;}
body{background:var(--bg);color:var(--text);min-height:100vh;}
.navbar{background:var(--brown-dark);padding:12px 20px;color:white;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:10;position:relative;}
.navbar-title{position:absolute;left:50%;transform:translateX(-50%);font-size:20px;font-weight:600;}
.navbar-right{display:flex;align-items:center;gap:10px;}
.logout-btn{padding:8px 14px;background:var(--accent);color:#fff;text-decoration:none;border-radius:6px;transition:0.2s;}
.logout-btn:hover{background:#b68857;}
.menu-btn{background:none;border:none;cursor:pointer;font-size:24px;color:white;padding:0;}
.sidebar{width:260px;height:100%;background:var(--brown-dark);position:fixed;left:-260px;top:0;overflow-x:hidden;padding-top:70px;transition:0.3s;}
.sidebar.show{left:0;}
.sidebar a{display:block;padding:14px 24px;color:white;text-decoration:none;border-bottom:1px solid rgba(255,255,255,0.06);}
.sidebar a:hover{background:var(--brown-mid);}
.close-btn{position:absolute;top:12px;right:18px;font-size:20px;color:white;cursor:pointer;}
.main{margin-left:0;padding:22px 28px;transition:margin-left 0.25s;}
.header-row{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:14px;}
.header-row h2{color:var(--brown-dark);font-size:26px;margin:0;}
.controls{display:flex;gap:10px;align-items:center;flex-wrap:wrap;margin-bottom:20px;justify-content:flex-end;}
.controls input, .controls select{padding:8px 10px;border-radius:6px;border:1px solid rgba(0,0,0,0.1);}
.controls button{padding:8px 12px;border-radius:6px;border:none;font-weight:600;cursor:pointer;}
.controls .search-btn{background:linear-gradient(45deg,var(--accent),#f1d6a1);color:#000;}
.controls .add-btn{background:linear-gradient(45deg,#27ae60,#2ecc71);color:#fff;}
.products{display:flex;flex-wrap:wrap;gap:20px;}
.card{background:white;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.1);padding:15px;width:220px;cursor:pointer;transition:0.3s;}
.card:hover{transform:scale(1.05);}
.card h3{margin-bottom:4px;}
.card p{margin:2px 0;font-size:14px;color:#555;}
.badge{padding:4px 6px;border-radius:10px;font-weight:600;font-size:12px;display:inline-block;}
.in-stock{background:#27ae60;color:#fff;}
.low-stock{background:#f1c40f;color:#3a2c1a;}
.out-stock{background:#e74c3c;color:#fff;}
.modal-backdrop{display:none;position:fixed;inset:0;background: rgba(0,0,0,0.6);z-index:2000;justify-content:center;align-items:center;padding:20px;}
.modal{background:#fff;color:#222;border-radius:10px;max-width:500px;width:100%;box-shadow:0 12px 36px rgba(0,0,0,0.25);overflow:auto;}
.modal-head{background:var(--brown-dark);color:white;padding:12px 16px;border-top-left-radius:10px;border-top-right-radius:10px;display:flex;justify-content:space-between;align-items:center;}
.modal-body{padding:16px;}
.modal-footer{padding:12px 16px;display:flex;justify-content:flex-end;gap:10px;}
.form-field{margin-bottom:12px;}
.form-field label{display:block;margin-bottom:6px;font-weight:600;color:#4a3a2b;}
.form-field input, .form-field textarea, .form-field select{width:100%;padding:10px;border-radius:8px;border:1px solid rgba(0,0,0,0.06);}
.actions button{padding:6px 10px;border:none;border-radius:6px;color:white;font-weight:600;cursor:pointer;}
.actions .delete{background:#e74c3c;}
.actions .edit{background:#3498db;}
.msg{padding:10px 12px;border-radius:8px;margin-bottom:10px;}
.msg.error{background: rgba(231,76,60,0.08); color:#7b2b28; border:1px solid rgba(231,76,60,0.12);}
.msg.success{background: rgba(39,174,96,0.06); color:#1f6b3f; border:1px solid rgba(39,174,96,0.12);}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div id="sideBar" class="sidebar">
    <span class="close-btn" onclick="toggleSidebar()">✖</span>
    <?php if($user_role === 'admin'): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="pos.php">Main POS</a>
        <a href="users.php">Users List</a>
        <a href="sales_report.php">Transaction History / Sales Report</a>
    <?php elseif($user_role === 'staff'): ?>
        <a href="pos.php">Main POS</a>
        <a href="dashboard.php">Dashboard</a>
    <?php endif; ?>
</div>

<!-- NAVBAR -->
<div class="navbar">
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <div class="navbar-title">Inventory List</div>
    <div class="navbar-right">
        <a class="logout-btn" href="logout.php">Logout</a>
    </div>
</div>

<!-- CONTROLS -->
<div class="main">
    <div class="controls">
        <input type="text" id="searchInput" placeholder="Search product..." onkeyup="filterProducts()">
        <select id="stockFilter" onchange="filterProducts()">
            <option value="">All Stock</option>
            <option value="low">Low Stock</option>
            <option value="out">Out of Stock</option>
        </select>
        <select id="categoryFilter" onchange="filterProducts()">
            <option value="">All Categories</option>
            <?php foreach($categories as $c): ?>
            <option value="<?= h($c) ?>"><?= h($c) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="openAddModal()" class="add-btn">+ Add Product</button>
    </div>

    <div class="header-row">
        <h2>Inventory Products</h2>
    </div>

    <!-- Products Listing -->
    <div class="products" id="productsContainer">
        <?php if($products && $products->num_rows>0): ?>
            <?php while($row=$products->fetch_assoc()): ?>
            <div class="card" data-name="<?= h(strtolower($row['name'])) ?>" data-category="<?= h($row['category']) ?>" data-stock="<?= intval($row['stock']) ?>" 
                onclick='openEditModal(<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8") ?>)'>
                <h3><?= h($row['name']) ?></h3>
                <p>Category: <?= h($row['category']) ?></p>
                <p>Stock: <?= intval($row['stock']) ?> 
                    <?php $s=intval($row['stock']);
                    if($s==0): ?><span class="badge out-stock">Out</span>
                    <?php elseif($s<=5): ?><span class="badge low-stock">Low</span>
                    <?php else: ?><span class="badge in-stock">In</span><?php endif; ?>
                </p>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL -->
<div class="modal-backdrop" id="addEditBackdrop">
    <div class="modal">
        <div class="modal-head">
            <div id="addEditTitle">Add Product</div>
            <button onclick="closeAddEditModal()">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" id="addEditForm">
                <input type="hidden" name="action" id="aeAction" value="add">
                <input type="hidden" name="id" id="aeId">
                <div class="form-field">
                    <label>Category</label>
                    <select name="category" id="aeCategory">
                        <?php foreach($categories as $c): ?>
                        <option value="<?= h($c) ?>"><?= h($c) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-field">
                    <label>Name</label>
                    <input type="text" name="name" id="aeName" required>
                </div>
                <div class="form-field">
                    <label>Description</label>
                    <textarea name="description" id="aeDescription" rows="4"></textarea>
                </div>
                <div class="form-field">
                    <label>Price</label>
                    <input type="text" name="price" id="aePrice" required>
                </div>
                <div class="form-field">
                    <label>Stock</label>
                    <input type="number" name="stock" id="aeStock" min="0" value="0" required>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="closeAddEditModal()">Cancel</button>
                    <button type="submit" style="background:var(--accent);color:#000;">Save</button>
                    <button type="button" style="background:#e74c3c;color:white;" onclick="submitDelete()">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.onload = function(){
    document.getElementById('aePrice').addEventListener('input',function(){this.value=this.value.replace(/[^\d.]/g,'');});
};

const sideBar=document.getElementById('sideBar');
function toggleSidebar(){sideBar.classList.toggle('show');}

const addEditBackdrop=document.getElementById('addEditBackdrop');
function openAddModal(){
    document.getElementById('addEditTitle').innerText='Add Product';
    document.getElementById('aeAction').value='add';
    document.getElementById('aeId').value='';
    document.getElementById('aeCategory').value='Hot Coffee';
    document.getElementById('aeName').value='';
    document.getElementById('aeDescription').value='';
    document.getElementById('aePrice').value='';
    document.getElementById('aeStock').value=0;
    addEditBackdrop.style.display='flex';
}
function openEditModal(row){
    document.getElementById('addEditTitle').innerText='Edit Product';
    document.getElementById('aeAction').value='edit';
    document.getElementById('aeId').value=row.id;
    document.getElementById('aeCategory').value=row.category;
    document.getElementById('aeName').value=row.name;
    document.getElementById('aeDescription').value=row.description;
    document.getElementById('aePrice').value=parseFloat(row.price).toFixed(2);
    document.getElementById('aeStock').value=row.stock;
    addEditBackdrop.style.display='flex';
}
function closeAddEditModal(){addEditBackdrop.style.display='none';}
function submitDelete(){
    if(confirm("Delete this product?")){
        document.getElementById('aeAction').value='delete';
        document.getElementById('addEditForm').submit();
    }
}

function filterProducts(){
    const search = document.getElementById('searchInput').value.toLowerCase();
    const stockFilter = document.getElementById('stockFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    const cards = document.querySelectorAll('.products .card');

    cards.forEach(card=>{
        const name = card.dataset.name;
        const category = card.dataset.category;
        const stock = parseInt(card.dataset.stock);

        let stockMatch = true;
        if(stockFilter==='low') stockMatch = stock>0 && stock<=5;
        else if(stockFilter==='out') stockMatch = stock===0;

        const searchMatch = name.includes(search);
        const categoryMatch = categoryFilter === '' || category === categoryFilter;

        card.style.display = (stockMatch && searchMatch && categoryMatch) ? '' : 'none';
    });
}
</script>

</body>
</html>
