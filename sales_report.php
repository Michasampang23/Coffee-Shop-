<?php
session_start();
include "db_connect.php";

if(!isset($_SESSION['username'])){
    header("Location: login.php");
    exit();
}

$user_role = strtolower(trim($_SESSION['role']));
$username = $_SESSION['username'];

// Fetch transactions
$transactions = [];
$total_sales = 0;
$result = $conn->query("SELECT * FROM salereport ORDER BY dates DESC");
if(!$result){
    die("SQL Error: " . $conn->error);
}
while($row = $result->fetch_assoc()){
    $transactions[] = $row;
    $total_sales += $row['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sales Report - Coffee Haven</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
body {
    margin:0;
    font-family:'Poppins', sans-serif;
    background:#f3e6d7;
    color:#3a2c1a;
}

/* SIDEBAR */
.sidebar {
    width:0;
    height:100%;
    background:#4d3b27;
    position:fixed;
    top:0;
    left:0;
    overflow-x:hidden;
    transition:0.3s;
    padding-top:60px;
    z-index:20;
}
.sidebar a{
    display:block;
    padding:15px 25px;
    color:white;
    font-size:18px;
    text-decoration:none;
    border-bottom:1px solid rgba(255,255,255,0.2);
}
.sidebar a:hover{ background:#6b4e30; }
.sidebar .close-btn{
    position:absolute;
    top:15px;
    right:20px;
    font-size:24px;
    color:white;
    cursor:pointer;
}
.sidebar .close-btn:hover{ color:#ffce8a; }

/* NAVBAR */
.navbar{
    background:#4d3b27;
    padding:15px 20px;
    color:white;
    display:flex;
    justify-content:space-between;
    align-items:center;
    position:sticky;
    top:0;
    z-index:10;
}
.navbar h1{margin:0;}
.navbar .logout-btn{
    padding:8px 16px;
    background:#d3a676;
    color:#fff;
    text-decoration:none;
    border-radius:6px;
    transition:0.3s;
}
.navbar .logout-btn:hover{ background:#c9302c; }
.navbar .menu-btn{
    font-size:24px;
    background:none;
    border:none;
    color:white;
    cursor:pointer;
}

/* SEARCH & FILTER CONTAINER */
.search-filter-container {
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:10px;
    margin:20px 0;
}
.search-filter-container input,
.search-filter-container select{
    padding:8px 12px;
    border:1px solid #ccc;
    border-radius:6px;
}

/* MAIN CONTENT */
.main-content{
    padding:0 20px 20px 20px;
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 0 10px rgba(0,0,0,0.1);
}
th, td{
    padding:12px 15px;
    text-align:left;
    border-bottom:1px solid #eee;
}
th{
    background:#d3a676;
    color:#fff;
    font-weight:600;
}
tr:hover{background:#f5e4d1;}
.print-btn{
    margin-top:5px;
    padding:6px 12px;
    background:#5cb85c;
    color:#fff;
    border:none;
    border-radius:4px;
    cursor:pointer;
    font-weight:600;
}
.print-btn:hover{background:#449d44;}

/* Total Sale */
.total-sale {
    margin-top:15px;
    padding:10px;
    background:#d3a676;
    color:#fff;
    border-radius:6px;
    font-weight:600;
    text-align:right;
    width:fit-content;
}

/* Responsive Table */
@media(max-width:768px){
    table, thead, tbody, th, td, tr{
        display:block;
    }
    th{
        position:absolute;
        top:-9999px;
        left:-9999px;
    }
    td{
        border:none;
        position:relative;
        padding-left:50%;
        margin-bottom:10px;
    }
    td:before{
        position:absolute;
        left:15px;
        width:45%;
        white-space:nowrap;
        font-weight:600;
    }
    td:nth-of-type(1):before{content:"Date";}
    td:nth-of-type(2):before{content:"Item";}
    td:nth-of-type(3):before{content:"Qty";}
    td:nth-of-type(4):before{content:"Price";}
    td:nth-of-type(5):before{content:"Total";}
    td:nth-of-type(6):before{content:"User";}
    td:nth-of-type(7):before{content:"Category";}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" onclick="toggleSidebar()">✖</span>
    <?php if($user_role==='admin'): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="pos.php">Main POS</a>
        <a href="inventory.php">Inventory List</a>
        <a href="users.php">Users List</a>
    <?php elseif($user_role==='staff'): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="inventory.php">Inventory (View Only)</a>
    <?php endif; ?>
</div>

<!-- NAVBAR -->
<div class="navbar">
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <h1>Sales Report</h1>
    <a class="logout-btn" href="logout.php">Logout</a>
</div>

<!-- SEARCH & FILTER -->

<div class="main-content">
    <div class="search-filter-container">
        <input type="text" id="searchInput" placeholder="Search item or user..." onkeyup="filterTable()">
        <select id="categorySelect" onchange="filterCategory(this.value)">
            <option value="all">All Categories</option>
            <option value="Coffee">Coffee</option>
            <option value="Non-Coffee">Non-Coffee</option>
            <option value="Food">Food</option>
        </select>
        <!-- Removed only the general print button -->
    </div>


    <!-- TABLE -->
<table id="salesTable">
    <thead>
        <tr>
            <th>Date</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
            <th>User</th>
            <th>Category</th>
            <th>Print</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($transactions as $t): ?>
        <tr data-item="<?php echo htmlspecialchars($t['item']); ?>">
            <td><?php echo $t['dates']; ?></td>
            <td><?php echo $t['item']; ?></td>
            <td><?php echo $t['quantity']; ?></td>
            <td>₱<?php echo number_format($t['price'],2); ?></td>
            <td>₱<?php echo number_format($t['total'],2); ?></td>
            <td><?php echo $t['username']; ?></td>
            <td><?php echo $t['category']; ?></td>
            <td><button class="print-btn" onclick="printProduct('<?php echo addslashes($t['item']); ?>')">Print</button></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align:right; font-size:12px; color:black;">
                Total Ordered: <span id="totalOrdered">0</span> &nbsp;&nbsp;
                Total Sale: <span id="totalSale">₱0.00</span> &nbsp;&nbsp;
                Average Price: <span id="averagePrice">₱0.00</span>
            </td>
        </tr>
    </tfoot>
</table>

<script>
function toggleSidebar(){ 
    const sb = document.getElementById('sidebar'); 
    sb.style.width = sb.style.width==='250px'?'0':'250px'; 
}

function filterTable(){
    const input = document.getElementById("searchInput").value.toLowerCase();
    const rows = document.querySelectorAll("#salesTable tbody tr");
    rows.forEach(row=>{
        const cells = row.querySelectorAll("td");
        let match=false;
        cells.forEach(cell=>{
            if(cell.textContent.toLowerCase().includes(input)){
                match=true;
            }
        });
        row.style.display = match ? "" : "none";
    });
    updateTotal();
}

function filterCategory(category){
    const rows = document.querySelectorAll("#salesTable tbody tr");
    rows.forEach(row=>{
        if(category === "all" || row.cells[6].textContent === category){
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
    updateTotal();
}

// Update totals dynamically
function updateTotal(){
    const rows = document.querySelectorAll("#salesTable tbody tr");
    let totalSale = 0;
    let totalQty = 0;

    rows.forEach(row=>{
        if(row.style.display !== "none"){
            const qty = parseFloat(row.cells[2].textContent);
            const total = parseFloat(row.cells[4].textContent.replace('₱','').replace(',',''));
            totalQty += qty;
            totalSale += total;
        }
    });

    const avgPrice = totalQty>0 ? (totalSale/totalQty) : 0;

    // Update footer
    document.getElementById("totalOrdered").textContent = totalQty;
    document.getElementById("totalSale").textContent = "₱" + totalSale.toFixed(2);
    document.getElementById("averagePrice").textContent = "₱" + avgPrice.toFixed(2);
}

// Print only sales for one product with totals
function printProduct(itemName){
    const rows = Array.from(document.querySelectorAll(`#salesTable tbody tr[data-item='${itemName}']`));
    if(rows.length === 0) return alert("No sales found for this product.");
    let totalQty = 0;
    let totalSale = 0;
    let html = `<h2>Receipt - ${itemName}</h2><table border="1" style="border-collapse:collapse;width:100%;"><tr><th>Date</th><th>Qty</th><th>Price</th><th>Total</th></tr>`;

    rows.forEach(r=>{
        if(r.style.display !== "none"){
            const date = r.cells[0].textContent;
            const qty = parseFloat(r.cells[2].textContent);
            const price = parseFloat(r.cells[3].textContent.replace('₱','').replace(',',''));
            const total = parseFloat(r.cells[4].textContent.replace('₱','').replace(',',''));
            totalQty += qty;
            totalSale += total;
            html += `<tr><td>${date}</td><td>${qty}</td><td>₱${price.toFixed(2)}</td><td>₱${total.toFixed(2)}</td></tr>`;
        }
    });

    const avgPrice = totalQty>0 ? (totalSale/totalQty) : 0;
    html += `</table><h3>Total Ordered: ${totalQty}</h3>`;
    html += `<h3>Total Sale: ₱${totalSale.toFixed(2)}</h3>`;
    html += `<h3>Average Price: ₱${avgPrice.toFixed(2)}</h3>`;

    const w = window.open("", "", "width=800,height=600");
    w.document.write(html);
    w.document.close();
    w.print();
}

// Run on page load to initialize totals
updateTotal();
</script>


</body>
</html>  