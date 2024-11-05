<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success - Mochi Daifuku</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-section {
            padding-top: 100px;
            min-height: calc(100vh - 400px);
        }

        .success-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .success-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #FFE4E1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .success-icon i {
            font-size: 40px;
            color: #FF69B4;
        }

        .order-details {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #FFE4E1;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            font-weight: bold;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #FF69B4;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #FF1493;
        }

        .btn-secondary {
            background: #FFE4E1;
            color: #FF69B4;
            border: none;
        }

        .btn-secondary:hover {
            background: #FFD1DC;
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
            <li><a href="shopping_cart.php" class="btn-order">CART (0)</a></li>
            <li><a href="login.php">LOGIN</a></li>
        </ul>
    </nav>

    <!-- Success Section -->
    <section class="success-section">
        <div class="success-container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Order Successfully Placed!</h2>
                <p>Thank you for your order. We've received your payment and are processing your order.</p>
            </div>

            <div class="order-details">
                <h3>Order Details</h3>
                <div class="detail-row">
                    <span class="detail-label">Order Number</span>
                    <span class="detail-value">#ORD123456</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Order Date</span>
                    <span class="detail-value">November 4, 2024</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">Bank Transfer</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value">Rp 15.000</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estimated Delivery</span>
                    <span class="detail-value">30-45 minutes</span>
                </div>
            </div>

            <div class="action-buttons">
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="orders.php" class="btn btn-primary">
                    <i class="fas fa-box"></i> Track Order
                </a>
            </div>
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
</body>
</html>