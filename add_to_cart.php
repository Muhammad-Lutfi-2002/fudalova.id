<?php
session_start();
include 'config.php';

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get POST data
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Validate input
if ($product_id <= 0 || $quantity <= 0) {
    exit(json_encode(['success' => false, 'message' => 'Invalid product or quantity']));
}

// Check product availability
$query = "SELECT p.*, c.price 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.product_id = ? AND p.status = 'available'";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'i', $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    exit(json_encode(['success' => false, 'message' => 'Product not found']));
}

// Check stock availability
if ($product['stock'] < $quantity) {
    exit(json_encode(['success' => false, 'message' => 'Insufficient stock']));
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Add or update cart
if (isset($_SESSION['cart'][$product_id])) {
    $new_quantity = $_SESSION['cart'][$product_id] + $quantity;
    if ($new_quantity > $product['stock']) {
        exit(json_encode(['success' => false, 'message' => 'Cannot exceed available stock']));
    }
    $_SESSION['cart'][$product_id] = $new_quantity;
} else {
    $_SESSION['cart'][$product_id] = $quantity;
}

// Return success response with cart count
$cart_count = array_sum($_SESSION['cart']);
echo json_encode([
    'success' => true,
    'message' => 'Product added to cart',
    'cartCount' => $cart_count
]);