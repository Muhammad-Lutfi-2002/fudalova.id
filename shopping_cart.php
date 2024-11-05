<?php
include 'config.php';

// Initialize cart if not exists
session_start();
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle quantity updates
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];
    
    if ($new_quantity > 0) {
        $_SESSION['cart'][$product_id] = $new_quantity;
    } else {
        unset($_SESSION['cart'][$product_id]);
    }
    header('Location: shopping_cart.php');
    exit();
}

// Handle remove item
if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    unset($_SESSION['cart'][$product_id]);
    header('Location: shopping_cart.php');
    exit();
}

// Fetch cart items with details
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $product_ids = array_keys($_SESSION['cart']);
    $ids_string = implode(',', array_map('intval', $product_ids)); // Sanitize IDs
    
    $query = "SELECT p.*, c.name as category_name, c.price 
              FROM products p 
              JOIN categories c ON p.category_id = c.category_id 
              WHERE p.product_id IN ($ids_string)";
    $result = mysqli_query($conn, $query);
    
    while ($row = mysqli_fetch_assoc($result)) {
        $quantity = $_SESSION['cart'][$row['product_id']];
        $row['quantity'] = $quantity;
        $row['subtotal'] = $quantity * $row['price'];
        $cart_items[] = $row;
        $total += $row['subtotal'];
    }
}

// Handle cash payment checkout
if (isset($_POST['checkout']) && $_POST['payment_method'] === 'cash' && !empty($cart_items)) {
    // Validate stock availability
    $stock_valid = true;
    $out_of_stock_items = [];
    
    foreach ($cart_items as $item) {
        $query = "SELECT stock FROM products WHERE product_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $item['product_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $current_stock = mysqli_fetch_assoc($result)['stock'];
        mysqli_stmt_close($stmt);
        
        if ($current_stock < $item['quantity']) {
            $stock_valid = false;
            $out_of_stock_items[] = $item['name'];
        }
    }
    
    if (!$stock_valid) {
        $error_message = "Some items are no longer available in requested quantity: " . 
                        implode(", ", $out_of_stock_items);
    } else {
        $customer_name = isset($_POST['customer_name']) ? mysqli_real_escape_string($conn, $_POST['customer_name']) : 'Guest';
        
        // Begin transaction
        mysqli_begin_transaction($conn);
        
        try {
            // Create sale record - Removed 'status' field from query
            $query = "INSERT INTO sales (customer_name, payment_method, total_amount) VALUES (?, 'cash', ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'sd', $customer_name, $total);
            mysqli_stmt_execute($stmt);
            $sale_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            
            // Process each sale detail
            foreach ($cart_items as $item) {
                // Insert sale detail
                $query = "INSERT INTO sale_details (sale_id, product_id, quantity, price_per_unit, subtotal) 
                         VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'iiidi', $sale_id, $item['product_id'], 
                                     $item['quantity'], $item['price'], $item['subtotal']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                
                // Update inventory
                $query = "UPDATE products SET stock = stock - ? WHERE product_id = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, 'ii', $item['quantity'], $item['product_id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            
            // Commit transaction
            mysqli_commit($conn);
            
            // Clear cart
            $_SESSION['cart'] = [];
            header('Location: shopping_cart.php?success=1');
            exit();
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error_message = "Error processing checkout: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Mochi Daifuku</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-section {
            padding-top: 100px;
            min-height: calc(100vh - 400px);
        }

        .cart-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .cart-title {
            color: #FF69B4;
            font-size: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .cart-empty {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2rem;
        }

        .cart-table th,
        .cart-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #FFE4E1;
        }

        .cart-table th {
            color: #FF69B4;
            font-weight: bold;
        }

        .cart-product {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .cart-product-image {
            width: 80px;
            height: 80px;
            background: #FFE4E1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }

        .cart-product-image i {
            font-size: 2rem;
            color: #FF69B4;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .quantity-input {
            width: 60px;
            text-align: center;
            padding: 0.5rem;
            border: 1px solid #FFE4E1;
            border-radius: 4px;
        }

        .update-btn,
        .remove-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .update-btn {
            background: #FF69B4;
            color: white;
        }

        .remove-btn {
            background: #ff4444;
            color: white;
        }

        .cart-summary {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .cart-total {
            font-size: 1.5rem;
            color: #FF69B4;
            margin-bottom: 1rem;
            text-align: right;
        }

        .checkout-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            color: #666;
        }

        .form-group input,
        .form-group select {
            padding: 0.75rem;
            border: 1px solid #FFE4E1;
            border-radius: 4px;
        }

        .checkout-btn {
            padding: 1rem;
            background: #FF69B4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
        }

        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .success-message {
            background: #4CAF50;
            color: white;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">Mochi Daifuku</div>
        <ul class="nav-links">
            <li><a href="index.php">HOME</a></li>
            <li><a href="products.php">PRODUCTS</a></li>
            <li><a href="index.php#locations">LOCATIONS</a></li>
            <li><a href="index.php#about">ABOUT</a></li>
            <li><a href="shopping_cart.php" class="btn-order">CART (<?php echo count($_SESSION['cart']); ?>)</a></li>
            <li><a href="login.php">LOGIN</a></li>
        </ul>
    </nav>

    <!-- Cart Section -->
    <section class="cart-section">
        <div class="cart-container">
            <h1 class="cart-title">Shopping Cart</h1>
            
            <?php if (isset($_GET['success'])): ?>
            <div class="success-message">
                Thank you for your purchase! Your order has been processed successfully.
            </div>
            <?php endif; ?>

            <?php if (isset($_GET['expired'])): ?>
            <div class="error-message">
                Your payment session has expired. Please try again.
            </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
            <?php endif; ?>

            <?php if (empty($cart_items)): ?>
            <div class="cart-empty">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Browse our delicious mochi selection and add some items to your cart!</p>
                <a href="products.php" class="view-products-btn">View Products</a>
            </div>
            <?php else: ?>
            <div class="cart-content">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                        <tr>
                            <td>
                                <div class="cart-product">
                                    <div class="cart-product-image">
                                        <i class="fas fa-cookie"></i>
                                    </div>
                                    <div>
                                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                        <p><?php echo htmlspecialchars($item['category_name']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>Rp <?php echo number_format($item['price'], 2); ?></td>
                            <td>
                                <form method="post" class="quantity-control">
                                    <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                    <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="0" max="<?php echo $item['stock']; ?>" class="quantity-input">
                                    <button type="submit" name="update_quantity" class="update-btn">Update</button>
                                </form>
                            </td>
                            <td>Rp <?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <a href="?remove=<?php echo $item['product_id']; ?>" class="remove-btn">Remove</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
    <div class="cart-total">
        Total: Rp <?php echo number_format($total, 2); ?>
    </div>
    <form method="post" id="checkoutForm" class="checkout-form">
        <div class="form-group">
            <label for="customer_name">Your Name</label>
            <input type="text" id="customer_name" name="customer_name" required>
        </div>
        <div class="form-group">
            <label for="payment_method">Payment Method</label>
            <select id="payment_method" name="payment_method" required>
                <option value="cash">Cash</option>
                <option value="transfer">Bank Transfer</option>
                <option value="qris">QRIS</option>
            </select>
        </div>
        <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
        <button type="submit" name="checkout" class="checkout-btn">
            Proceed to Checkout
        </button>
    </form>
</div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Mochi Daifuku</h3>
                <p>Crafting sweet moments since 2017</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="index.php#locations">Locations</a></li>
                    <li><a href="index.php#about">About</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>+62 857-9875-4461</p>
                <p>hello@mochidaifuku.com</p>
                <p>Sukabumi Jawa barat, Indonesia</p>
            </div>
            <div class="footer-section">
                <h3>Stay Sweet</h3>
                <p>Subscribe for special offers and new flavor updates!</p>
                <div class="newsletter-form">
                    <input type="email" placeholder="Enter your email">
                    <button>Subscribe</button>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Mochi Daifuku. All rights reserved.</p>
        </div>
    </footer>
    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const paymentMethod = document.getElementById('payment_method').value;
    
    if (paymentMethod !== 'cash') {
        e.preventDefault();
        this.action = 'payment.php';
        this.submit();
    }
    // For cash payments, let the form submit normally to be handled by the PHP logic
});
    </script>
</body>
</html>