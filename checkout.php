<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Mochi Daifuku</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .checkout-section {
            padding-top: 100px;
            min-height: calc(100vh - 400px);
        }

        .checkout-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .checkout-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #FFE4E1;
            border-radius: 8px;
            font-size: 1rem;
        }

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .order-summary {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            align-self: start;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #FFE4E1;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #FF69B4;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .place-order-btn {
            width: 100%;
            padding: 1rem;
            background: #FF69B4;
            color: white;
            border: none;
            border-radius: 25px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
            margin-top: 1rem;
        }

        .place-order-btn:hover {
            background: #FF1493;
        }

        .payment-methods {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }

        .payment-method {
            flex: 1;
            padding: 1rem;
            border: 1px solid #FFE4E1;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover,
        .payment-method.active {
            border-color: #FF69B4;
            background: #FFF0F5;
        }

        .payment-method i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #FF69B4;
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
            <li><a href="shopping_cart.php" class="btn-order">CART (3)</a></li>
            <li><a href="login.php">LOGIN</a></li>
        </ul>
    </nav>

    <!-- Checkout Section -->
    <section class="checkout-section">
        <div class="checkout-container">
            <div class="checkout-form">
                <div class="section-header">
                    <h2>Checkout</h2>
                    <p>Please fill in your delivery details</p>
                </div>

                <form action="process_order.php" method="POST">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="form-group">
                        <label for="address">Delivery Address</label>
                        <textarea id="address" name="address" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <div class="payment-methods">
                            <div class="payment-method active">
                                <i class="fas fa-money-bill-wave"></i>
                                <div>Cash</div>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-credit-card"></i>
                                <div>Transfer</div>
                            </div>
                            <div class="payment-method">
                                <i class="fas fa-qrcode"></i>
                                <div>QRIS</div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">Order Notes (Optional)</label>
                        <textarea id="notes" name="notes"></textarea>
                    </div>
                </form>
            </div>

            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span>Choco Crunchy (2x)</span>
                    <span>Rp 10.000</span>
                </div>
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>Rp 10.000</span>
                </div>
                <div class="summary-item">
                    <span>Delivery Fee</span>
                    <span>Rp 5.000</span>
                </div>
                <div class="summary-total">
                    <span>Total</span>
                    <span>Rp 15.000</span>
                </div>
                <button class="place-order-btn" onclick="window.location.href='Order.php'">Place Order</button>
                
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