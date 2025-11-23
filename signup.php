<?php
session_start();
include "db_connect.php";

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Get verify code from form for admin, empty for staff
    $verifycode = ($role === "admin") ? trim($_POST['verifycode']) : "";

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (username, password, role, verifycode) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashedPassword, $role, $verifycode);

    if ($stmt->execute()) {
        // Automatically log in after signup
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "❌ Error: Username might already exist.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bean Street Cafe  Signup</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }

body {
    background: url("bg.jpg") no-repeat center center / cover;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.signup-container {
    width: 900px;
    height: 550px;
    display: flex;
    border-radius: 25px;
    overflow: hidden;
    background: rgba(134, 84, 28, 0.25);
    backdrop-filter: blur(8px);
}

.left-side { flex: 1; display: flex; justify-content: center; align-items: center; background: transparent; }
.left-side img { width: 500px; }

.right-side {
    flex: 1;
    background: rgba(134, 84, 28, 0.45);
    padding: 50px;
    color: black;
    display: flex;
    flex-direction: column;
    justify-content: center;
    backdrop-filter: blur(5px);
}

h3 { text-align: center; margin-bottom: 25px; font-size: 20px; }
label { color: black; }
input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 10px;
    background: rgba(255,255,255,0.15);
    border: none;
    color: black;
}

select option { color: black; }

.password-box { position: relative; }
.password-box .toggle { position: absolute; right: 12px; top: 12px; cursor: pointer; color: black; }

.btn {
    width: 100%;
    padding: 12px;
    border-radius: 10px;
    background: #fff;
    font-size: 16px;
    font-weight: bold;
    border: none;
    margin-bottom: 20px;
    cursor: pointer;
}

.divider { text-align: center; margin: 12px 0; }
.login-text { text-align: center; }
.login-text a { color: #ffddaa; }

.error {
    background: #ff3333;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
}

.success {
    background: #33cc33;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
}
</style>
</head>
<body>

<div class="signup-container">

    <div class="left-side">
        <img src="logo.png" alt="Logo">
    </div>

    <div class="right-side">

        <h3>Create Your Account</h3>

        <?php
        if (!empty($error)) echo "<p class='error'>$error</p>";
        if (!empty($success)) echo "<p class='success'>$success</p>";
        ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" required>

            <label>Password</label>
            <div class="password-box">
                <input type="password" name="password" id="password" required>
                <span class="toggle" onclick="togglePass()">👁</span>
            </div>

            <label>Role</label>
            <select name="role" id="role" required onchange="handleRole()">
                <option value="staff">Staff</option>
                <option value="admin">Admin</option>
            </select>

            <div id="verifyContainer" style="display:none;">
                <label>Enter Your Verification Code</label>
                <input type="text" id="verifycode" name="verifycode" placeholder="">
            </div>

            <button type="submit" class="btn">Sign Up</button>

            <div class="divider">— OR —</div>

            <p class="login-text">Already have an account?  
                <a href="login.php">Login</a>
            </p>

        </form>

    </div>

</div>

<script>
function togglePass() {
    let p = document.getElementById("password");
    p.type = p.type === "password" ? "text" : "password";
}

function handleRole() {
    const role = document.getElementById("role").value;
    const container = document.getElementById("verifyContainer");

    if(role === "admin") {
        container.style.display = "block";
    } else {
        container.style.display = "none";
        document.getElementById("verifycode").value = '';
    }
}
</script>

</body>
</html>
