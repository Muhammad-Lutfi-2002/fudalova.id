<?php
require_once '../config.php';

// Fetch products with their categories
try {
    $result = mysqli_query($conn, "SELECT p.*, c.name as category_name, c.price 
                                  FROM products p 
                                  JOIN categories c ON p.category_id = c.category_id
                                  ORDER BY p.product_id DESC");
    $products = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    // Fetch categories for the add/edit form
    $categories = mysqli_query($conn, "SELECT * FROM categories");
    
} catch(Exception $e) {
    error_log($e->getMessage());
    $error = "An error occurred while fetching products data.";
}

// Handle Add/Edit/Delete operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $category_id = (int)$_POST['category_id'];
                $stock = (int)$_POST['stock'];
                
                mysqli_query($conn, "INSERT INTO products (name, category_id, stock) 
                                   VALUES ('$name', $category_id, $stock)");
                break;

            case 'edit':
                $id = (int)$_POST['product_id'];
                $name = mysqli_real_escape_string($conn, $_POST['name']);
                $category_id = (int)$_POST['category_id'];
                $stock = (int)$_POST['stock'];
                
                mysqli_query($conn, "UPDATE products 
                                   SET name = '$name', category_id = $category_id, stock = $stock 
                                   WHERE product_id = $id");
                break;

                case 'delete':
                    $id = (int)$_POST['product_id'];
                    
                    // Hapus data di sale_details terlebih dahulu
                    mysqli_query($conn, "DELETE FROM sale_details WHERE product_id = $id");
                    // Baru hapus data produk
                    mysqli_query($conn, "DELETE FROM products WHERE product_id = $id");
                    break;
            }
            header('Location: products.php');
            exit;
        }
        
        // Redirect to refresh the page
        header('Location: products.php');
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Mochi Daifuku</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
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
        /* Additional styles for Products page */
        .products-container {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .add-product-btn {
            background: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-product-btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .products-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 10px;
        }

        .products-table th {
            background: var(--light);
            padding: 15px;
            text-align: left;
            color: var(--dark);
            font-weight: 600;
            border-bottom: 2px solid var(--primary);
        }

        .products-table td {
            padding: 15px;
            background: white;
            transition: all 0.3s ease;
        }

        .products-table tr {
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .products-table tr:hover td {
            background: var(--light);
        }

        .action-buttons button {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .edit-btn {
            background: var(--warning);
            color: white;
        }

        .delete-btn {
            background: var(--danger);
            color: white;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .modal-buttons button {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .save-btn {
            background: var(--success);
            color: white;
        }

        .cancel-btn {
            background: var(--light);
            color: var(--dark);
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

            <!-- Products Container -->
            <div class="products-container animate__animated animate__fadeIn">
                <div class="products-header">
                    <h2>Products Management</h2>
                    <button class="add-product-btn" onclick="showModal('add')">
                        <i class="fas fa-plus"></i> Add Product
                    </button>
                </div>

                <table class="products-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                <td>Rp <?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                <td class="action-buttons">
                                    <button class="edit-btn" onclick="showModal('edit', <?php echo htmlspecialchars(json_encode($product)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="delete-btn" onclick="deleteProduct(<?php echo $product['product_id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal for Add/Edit Product -->
    <div class="modal" id="productModal">
        <div class="modal-content">
            <h2 id="modalTitle">Add Product</h2>
            <form id="productForm" method="POST">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="product_id" id="productId">
                
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="category_id">Category</label>
                    <select id="category_id" name="category_id" required>
                        <?php while ($category = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?php echo $category['category_id']; ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="stock">Stock</label>
                    <input type="number" id="stock" name="stock" required min="0">
                </div>
                
                <div class="modal-buttons">
                    <button type="button" class="cancel-btn" onclick="hideModal()">Cancel</button>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Remove loading animation when page is fully loaded
        window.addEventListener('load', () => {
            document.querySelector('.loading').style.display = 'none';
        });

        // Sidebar Toggle
        const toggleBtn = document.querySelector('.toggle-sidebar');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        });

        // Modal Functions
        function showModal(action, product = null) {
            const modal = document.getElementById('productModal');
            const form = document.getElementById('productForm');
            const modalTitle = document.getElementById('modalTitle');
            
            if (action === 'add') {
                modalTitle.textContent = 'Add Product';
                form.action.value = 'add';
                form.reset();
            } else {
                modalTitle.textContent = 'Edit Product';
                form.action.value = 'edit';
                form.product_id.value = product.product_id;
                form.name.value = product.name;
                form.category_id.value = product.category_id;
                form.stock.value = product.stock;
            }
            
            modal.style.display = 'flex';
        }

        function hideModal() {
            document.getElementById('productModal').style.display = 'none';
        }

        function deleteProduct(productId) {
            if (confirm('Are you sure you want to delete this product?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="product_id" value="${productId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('productModal');
            if (event.target === modal) {
                hideModal();
            }
        }

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
    </script>
</body>
</html>