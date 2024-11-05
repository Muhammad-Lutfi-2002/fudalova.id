<?php
// Database Configuration
$host = 'localhost';
$username = 'root';  // Default username for XAMPP
$password = '';     // Default empty password for XAMPP
$database = 'mochi_daifuku_management';

// Create connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, 'utf8mb4');
?>