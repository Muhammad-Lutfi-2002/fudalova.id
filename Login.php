<?php
session_start();
require_once 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password';
    } else {
        // Prepare SQL statement to prevent SQL injection
        $sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                // Redirect based on role
                if ($row['role'] == 'admin') {
                    header('Location: Admin/Dashboard.php');
                } else {
                    header('Location: customer_dashboard.php');
                }
                exit;
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mochi Daifuku - Login</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #ffd1dc 0%, #ff9eaa 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .bubble {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            animation: float 8s infinite ease-in-out;
            pointer-events: none;
        }

        @keyframes float {
            0% { transform: translateY(100vh) scale(0); opacity: 0; }
            50% { opacity: 0.8; }
            100% { transform: translateY(-100px) scale(1); opacity: 0; }
        }

        .auth-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-in-out;
            position: relative;
            z-index: 1;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-header h1 {
            color: #ff6b81;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #ff6b81;
        }

        .form-control {
            width: 100%;
            padding: 12px 40px;
            border: 2px solid #ffd1dc;
            border-radius: 25px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b81;
            box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.2);
        }

        .btn {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 25px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        text-align: center; /* Tambahan */
        text-decoration: none; /* Tambahan */
        display: inline-block; /* Tambahan */
    }

    .btn i {
        margin-right: 8px; /* Tambahan */
    }

    .btn-primary {
        background: #ff6b81;
        color: white;
    }

    .btn-primary:hover {
        background: #ff5266;
        transform: translateY(-2px);
    }

    .btn-outline {
        background: transparent;
        border: 2px solid #ff6b81;
        color: #ff6b81;
        display: flex; /* Tambahan */
        align-items: center; /* Tambahan */
        justify-content: center; /* Tambahan */
        gap: 8px; /* Tambahan */
    }

    .btn-outline:hover {
        background: #fff1f3;
    }

        .error-message {
            background: #ffe6e6;
            color: #ff0033;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0;
            color: #666;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-top: 1px solid #ddd;
            margin: 0 10px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .auth-footer a {
            color: #ff6b81;
            text-decoration: none;
            font-weight: bold;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        .back-home {
            position: fixed;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }

        .back-home a {
            color: #fff;
            text-decoration: none;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .back-home a:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 480px) {
            .auth-container {
                width: 95%;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h1>Mochi Daifuku</h1>
            <p>Welcome Back!</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" autocomplete="off">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" 
                       name="username" 
                       class="form-control" 
                       placeholder="Username or Email"
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       required>
            </div>
            
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" 
                       name="password" 
                       class="form-control" 
                       placeholder="Password"
                       required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>

            <div class="divider">or</div>

            <a href="register.php" class="btn btn-outline">
                <i class="fas fa-user-plus"></i> Create New Account
            </a>
        </form>

        <div class="auth-footer">
            <p>Forgot your password? <a href="forgot-password.php">Reset here</a></p>
        </div>
    </div>

    <div class="back-home">
        <a href="index.php">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Home</span>
        </a>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Create floating bubbles
        function createBubbles() {
            const colors = ['#ffd1dc', '#ffb7c5', '#ff9eaa', '#ffffff'];
            const bubblesCount = 15;
            
            for(let i = 0; i < bubblesCount; i++) {
                const bubble = document.createElement('div');
                bubble.className = 'bubble';
                
                const size = Math.random() * 60 + 20;
                const color = colors[Math.floor(Math.random() * colors.length)];
                const left = Math.random() * 100;
                const delay = Math.random() * 5;
                const duration = Math.random() * 4 + 6;
                
                bubble.style.width = `${size}px`;
                bubble.style.height = `${size}px`;
                bubble.style.background = color;
                bubble.style.left = `${left}%`;
                bubble.style.animationDelay = `${delay}s`;
                bubble.style.animationDuration = `${duration}s`;
                
                document.body.appendChild(bubble);
            }
        }

        createBubbles();

        // Add loading state to button when form is submitted
        const form = document.querySelector('form');
        const button = document.querySelector('.btn-primary');
        
        form.addEventListener('submit', function() {
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
            button.disabled = true;
        });
    });
    </script>
</body>
</html>