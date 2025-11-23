
<?php
session_start();
include "db_connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $verifycode = trim($_POST["verifycode"]); // new field

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row["password"])) {

            // Check verify code if admin
            if ($row["role"] === "admin") {
                if (empty($verifycode) || $verifycode !== $row["verifycode"]) {
                    $error = "❌ Invalid verification code for admin.";
                } else {
                    $_SESSION["username"] = $row["username"];
                    $_SESSION["role"] = $row["role"];
                    header("Location: dashboard.php");
                    exit();
                }
            } else {
                // Staff login
                $_SESSION["username"] = $row["username"];
                $_SESSION["role"] = $row["role"];
                header("Location: dashboard.php");
                exit();
            }

        } else {
            $error = "❌ Incorrect password.";
        }
    } else {
        $error = "❌ Account not found.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bean Street Cafe Login</title>
<style>
    
* { margin: 0; padding: 0; box-sizing: border-box; font-family: "Poppins", sans-serif; }
body {
    background: url("bg.jpg") no-repeat center center / cover;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}
.login-container {
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
    color: #000000ff;
    display: flex;
    flex-direction: column;
    justify-content: center;
    backdrop-filter: blur(5px);
}
h3 { text-align: center; margin-bottom: 25px; font-size: 20px; }
input, select {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 10px;
    background: rgba(255,255,255,0.15);
    border: none;
    color: white;
}
.password-box { position: relative; }
.password-box .toggle { position: absolute; right: 12px; top: 12px; cursor: pointer; color: white; }
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
.signup-text { text-align: center; }
.signup-text a { color: #ffddaa; }
.error {
    background: #ff3333;
    padding: 10px;
    border-radius: 8px;
    text-align: center;
    margin-bottom: 20px;
}
/* Keep all your previous styling */
.right-side { color: black; }
input, select { color: black; }
label { color: black; }
.password-box .toggle { color: black; }
</style>
</head>
<body>

<div class="login-container">

    <div class="left-side">
        <img src="logo.png" alt="Logo">
    </div>

    <div class="right-side">

        <h3>Welcome Back — Log In</h3>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="email" required>

            <label>Password</label>
            <div class="password-box">
                <input type="password" name="password" id="password" required>
                <span class="toggle" onclick="togglePass()">👁</span>
            </div>

            <label>Verify Code (Admin Only)</label>
            <input type="text" name="verifycode" placeholder="">

            <button type="submit" class="btn">Sign In</button>

            <div class="divider">— OR —</div>

            <p class="signup-text">No account yet?  
                <a href="signup.php">Create One</a>
            </p>

        </form>

    </div>

</div>

<script>
function togglePass() {
    let p = document.getElementById("password");
    p.type = p.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
