<?php
require_once '../config.php';

// Handle Add Product
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $stock = mysqli_real_escape_string($conn, $_POST['stock']);
        
        $query = "INSERT INTO products (name, category_id, price, stock) VALUES ('$name', '$category_id', '$price', '$stock')";
        mysqli_query($conn, $query);
        header('Location: inventory.php?msg=added');
        exit();
    }
    
    // Handle Edit Product
    if ($_POST['action'] == 'edit') {
        $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
        $price = mysqli_real_escape_string($conn, $_POST['price']);
        $stock = mysqli_real_escape_string($conn, $_POST['stock']);
        
        $query = "UPDATE products SET 
                  name = '$name', 
                  category_id = '$category_id', 
                  price = '$price', 
                  stock = '$stock' 
                  WHERE product_id = '$product_id'";
        mysqli_query($conn, $query);
        header('Location: inventory.php?msg=updated');
        exit();
    }

    // Handle Restock
    if ($_POST['action'] == 'restock') {
        $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
        $add_stock = mysqli_real_escape_string($conn, $_POST['add_stock']);
        
        $query = "UPDATE products SET stock = stock + $add_stock WHERE product_id = '$product_id'";
        mysqli_query($conn, $query);
        header('Location: inventory.php?msg=restocked');
        exit();
    }
}
// Fetch inventory statistics and data
try {
    // Total products in inventory
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
    $row = mysqli_fetch_assoc($result);
    $totalProducts = $row['total'];
    
    // Low stock products (less than 20)
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock < 20");
    $row = mysqli_fetch_assoc($result);
    $lowStock = $row['total'];
    
    // Out of stock products
    $result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products WHERE stock = 0");
    $row = mysqli_fetch_assoc($result);
    $outOfStock = $row['total'];
    
    // Average stock level
    $result = mysqli_query($conn, "SELECT AVG(stock) as avg_stock FROM products");
    $row = mysqli_fetch_assoc($result);
    $avgStock = round($row['avg_stock']);
    
    // Get detailed inventory data
    $result = mysqli_query($conn, "SELECT p.*, c.name as category_name 
                                 FROM products p 
                                 JOIN categories c ON p.category_id = c.category_id 
                                 ORDER BY p.stock ASC");
    $inventoryItems = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $inventoryItems[] = $row;
    }

    // Get stock levels by category for chart
    $result = mysqli_query($conn, "SELECT c.name, SUM(p.stock) as total_stock 
                                 FROM products p 
                                 JOIN categories c ON p.category_id = c.category_id 
                                 GROUP BY c.category_id");
    $categoryStock = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categoryStock[] = $row;
    }
    
} catch(Exception $e) {
    error_log($e->getMessage());
    $error = "An error occurred while fetching inventory data.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management - Mochi Daifuku</title>
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
        /* Add additional styles for inventory-specific elements */
        .stock-level {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: bold;
        }

        .stock-low {
            background: var(--danger);
            color: white;
        }

        .stock-medium {
            background: var(--warning);
            color: var(--dark);
        }

        .stock-good {
            background: var(--success);
            color: white;
        }

        .inventory-actions {
            display: flex;
            gap: 10px;
        }

        .action-button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: var(--warning);
        }

        .restock-btn {
            background: var(--success);
            color: white;
        }

        .filter-section {
            margin-bottom: 20px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-input {
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            min-width: 200px;
        }

        .stock-progress {
            width: 100%;
            height: 8px;
            background: #eee;
            border-radius: 4px;
            overflow: hidden;
        }

        .stock-progress-bar {
            height: 100%;
            border-radius: 4px;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body>
    <!-- Loading Animation -->
   

    <div class="wrapper">
        <!-- Sidebar (same as Dashboard.php) -->
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
                    <h3>Low Stock Items</h3>
                    <div class="value" data-target="<?php echo $lowStock; ?>">0</div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <h3>Out of Stock</h3>
                    <div class="value" data-target="<?php echo $outOfStock; ?>">0</div>
                </div>
                <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                    <h3>Average Stock Level</h3>
                    <div class="value" data-target="<?php echo $avgStock; ?>">0</div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="filter-section animate__animated animate__fadeIn">
                <input type="text" id="searchInput" class="filter-input" placeholder="Search products...">
                <select id="categoryFilter" class="filter-input">
                    <option value="">All Categories</option>
                    <?php
                    $categories = mysqli_query($conn, "SELECT * FROM categories");
                    while ($category = mysqli_fetch_assoc($categories)) {
                        echo "<option value='" . htmlspecialchars($category['name']) . "'>" . 
                             htmlspecialchars($category['name']) . "</option>";
                    }
                    ?>
                </select>
                <select id="stockFilter" class="filter-input">
                    <option value="">All Stock Levels</option>
                    <option value="low">Low Stock</option>
                    <option value="out">Out of Stock</option>
                    <option value="good">Good Stock</option>
                </select>
            </div>

            <!-- Charts -->
            <div class="charts-container">
                <div class="chart-card animate__animated animate__fadeIn">
                    <h3>Stock Levels by Category</h3>
                    <canvas id="stockChart"></canvas>
                </div>
            </div>

            <!-- Inventory Table -->
            <div class="recent-sales animate__animated animate__fadeIn">
                <h2>Inventory Status</h2>
                <table class="sales-table" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Category</th>
                            <th>Stock Level</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventoryItems as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                <td>
                                    <div class="stock-progress">
                                        <?php
                                        $percentage = min(($item['stock'] / 100) * 100, 100);
                                        $colorClass = $item['stock'] < 20 ? 'stock-low' : 
                                                    ($item['stock'] < 50 ? 'stock-medium' : 'stock-good');
                                        ?>
                                        <div class="stock-progress-bar <?php echo $colorClass; ?>" 
                                             style="width: <?php echo $percentage; ?>%"></div>
                                    </div>
                                    <?php echo $item['stock']; ?> units
                                </td>
                                <td>
                                    <?php
                                    if ($item['stock'] == 0) {
                                        echo '<span class="stock-level stock-low">Out of Stock</span>';
                                    } elseif ($item['stock'] < 20) {
                                        echo '<span class="stock-level stock-low">Low Stock</span>';
                                    } elseif ($item['stock'] < 50) {
                                        echo '<span class="stock-level stock-medium">Medium Stock</span>';
                                    } else {
                                        echo '<span class="stock-level stock-good">Good Stock</span>';
                                    }
                                    ?>
                                </td>
                                <td class="inventory-actions">
                                    <button class="action-button edit-btn" 
                                            onclick="editProduct(<?php echo $item['product_id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-button restock-btn" 
                                            onclick="restockProduct(<?php echo $item['product_id']; ?>)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Copy all JavaScript from Dashboard.php
        // Add inventory-specific JavaScript

        // Stock Chart
        const stockCtx = document.getElementById('stockChart').getContext('2d');
        new Chart(stockCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($categoryStock, 'name')); ?>,
                datasets: [{
                    label: 'Stock Levels',
                    data: <?php echo json_encode(array_column($categoryStock, 'total_stock')); ?>,
                    backgroundColor: '#ff6b81',
                    borderColor: '#ff6b81',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Search and Filter Functionality
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const stockFilter = document.getElementById('stockFilter');
        const table = document.getElementById('inventoryTable');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryTerm = categoryFilter.value.toLowerCase();
            const stockTerm = stockFilter.value.toLowerCase();

            const rows = table.getElementsByTagName('tr');

            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                const productName = row.cells[0].textContent.toLowerCase();
                const category = row.cells[1].textContent.toLowerCase();
                const stockLevel = row.cells[3].textContent.toLowerCase();

                const matchesSearch = productName.includes(searchTerm);
                const matchesCategory = !categoryTerm || category === categoryTerm;
                const matchesStock = !stockTerm || 
                                   (stockTerm === 'low' && stockLevel.includes('low')) ||
                                   (stockTerm === 'out' && stockLevel.includes('out')) ||
                                   (stockTerm === 'good' && stockLevel.includes('good'));

                row.style.display = matchesSearch && matchesCategory && matchesStock ? '' : 'none';
            }
        }

        searchInput.addEventListener('input', filterTable);
        categoryFilter.addEventListener('change', filterTable);
        stockFilter.addEventListener('change', filterTable);

        // Product Management Functions
        function editProduct(productId) {
            // Implement edit product functionality
            alert('Edit product ' + productId);
        }

        function restockProduct(productId) {
            // Implement restock functionality
            alert('Restock product ' + productId);
        }
    </script>
</body>
</html>