<?php
require_once '../config.php';

// Fetch report data
try {
    // Sales by category
    $result = mysqli_query($conn, "SELECT 
        c.name as category,
        COUNT(sd.sale_detail_id) as total_sales,
        SUM(sd.quantity) as total_items,
        SUM(sd.subtotal) as revenue
        FROM categories c
        LEFT JOIN products p ON c.category_id = p.category_id
        LEFT JOIN sale_details sd ON p.product_id = sd.product_id
        LEFT JOIN sales s ON sd.sale_id = s.sale_id
        WHERE s.transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY c.category_id");
    
    $categoryData = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categoryData[] = $row;
    }
    
    // Top selling products
    $result = mysqli_query($conn, "SELECT 
        p.name,
        SUM(sd.quantity) as total_sold,
        SUM(sd.subtotal) as revenue
        FROM products p
        LEFT JOIN sale_details sd ON p.product_id = sd.product_id
        LEFT JOIN sales s ON sd.sale_id = s.sale_id
        WHERE s.transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY p.product_id
        ORDER BY total_sold DESC
        LIMIT 5");
    
    $topProducts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $topProducts[] = $row;
    }
    
    // Daily revenue for the last 7 days
    $result = mysqli_query($conn, "SELECT 
        DATE(transaction_date) as date,
        COUNT(*) as total_transactions,
        SUM(total_amount) as daily_revenue
        FROM sales
        WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        GROUP BY DATE(transaction_date)
        ORDER BY date");
    
    $dailyRevenue = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $dailyRevenue[] = $row;
    }
    
    // Payment method distribution
    $result = mysqli_query($conn, "SELECT 
        payment_method,
        COUNT(*) as count,
        SUM(total_amount) as total
        FROM sales
        WHERE transaction_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        GROUP BY payment_method");
    
    $paymentMethods = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $paymentMethods[] = $row;
    }
    
} catch(Exception $e) {
    error_log($e->getMessage());
    $error = "An error occurred while fetching report data.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mochi Daifuku Reports</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <style>
        /* Copy all styles from Dashboard.php */
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

        .main-content {
            flex: 1;
            padding: 20px;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 80px;
        }

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
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0,0,0,0.15);
        }

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

        .data-table {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .data-table table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .data-table th {
            background: var(--light);
            padding: 15px;
            text-align: left;
            color: var(--dark);
            font-weight: 600;
            border-bottom: 2px solid var(--primary);
        }

        .data-table td {
            padding: 15px;
            background: white;
        }

        .data-table tr {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .data-table tr:hover td {
            background: var(--light);
        }

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

        .date-filter {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .date-filter form {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .date-filter input[type="date"] {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .date-filter button {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .date-filter button:hover {
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

            <!-- Date Filter -->
            <div class="date-filter animate__animated animate__fadeIn">
                <form method="GET">
                    <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days')); ?>">
                    <input type="date" name="end_date" value="<?php echo $_GET['end_date'] ?? date('Y-m-d'); ?>">
                    <button type="submit">Apply Filter</button>
                </form>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card animate__animated animate__fadeInUp">
                    <h3>Total Revenue (30 Days)</h3>
                    <div class="value">Rp <span id="totalRevenue">0</span></div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    <h3>Total Transactions</h3>
                    <div class="value" id="totalTransactions">0</div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <h3>Average Transaction Value</h3>
                    <div class="value">Rp <span id="avgTransaction">0</span></div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                    <h3>Total Items Sold</h3>
                    <div class="value" id="totalItems">0</div>
                </div>
            </div>

            <!-- Charts -->
            <div class="charts-container">
                <div class="chart-card animate__animated animate__fadeIn">
                    <h3>Daily Revenue (Last 7 Days)</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
                <div class="chart-card animate__animated animate__fadeIn">
                    <h3>Sales by Category</h3>
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <!-- Top Products Table -->
            <div class="data-table animate__animated animate__fadeIn">
                <h2>Top Selling Products</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProducts as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo number_format($product['total_sold']); ?></td>
                                <td>Rp <?php echo number_format($product['revenue'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payment Methods Table -->
            <div class="data-table animate__animated animate__fadeIn">
                <h2>Payment Method Distribution</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Payment Method</th>
                            <th>Number of Transactions</th>
                            <th>Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paymentMethods as $method): ?>
                            <tr>
                                <td><?php echo ucfirst(htmlspecialchars($method['payment_method'])); ?></td>
                                <td><?php echo number_format($method['count']); ?></td>
                                <td>Rp <?php echo number_format($method['total'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Toggle Sidebar
        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapsed');
            document.querySelector('.main-content').classList.toggle('expanded');
        });

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode(array_column($dailyRevenue, 'date')); ?>,
                    datasets: [{
                        label: 'Daily Revenue',
                        data: <?php echo json_encode(array_column($dailyRevenue, 'daily_revenue')); ?>,
                        borderColor: '#ff6b81',
                        backgroundColor: 'rgba(255, 107, 129, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
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
                    }
                }
            });

            // Category Sales Chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'pie',
                data: {
                    labels: <?php echo json_encode(array_column($categoryData, 'category')); ?>,
                    datasets: [{
                        data: <?php echo json_encode(array_column($categoryData, 'revenue')); ?>,
                        backgroundColor: [
                            '#ff6b81',
                            '#ffd3d9',
                            '#ff9eb5'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });

            // Update Summary Statistics
            const totalRevenue = <?php echo array_sum(array_column($dailyRevenue, 'daily_revenue')); ?>;
            const totalTransactions = <?php echo array_sum(array_column($dailyRevenue, 'total_transactions')); ?>;
            const avgTransaction = totalTransactions > 0 ? totalRevenue / totalTransactions : 0;
            const totalItems = <?php echo array_sum(array_column($categoryData, 'total_items')); ?>;

            document.getElementById('totalRevenue').textContent = totalRevenue.toLocaleString();
            document.getElementById('totalTransactions').textContent = totalTransactions.toLocaleString();
            document.getElementById('avgTransaction').textContent = avgTransaction.toLocaleString();
            document.getElementById('totalItems').textContent = totalItems.toLocaleString();
        });

        // Loading Animation
        window.addEventListener('load', function() {
            document.querySelector('.loading').style.display = 'none';
        });
    </script>
</body>
</html>