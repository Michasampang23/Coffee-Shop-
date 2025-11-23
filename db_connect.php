<?php
// ===== DATABASE CONFIGURATION =====
$host = "localhost";     // Database Host
$user = "root";          // Database Username
$pass = "";              // Database Password
$dbname = "beanstreet";   // Your Database Name

// ===== CREATE CONNECTION =====
$conn = new mysqli($host, $user, $pass, $dbname);

// ===== CHECK CONNECTION =====
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Optional: set UTF-8 encoding
$conn->set_charset("utf8");
?>
