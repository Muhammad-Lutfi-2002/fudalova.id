<?php
include 'config.php';

// Fetch all products with their categories
$query = "SELECT p.*, c.name as category_name, c.price 
          FROM products p 
          JOIN categories c ON p.category_id = c.category_id 
          WHERE p.status = 'available' AND p.stock > 0
          ORDER BY p.category_id, p.name";
$result = mysqli_query($conn, $query);

// Group products by category
$products_by_category = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products_by_category[$row['category_name']][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Mochi Daifuku</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .products-section {
            padding-top: 100px;
            min-height: calc(100vh - 400px);
        }

        .products-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .category-section {
            margin-bottom: 3rem;
        }

        .category-title {
            color: #FF69B4;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #FFE4E1;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image {
            width: 100%;
            height: 200px;
            background: #FFE4E1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image i {
            font-size: 3rem;
            color: #FF69B4;
        }

        .product-info {
            padding: 1.5rem;
        }

        .product-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .product-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1rem;
            height: 40px;
            overflow: hidden;
        }

        .product-price {
            color: #FF69B4;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .product-stock {
            color: #4CAF50;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .add-to-cart {
            width: 100%;
            padding: 0.75rem;
            background: #FF69B4;
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .add-to-cart:hover {
            background: #FF1493;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #FFE4E1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: white;
            color: #FF69B4;
        }

        .quantity-input {
            width: 50px;
            text-align: center;
            border: 1px solid #FFE4E1;
            border-radius: 15px;
            padding: 0.25rem;
        }

        /* Cart notification */
        #cart-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #4CAF50;
            color: white;
            padding: 1rem;
            border-radius: 8px;
            display: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">Mochi Daifuku</div>
        <ul class="nav-links">
            <li><a href="index.php">HOME</a></li>
            <li><a href="index.php#menu">MENU</a></li>
            <li><a href="index.php#locations">LOCATIONS</a></li>
            <li><a href="index.php#about">ABOUT</a></li>
            <li><a href="shopping_cart.php" class="btn-order">CART (<span id="cart-count">0</span>)</a></li>
            <li><a href="login.php">LOGIN</a></li>
        </ul>
    </nav>

    <!-- Cart Notification -->
    <div id="cart-notification">Item added to cart!</div>

    <!-- Products Section -->
    <section class="products-section">
        <div class="products-container">
            <?php foreach ($products_by_category as $category => $products): ?>
            <div class="category-section">
                <h2 class="category-title"><?php echo htmlspecialchars($category); ?></h2>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <i class="fas fa-cookie"></i>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <p class="product-price">Rp <?php echo number_format($product['price'], 2); ?></p>
                            <p class="product-stock">Stock: <?php echo $product['stock']; ?> available</p>
                            <div class="quantity-control">
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $product['product_id']; ?>, -1)">-</button>
                                <input type="number" id="quantity-<?php echo $product['product_id']; ?>" class="quantity-input" value="1" min="1" max="<?php echo $product['stock']; ?>">
                                <button class="quantity-btn" onclick="updateQuantity(<?php echo $product['product_id']; ?>, 1)">+</button>
                            </div>
                            <button class="add-to-cart" onclick="addToCart(<?php echo $product['product_id']; ?>)">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
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
                    <li><a href="index.php#menu">Menu</a></li>
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
        function updateQuantity(productId, change) {
            const input = document.getElementById(`quantity-${productId}`);
            let newValue = parseInt(input.value) + change;
            const maxStock = parseInt(input.max);
            
            if (newValue >= 1 && newValue <= maxStock) {
                input.value = newValue;
            }
        }

        function showNotification() {
            const notification = document.getElementById('cart-notification');
            notification.style.display = 'block';
            setTimeout(() => {
                notification.style.display = 'none';
            }, 2000);
        }

        function updateCartCount() {
            const cartCount = document.getElementById('cart-count');
            let currentCount = parseInt(cartCount.textContent);
            cartCount.textContent = currentCount + 1;
        }

        function updateQuantity(productId, change) {
    const input = document.getElementById(`quantity-${productId}`);
    let newValue = parseInt(input.value) + change;
    const maxStock = parseInt(input.max);
    
    if (newValue >= 1 && newValue <= maxStock) {
        input.value = newValue;
    }
}

function showNotification(message, isError = false) {
    const notification = document.getElementById('cart-notification');
    notification.textContent = message;
    notification.style.backgroundColor = isError ? '#ff4444' : '#4CAF50';
    notification.style.display = 'block';
    setTimeout(() => {
        notification.style.display = 'none';
    }, 2000);
}

function updateCartCount(count) {
    const cartCount = document.getElementById('cart-count');
    cartCount.textContent = count;
}

function addToCart(productId) {
    const quantity = document.getElementById(`quantity-${productId}`).value;
    const addToCartBtn = document.querySelector(`[onclick="addToCart(${productId})"]`);
    
    // Disable button during request
    addToCartBtn.disabled = true;
    
    fetch('add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message);
            updateCartCount(data.cartCount);
        } else {
            showNotification(data.message, true);
        }
    })
    .catch(error => {
        showNotification('Error adding product to cart', true);
    })
    .finally(() => {
        addToCartBtn.disabled = false;
    });
}
    </script>
</body>
</html>