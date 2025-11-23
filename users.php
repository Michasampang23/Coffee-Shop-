<?php
session_start();
include "db_connect.php";

// Protect page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_role = strtolower(trim($_SESSION['role'])); // admin/staff

// ================== UPDATE USER ==================
if (isset($_POST['update_user'])) {
    $id = intval($_POST['edit_id']);
    $username = trim($_POST['edit_username']);
    $role = trim($_POST['edit_role']);

    $stmt = $conn->prepare("UPDATE users SET username=?, role=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $role, $id);
    $stmt->execute();

    header("Location: users.php?msg=updated");
    exit();
}

// ================== DELETE USER ==================
if (isset($_POST['delete_user'])) {
    $id = intval($_POST['delete_id']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: users.php?msg=deleted");
    exit();
}

// Fetch all users
$result = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User List - Coffee Haven</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
body {
    font-family: 'Poppins', sans-serif;
    margin:0;
    background: #f3e6d7;
    color: #3a2c1a;
}
/* Sidebar */
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
.sidebar a {
    display:block;
    padding:15px 25px;
    color:white;
    font-size:18px;
    text-decoration:none;
    border-bottom:1px solid rgba(255,255,255,0.2);
}
.sidebar a:hover { background:#6b4e30; }
.sidebar .close-btn {
    position:absolute;
    top:15px;
    right:20px;
    font-size:24px;
    color:white;
    cursor:pointer;
}
.sidebar .close-btn:hover { color:#ffce8a; }

/* Navbar */
.navbar {
    background: #4d3b27;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    position: sticky;
    top: 0;
}
.navbar h1 { margin:0; }
.logout-btn {
    background: #d3a676;
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
}
.logout-btn:hover { background: #c9302c; }
.menu-btn {
    font-size:24px;
    background:none;
    border:none;
    color:white;
    cursor:pointer;
}

/* Main container */
.container { padding: 20px; max-width: 1000px; margin:auto; }
.search-reset { display:flex; gap:10px; margin-bottom: 15px; }
.search-reset input {
    padding:8px 10px; border-radius:6px; border:1px solid #ccc; flex:1;
}
.search-reset button {
    padding:8px 12px; border:none; border-radius:6px;
    cursor:pointer; background:#d3a676; color:#3a2c1a; font-weight:600;
}
.search-reset button:hover { background:#b68857; color:white; }

.role-buttons { margin-bottom:15px; }
.role-buttons button {
    margin-right:10px;
    padding:8px 15px;
    border:none; border-radius:6px;
    background:#d3a676; color:#3a2c1a; font-weight:600;
    cursor:pointer;
    transition:0.3s;
}
.role-buttons button:hover { background:#b68857; color:white; }
.role-buttons button.active { background:#4d3b27; color:white; }

table { width:100%; border-collapse: collapse; background:white; border-radius:6px; overflow:hidden; }
th, td { padding:12px; text-align:left; border-bottom:1px solid #ddd; }
th { background:#4d3b27; color:white; }
tr:hover { background:#f1e6d7; }

.action-btn {
    padding:6px 10px;
    border:none; border-radius:4px;
    cursor:pointer; color:white;
}
.edit-btn { background:#5cb85c; }
.edit-btn:hover { background:#449d44; }
.delete-btn { background:#d9534f; }
.delete-btn:hover { background:#c9302c; }

/* EDIT MODAL */
#editModal {
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
    z-index:50;
}
.edit-box {
    background:white;
    padding:20px;
    width:350px;
    border-radius:8px;
}
.edit-box h3 { margin-top:0; }
.edit-box input, .edit-box select {
    width:95%;
    padding:8px;
    margin-top:10px;
    border-radius:5px;
    border:1px solid #aaa;
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <span class="close-btn" onclick="toggleSidebar()">✖</span>
    <?php if($user_role === 'admin'): ?>
        <a href="dashboard.php">Dashboard</a>
        <a href="pos.php">Main POS</a>
        <a href="inventory.php">Inventory List</a>
        <a href="sales_report.php">Transaction History / Sales Report</a>
    <?php endif; ?>
</div>

<!-- Navbar -->
<div class="navbar">
    <button class="menu-btn" onclick="toggleSidebar()">☰</button>
    <h1>User List</h1>
    <a href="logout.php" class="logout-btn">Logout</a>
</div>

<div class="container">

    <!-- Search & Reset -->
    <div class="search-reset">
        <input type="text" id="searchInput" placeholder="Search users..." onkeyup="searchUsers()">
        <button onclick="resetSearch()">Reset</button>
    </div>

    <!-- Role Buttons -->
    <div class="role-buttons">
        <button id="btn-all" class="active" onclick="filterRole('all')">All</button>
        <button id="btn-admin" onclick="filterRole('admin')">Admin</button>
        <button id="btn-staff" onclick="filterRole('staff')">Staff</button>
    </div>

    <!-- User Table -->
    <table id="userTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <?php if($user_role === 'admin'): ?>
                <th>Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>

            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>

                    <?php if($user_role === 'admin'): ?>
                    <td>
                        <button class="action-btn edit-btn"
                            onclick="openEditModal(<?= $row['id'] ?>,'<?= $row['username'] ?>','<?= $row['role'] ?>')">
                            Edit
                        </button>

                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_user" class="action-btn delete-btn"
                                onclick="return confirm('Delete this user?');">
                                Delete
                            </button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>

        </tbody>
    </table>

</div>

<!-- EDIT MODAL -->
<div id="editModal">
    <form class="edit-box" method="POST">
        <h3>Edit User</h3>

        <input type="hidden" name="edit_id" id="edit_id">

        <label>Username</label>
        <input type="text" name="edit_username" id="edit_username" required>

        <label>Role</label>
        <select name="edit_role" id="edit_role">
            <option value="admin">Admin</option>
            <option value="staff">Staff</option>
        </select>

        <button type="submit" name="update_user" style="margin-top:10px; width:100%; background:#4d3b27; padding:10px; color:white; border:none; border-radius:5px;">
            Update User
        </button>

        <button type="button" onclick="closeEditModal()" style="margin-top:10px; width:100%; background:#d9534f; padding:10px; color:white; border:none; border-radius:5px;">
            Cancel
        </button>
    </form>
</div>

<script>
function toggleSidebar(){
    const sb = document.getElementById('sidebar');
    sb.style.width = sb.style.width === '250px' ? '0' : '250px';
}

let currentRole = 'all';

function filterRole(role){
    currentRole = role;
    searchUsers();
    document.querySelectorAll('.role-buttons button').forEach(btn => btn.classList.remove('active'));
    document.getElementById('btn-' + role).classList.add('active');
}

function searchUsers(){
    const input = document.getElementById('searchInput').value.toLowerCase();
    const trs = document.querySelectorAll("#userTable tbody tr");

    trs.forEach(tr => {
        const username = tr.children[1].textContent.toLowerCase();
        const role = tr.children[2].textContent.toLowerCase();

        const matchSearch = username.includes(input);
        const matchRole = currentRole === "all" || currentRole === role;

        tr.style.display = (matchSearch && matchRole) ? "" : "none";
    });
}

function resetSearch(){
    document.getElementById('searchInput').value = "";
    currentRole = "all";
    document.getElementById('btn-all').classList.add('active');
    searchUsers();
}

function openEditModal(id, username, role){
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_username").value = username;
    document.getElementById("edit_role").value = role;
    document.getElementById("editModal").style.display = "flex";
}

function closeEditModal(){
    document.getElementById("editModal").style.display = "none";
}
</script>

</body>
</html>
