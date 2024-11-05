<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - Mochi Daifuku</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-container {
            max-width: 500px;
            margin: 100px auto;
            padding: 2rem;
            text-align: center;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-icon i {
            color: white;
            font-size: 40px;
        }

        .success-title {
            color: #FF69B4;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .success-message {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .success-reference {
            color: #888;
            font-size: 0.9rem;
            margin: 2rem 0;
        }

        .continue-btn {
            display: block;
            width: 100%;
            padding: 1rem;
            background: #FF69B4;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            text-decoration: none;
            margin-bottom: 1rem;
        }

        .home-btn {
            display: block;
            width: 100%;
            padding: 0.9rem;
            background: white;
            color: #FF69B4;
            border: 2px solid #FF69B4;
            border-radius: 4px;
            font-size: 1.1rem;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h1 class="success-title">Payment Successful!</h1>
        <p class="success-message">Thank you for your purchase. Your transaction has been completed successfully.</p>
        <p class="success-message">A confirmation email with your order details has been sent to your email address.</p>
        <p class="success-reference">Order reference: #G0RU8MMKI</p>
        <a href="products.php" class="continue-btn">Continue Shopping</a>
        <a href="index.php" class="home-btn">Back to Home</a>
    </div>
</body>
</html>