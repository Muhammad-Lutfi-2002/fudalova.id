<?php
require_once '../config.php';

// Fetch basic statistics
try {
    // Total products
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
    $row = mysqli_fetch_assoc($result);
    $totalProducts = $row['total'];
    
    // Total sales today
    $result = mysqli_query($conn, "SELECT COUNT(*) as total, COALESCE(SUM(total_amount), 0) as revenue 
                         FROM sales 
                         WHERE DATE(transaction_date) = CURDATE()");
    $salesData = mysqli_fetch_assoc($result);
    
    // Low stock products
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock < 20");
    $row = mysqli_fetch_assoc($result);
    $lowStock = $row['total'];
    
    // Recent sales with product details
    $result = mysqli_query($conn, "SELECT s.*, GROUP_CONCAT(p.name) as products
                         FROM sales s
                         LEFT JOIN sale_details sd ON s.sale_id = sd.sale_id
                         LEFT JOIN products p ON sd.product_id = p.product_id
                         GROUP BY s.sale_id
                         ORDER BY s.transaction_date DESC
                         LIMIT 5");
    $recentSales = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $recentSales[] = $row;
    }

    // Get monthly sales data for chart
    $result = mysqli_query($conn, "SELECT 
                            DATE_FORMAT(transaction_date, '%M') as month,
                            SUM(total_amount) as revenue
                         FROM sales
                         WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                         GROUP BY MONTH(transaction_date)
                         ORDER BY transaction_date");
    $monthlySales = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $monthlySales[] = $row;
    }
    
} catch(Exception $e) {
    error_log($e->getMessage());
    $error = "An error occurred while fetching dashboard data.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mochi Daifuku Dashboard</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <style>
        :root {
            --primary: #ff6b81;
            --secondary: #ffd3d9;
            --dark: #2d3436;
            --light: #f8f9fa;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #d63031;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f0f2f5;
            overflow-x: hidden;
        }

        .wrapper {
            display: flex;
            min-height: 100vh;
        }

        /* Animated Sidebar */
        .sidebar {
            width: 260px;
            background: var(--primary);
            padding: 20px;
            transition: all 0.3s ease;
            position: fixed;
            height: 100vh;
            z-index: 1000;
        }

        .sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px 0;
            text-align: center;
            color: white;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .sidebar-header h2 {
            opacity: 0;
        }

        .menu-item {
            padding: 15px;
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 8px;
            margin: 5px 0;
        }

        .menu-item:hover {
            background: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .menu-item span {
            transition: opacity 0.3s;
        }

        .sidebar.collapsed .menu-item span {
            opacity: 0;
            width: 0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

        /* Modern Header */
        .header {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toggle-sidebar {
            background: none;
            border: none;
            color: var(--dark);
            cursor: pointer;
            font-size: 1.2rem;
            padding: 10px;
        }

        /* Animated Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1));
            top: 0;
            left: -100%;
            transition: 0.5s;
        }

        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card h3 {
            color: var(--dark);
            font-size: 1rem;
            margin-bottom: 15px;
        }

        .stat-card .value {
            font-size: 2rem;
            color: var(--primary);
            font-weight: bold;
        }

        /* Interactive Charts Section */
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Modern Table */
        .recent-sales {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .sales-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .sales-table th {
            background: var(--light);
            padding: 15px;
            text-align: left;
            color: var(--dark);
            font-weight: 600;
            border-bottom: 2px solid var(--primary);
        }

        .sales-table td {
            padding: 15px;
            background: white;
            transition: all 0.3s ease;
        }

        .sales-table tr {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .sales-table tr:hover td {
            background: var(--light);
            transform: scale(1.01);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 80px;
            }

            .sidebar .sidebar-header h2,
            .sidebar .menu-item span {
                opacity: 0;
                width: 0;
            }

            .main-content {
                margin-left: 80px;
            }

            .charts-container {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }

        /* Loading Animation */
        .loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--secondary);
            border-top: 5px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary);
        }
    </style>
</head>
<body>
    <!-- Loading Animation -->
    <div class="loading">
        <div class="loading-spinner"></div>
    </div>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>Mochi Daifuku</h2>
            </div>
            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
                <a href="products.php" class="menu-item">
                    <i class="fas fa-box"></i>
                    <span>Products</span>
                </a>
                <a href="sales.php" class="menu-item">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Sales</span>
                </a>
                <a href="inventory.php" class="menu-item">
                    <i class="fas fa-warehouse"></i>
                    <span>Inventory</span>
                </a>
                <a href="reports.php" class="menu-item">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
                <a href="../logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header animate__animated animate__fadeIn">
                <button class="toggle-sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
                </div>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger animate__animated animate__fadeIn">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card animate__animated animate__fadeInUp">
                    <h3>Total Products</h3>
                    <div class="value" data-target="<?php echo $totalProducts; ?>">0</div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    <h3>Today's Sales</h3>
                    <div class="value" data-target="<?php echo $salesData['total']; ?>">0</div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <h3>Today's Revenue</h3>
                    <div class="value">Rp <span data-target="<?php echo $salesData['revenue']; ?>">0</span></div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                    <h3>Low Stock Items</h3>
                    <div class="value" data-target="<?php echo $lowStock; ?>">0</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-container">
                <div class="chart-card animate__animated animate__fadeIn">
                    <h3>Monthly Sales</h3>
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Recent Sales Table -->
            <div class="recent-sales animate__animated animate__fadeIn">
                <h2>Recent Sales</h2>
                <table class="sales-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Customer</th>
                            <th>Products</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentSales as $sale): ?>
                            <tr>
                                <td><?php echo date('d M Y H:i', strtotime($sale['transaction_date'])); ?></td>
                                <td><?php echo htmlspecialchars($sale['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($sale['products']); ?></td>
                                <td>Rp <?php echo number_format($sale['total_amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($sale['payment_method']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Remove loading animation when page is fully loaded
        window.addEventListener('load', () => {
            document.querySelector('.loading').style.display = 'none';
        });

        // Animate numbers in stat cards
        function animateValue(obj, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                obj.innerHTML = Math.floor(progress * (end - start) + start).toLocaleString();
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        // Trigger number animations when elements are in viewport
        const observers = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const target = parseInt(entry.target.getAttribute('data-target'));
                    animateValue(entry.target, 0, target, 2000);
                    observers.unobserve(entry.target);
                }
            });
        });

        document.querySelectorAll('.value').forEach(el => observers.observe(el));

        // Sidebar Toggle
        const toggleBtn = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthlySales, 'month')); ?>,
                datasets: [{
                    label: 'Monthly Revenue',
                    data: <?php echo json_encode(array_column($monthlySales, 'revenue')); ?>,
                    borderColor: '#ff6b81',
                    backgroundColor: 'rgba(255, 107, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Monthly Sales Trend'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString();
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                }
            }
        });

        // Add hover effects to menu items
        document.querySelectorAll('.menu-item').forEach(item => {
            item.addEventListener('mouseenter', (e) => {
                const icon = e.currentTarget.querySelector('i');
                icon.classList.add('animate__animated', 'animate__rubberBand');
            });

            item.addEventListener('mouseleave', (e) => {
                const icon = e.currentTarget.querySelector('i');
                icon.classList.remove('animate__animated', 'animate__rubberBand');
            });
        });

        // Add smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add animated notification badge
        function showNotification(message) {
            const notification = document.createElement('div');
            notification.className = 'notification animate__animated animate__fadeInRight';
            notification.innerHTML = message;
            document.body.appendChild(notification);

            setTimeout(() => {
                notification.classList.remove('animate__fadeInRight');
                notification.classList.add('animate__fadeOutRight');
                setTimeout(() => notification.remove(), 1000);
            }, 3000);
        }

        // Mochi Animation
        class MochiAnimation {
            constructor() {
                this.canvas = document.createElement('canvas');
                this.canvas.className = 'mochi-animation';
                document.querySelector('.header').appendChild(this.canvas);
                this.ctx = this.canvas.getContext('2d');
                this.mochis = [];
                this.init();
            }

            init() {
                this.canvas.width = 150;
                this.canvas.height = 50;
                this.createMochis();
                this.animate();
            }

            createMochis() {
                for (let i = 0; i < 3; i++) {
                    this.mochis.push({
                        x: 20 + i * 50,
                        y: 25,
                        radius: 10,
                        color: `hsl(${350 + i * 20}, 100%, 88%)`,
                        bounce: 0
                    });
                }
            }

            animate() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                
                this.mochis.forEach((mochi, index) => {
                    mochi.bounce += 0.1;
                    const y = mochi.y + Math.sin(mochi.bounce) * 5;

                    this.ctx.beginPath();
                    this.ctx.arc(mochi.x, y, mochi.radius, 0, Math.PI * 2);
                    this.ctx.fillStyle = mochi.color;
                    this.ctx.fill();
                    this.ctx.closePath();
                });

                requestAnimationFrame(() => this.animate());
            }
        }

        // Initialize Mochi Animation
        new MochiAnimation();
    </script>
</body>
</html>