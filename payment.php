<?php
include 'config.php';
session_start();

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: shopping_cart.php');
    exit();
}

$payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';
$customer_name = isset($_POST['customer_name']) ? $_POST['customer_name'] : '';
$total_amount = isset($_POST['total_amount']) ? $_POST['total_amount'] : 0;

// Generate unique payment reference
$payment_ref = 'PAY-' . strtoupper(uniqid());

// Bank account details
$bank_accounts = [
    'BCA' => '1234567890',
    'Mandiri' => '0987654321',
    'BNI' => '1122334455'
];

// Function to generate fake QRIS data
function generateQRISData($payment_ref, $amount) {
    return "00020101021226590014ID.CO.QRIS.WWW0215ID10200000000310303UME51450015ID.OR.GPNQR.WWW02" . 
           str_pad($amount, 13, '0', STR_PAD_LEFT) . "5802ID5920Mochi Daifuku Store6007Jakarta62" . 
           strlen($payment_ref) . $payment_ref . "6304";
}

// Function to process payment and clear cart
function processPayment($conn, $customer_name, $payment_method, $total_amount, $cart_items) {
    mysqli_begin_transaction($conn);
    
    try {
        // Create sale record
        $query = "INSERT INTO sales (customer_name, payment_method, total_amount) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ssd', $customer_name, $payment_method, $total_amount);
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
        
        mysqli_commit($conn);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        return false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Mochi Daifuku</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .payment-section {
            padding-top: 100px;
            min-height: calc(100vh - 400px);
            background-color: #FFF5F7;
        }

        .payment-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .payment-header h1 {
            color: #FF69B4;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .payment-amount {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .payment-reference {
            background: #FFE4E1;
            padding: 0.5rem;
            border-radius: 4px;
            font-family: monospace;
            margin-bottom: 2rem;
        }

        .payment-method-details {
            margin-bottom: 2rem;
        }

        .bank-accounts {
            margin-bottom: 2rem;
        }

        .bank-account {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border: 1px solid #FFE4E1;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .qris-container {
            text-align: center;
            margin-bottom: 2rem;
        }

        .qris-code {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 8px;
            display: inline-block;
        }

        .pin-input {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .pin-digit {
            width: 40px;
            height: 40px;
            text-align: center;
            border: 2px solid #FF69B4;
            border-radius: 4px;
            font-size: 1.2rem;
        }

        .timer {
            text-align: center;
            margin-bottom: 1rem;
            color: #FF69B4;
            font-size: 1.2rem;
        }

        .instructions {
            background: #FFF5F7;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 2rem;
        }

        .instructions ol {
            margin-left: 1.5rem;
        }

        .instructions li {
            margin-bottom: 0.5rem;
        }

        .verify-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: #FF69B4;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            cursor: pointer;
            margin-bottom: 1rem;
        }

        .verify-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .copy-btn {
            padding: 0.5rem 1rem;
            background: #FF69B4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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

    <!-- Payment Section -->
    <section class="payment-section">
        <div class="payment-container">
            <div class="payment-header">
                <h1>Complete Your Payment</h1>
                <div class="payment-amount">
                    Total Amount: Rp <?php echo number_format($total_amount, 2); ?>
                </div>
                <div class="payment-reference">
                    Payment Reference: <?php echo $payment_ref; ?>
                </div>
                <div class="timer">
                    Time remaining: <span id="countdown">15:00</span>
                </div>
            </div>

            <?php if ($payment_method === 'transfer'): ?>
            <div class="payment-method-details">
                <h2>Bank Transfer Instructions</h2>
                <div class="instructions">
                    <ol>
                        <li>Select one of our bank accounts below</li>
                        <li>Transfer the exact amount to avoid payment verification issues</li>
                        <li>Use the payment reference as transfer description</li>
                        <li>Enter your bank security PIN to confirm the transfer</li>
                        <li>Wait for payment verification (usually within 5 minutes)</li>
                    </ol>
                </div>

                <div class="bank-accounts">
                    <?php foreach ($bank_accounts as $bank => $account): ?>
                    <div class="bank-account">
                        <div>
                            <strong><?php echo $bank; ?></strong><br>
                            <span><?php echo $account; ?></span>
                        </div>
                        <button class="copy-btn" onclick="copyToClipboard('<?php echo $account; ?>')">
                            Copy
                        </button>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="pin-container">
                    <h3>Enter Bank PIN to Confirm Transfer</h3>
                    <div class="pin-input">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                        <input type="password" maxlength="1" class="pin-digit" required>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <?php elseif ($payment_method === 'qris'): ?>
            <div class="payment-method-details">
                <h2>QRIS Payment</h2>
                <div class="instructions">
                    <ol>
                        <li>Open your mobile banking or e-wallet app</li>
                        <li>Scan the QR code below</li>
                        <li>Verify the merchant name and amount</li>
                        <li>Enter your PIN to confirm payment</li>
                        <li>Wait for payment verification (usually within 1 minute)</li>
                    </ol>
                </div>

                <div class="qris-container">
                    <div class="qris-code">
                        <img src="https://chart.googleapis.com/chart?cht=qr&chs=300x300&chl=<?php echo urlencode(generateQRISData($payment_ref, $total_amount)); ?>" alt="QRIS Code">
                    </div>
                </div>

                <div class="pin-container">
                    <h3>Enter E-wallet PIN to Confirm Payment</h3>
                    <div class="pin-input">
                        <?php for ($i = 0; $i < 6; $i++): ?>
                        <input type="password" maxlength="1" class="pin-digit" required>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <button type="button" id="verifyBtn" class="verify-btn" disabled>
                Verify Payment
            </button>
        </div>
    </section>

    <script>
        // PIN input handling
        const pinInputs = document.querySelectorAll('.pin-digit');
        const verifyBtn = document.getElementById('verifyBtn');
        
        pinInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value) {
                    if (index < pinInputs.length - 1) {
                        pinInputs[index + 1].focus();
                    }
                }
                
                // Enable verify button if all digits are filled
                const allFilled = Array.from(pinInputs).every(input => input.value);
                verifyBtn.disabled = !allFilled;
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    pinInputs[index - 1].focus();
                }
            });
        });

        // Countdown timer
        function startCountdown(duration, display) {
            let timer = duration, minutes, seconds;
            const countdown = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(countdown);
                    display.textContent = "Time expired";
                    verifyBtn.disabled = true;
                    // Redirect to cart or show expired message
                    window.location.href = 'shopping_cart.php?expired=1';
                }
            }, 1000);
        }

        // Start countdown
        const fifteenMinutes = 60 * 15;
        const display = document.querySelector('#countdown');
        startCountdown(fifteenMinutes, display);

        // Copy to clipboard function
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Account number copied to clipboard!');
            });
        }

        // Payment verification handler
        verifyBtn.addEventListener('click', () => {
            // Simulate payment verification
            verifyBtn.textContent = 'Verifying...';
            verifyBtn.disabled = true;

            setTimeout(() => {
                window.location.href = 'shopping_cart.php?success=1';
            }, 2000);
        });
    </script>
</body>
</html>