<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Order - Mochi Daifuku</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .tracking-section {
            padding-top: 100px;
            min-height: calc(100vh - 400px);
        }

        .tracking-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        .tracking-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .tracking-icon {
            width: 80px;
            height: 80px;
            background: #FFE4E1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .tracking-icon i {
            font-size: 40px;
            color: #FF69B4;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .order-status {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0;
            position: relative;
        }

        .status-step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .status-step::before {
            content: '';
            height: 3px;
            width: 100%;
            background: #FFE4E1;
            position: absolute;
            top: 15px;
            left: 50%;
            z-index: 1;
        }

        .status-step:last-child::before {
            display: none;
        }

        .status-icon {
            width: 35px;
            height: 35px;
            background: #FFE4E1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            position: relative;
            z-index: 2;
        }

        .status-icon.active {
            background: #FF69B4;
            color: white;
        }

        .status-text {
            font-size: 0.9rem;
            color: #666;
        }

        .status-text.active {
            color: #FF69B4;
            font-weight: bold;
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

        .btn {
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-primary {
            background: #FF69B4;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #FF1493;
        }

        .center-button {
            text-align: center;
            margin-top: 2rem;
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

    <!-- Tracking Section -->
    <section class="tracking-section">
        <div class="tracking-container">
            <div class="tracking-header">
                <div class="tracking-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h2>Track Your Order</h2>
                <p>Order #ORD123456</p>
            </div>

            <div class="order-card">
                <div class="order-status">
                    <div class="status-step">
                        <div class="status-icon active">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="status-text active">Order Placed</div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon active">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="status-text active">Payment Confirmed</div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon active">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="status-text active">Preparing</div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon">
                            <i class="fas fa-motorcycle"></i>
                        </div>
                        <div class="status-text">On Delivery</div>
                    </div>
                    <div class="status-step">
                        <div class="status-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="status-text">Delivered</div>
                    </div>
                </div>

                <div class="detail-row">
                    <span class="detail-label">Order Date</span>
                    <span class="detail-value">November 4, 2024 15:30</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Estimated Delivery</span>
                    <span class="detail-value">November 4, 2024 16:15</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Delivery Address</span>
                    <span class="detail-value">Jl. Bhayangkara No. 123, Sukabumi</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount</span>
                    <span class="detail-value">Rp 15.000</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">Bank Transfer</span>
                </div>
            </div>

            <div class="center-button">
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Back to Home
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