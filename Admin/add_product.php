<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Handle file upload
    $image_url = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/';
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_url = 'uploads/' . $file_name;
        }
    }
    
    $query = "INSERT INTO products (name, description, price, stock, category, image_url) 
              VALUES (?, ?, ?, ?, ?, ?)";
              
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ssdiis", $name, $description, $price, $stock, $category, $image_url);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: products.php?success=Product added successfully');
        exit;
    } else {
        header('Location: products.php?error=Failed to add product');
        exit;
    }
}
?>