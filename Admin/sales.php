<?php
require_once '../config.php';

// Fetch sales with details
try {
    $result = mysqli_query($conn, "SELECT s.*, 
        GROUP_CONCAT(CONCAT(p.name, ' (', sd.quantity, ')') SEPARATOR ', ') as products,
        SUM(sd.subtotal) as total_amount
        FROM sales s
        LEFT JOIN sale_details sd ON s.sale_id = sd.sale_id
        LEFT JOIN products p ON sd.product_id = p.product_id
        GROUP BY s.sale_id
        ORDER BY s.transaction_date DESC");
    
    $sales = mysqli_fetch_all($result, MYSQLI_ASSOC);
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Management - Mochi Daifuku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary-color: #ff6b81;
            --secondary-color: #ff6b81;
        }

        body {
            min-height: 100vh;
            background-color: #f5f5f5;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--primary-color);
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            transform: translateX(-180px);
        }

        .sidebar-header {
            padding: 20px;
            background: var(--secondary-color);
            text-align: center;
        }

        .sidebar-logo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid white;
            animation: pulse 2s infinite;
        }
        .sidebar-header h2 {
            transition: opacity 0.3s;
        }

        .nav {
    display: flex;
    flex-direction: column;
    gap: 5px;  /* Untuk memberikan jarak antar menu */
    padding: 0 20px; /* Padding kiri-kanan */
}

.nav-link {
    color: white;
    padding: 15px;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.nav-link i {
    width: 20px; /* Memberikan lebar tetap untuk icon */
    margin-right: 10px;
    margin : 5px;
}

.nav-item {
    width: 100%;
}

.nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.nav-link:active,
.nav-link:visited,
.nav-link:focus,
.nav-link.active {
    color: white !important;
    background: none !important;
    box-shadow: none !important;
    outline: none !important;
}
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
        }
/* Card Summary Styles */
.card.bg-primary {
    background-color: #ff6b81 !important; 
}

.card.bg-success {
    background-color: #ff6b81 !important; 
}

.card.bg-info {
    background-color:#ff6b81 !important; 
}

/* Optional: Hover effect */
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(255, 107, 129, 0.2); /* Shadow dengan warna pink */
}
        /* Card Styles */
        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .car-body {
            background-color: var(--primary-color);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        /* Table Styles */
        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--primary-color);
            color: white;
            border: none;
        }

        .table td {
            vertical-align: middle;
        }

        /* Button Styles */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-action {
            padding: 5px 10px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: scale(1.1);
        }

        /* Animation Keyframes */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .sale-item {
            animation: fadeIn 0.5s ease-out forwards;
            opacity: 0;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .modal-body {
            padding: 20px;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.expanded {
                margin-left: 0;
            }
        }

        /* Loading Spinner */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255,255,255,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .spinner-overlay.active {
            visibility: visible;
            opacity: 1;
        }
    </style>
</head>
<body>
<div class="loading">
        <div class="loading-spinner"></div>
    </div>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4>Mochi Daifuku</h4>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php">
            <i class="fas fa-home"></i></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="products.php">
                <i class="fas fa-box"></i> Products
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="sales.php">
                <i class="fas fa-shopping-cart"></i> Sales
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="inventory.php">
                <i class="fas fa-warehouse"></i> Inventory
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="reports.php">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="../logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-cart"></i> Sales Management</h2>
                <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#addSaleModal">
                    <i class="fas fa-plus"></i> New Sale
                </button>
            </div>

            <!-- Sales Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Today's Sales</h5>
                            <h3 class="mb-0">
                                <?php
                                $today_sales = mysqli_query($conn, "SELECT SUM(subtotal) as total FROM sale_details 
                                    JOIN sales ON sales.sale_id = sale_details.sale_id 
                                    WHERE DATE(transaction_date) = CURDATE()");
                                $today_total = mysqli_fetch_assoc($today_sales)['total'] ?? 0;
                                echo 'Rp ' . number_format($today_total, 0, ',', '.');
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Transactions</h5>
                            <h3 class="mb-0">
                                <?php
                                $count_sales = mysqli_query($conn, "SELECT COUNT(*) as count FROM sales");
                                echo mysqli_fetch_assoc($count_sales)['count'];
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Average Sale Value</h5>
                            <h3 class="mb-0">
                                <?php
                                $avg_sale = mysqli_query($conn, "SELECT AVG(subtotal) as avg FROM sale_details");
                                $avg_value = mysqli_fetch_assoc($avg_sale)['avg'] ?? 0;
                                echo 'Rp ' . number_format($avg_value, 0, ',', '.');
                                ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Products</th>
                                    <th>Total Amount</th>
                                    <th>Payment Method</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $index => $sale): ?>
                                <tr class="sale-item" style="animation-delay: <?= $index * 0.1 ?>s">
                                    <td>#<?= $sale['sale_id'] ?></td>
                                    <td><?= date('Y-m-d H:i', strtotime($sale['transaction_date'])) ?></td>
                                    <td><?= htmlspecialchars($sale['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($sale['products']) ?></td>
                                    <td>Rp <?= number_format($sale['total_amount'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge bg-<?= $sale['payment_method'] === 'cash' ? 'success' : 
                                            ($sale['payment_method'] === 'transfer' ? 'primary' : 'warning') ?>">
                                            <?= ucfirst($sale['payment_method']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info btn-action me-1" 
                                                onclick="viewSale(<?= $sale['sale_id'] ?>)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-action" 
                                                onclick="deleteSale(<?= $sale['sale_id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Sale Modal -->
    <div class="modal fade" id="addSaleModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Sale</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addSaleForm">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer Name</label>
                                <input type="text" class="form-control" name="customer_name" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="cash">Cash</option>
                                    <option value="transfer">Transfer</option>
                                    <option value="qris">QRIS</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="2"></textarea>
                        </div>

                        <div id="productsList" class="mb-3">
                            <label class="form-label">Products</label>
                            <div class="product-entry mb-2">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <select class="form-select" name="products[]" required>
                                            <option value="">Select Product</option>
                                            <?php
                                            $products_query = mysqli_query($conn, "SELECT p.*, c.name as category_name 
                                                FROM products p 
                                                JOIN categories c ON p.category_id = c.category_id 
                                                WHERE p.stock > 0");
                                            while ($product = mysqli_fetch_assoc($products_query)) {
                                                echo "<option value='{$product['product_id']}'>{$product['name']} ({$product['category_name']})</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" class="form-control" name="quantities[]" min="1" placeholder="Quantity" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm w-100 remove-product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-secondary btn-sm mb-3" id="addMoreProducts">
                            <i class="fas fa-plus"></i> Add More Products
                        </button>

                        <div class="text-end">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Sale</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Sale Modal -->
    <div class="modal fade" id="viewSaleModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sale Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="saleDetailsContent">
                    <!-- Content will be loaded dynamically -->
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="spinner-overlay" id="loadingSpinner">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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


        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Add Sale Form Handling
        $(document).ready(function() {
            $('#addMoreProducts').click(function() {
                const productEntry = $('.product-entry').first().clone();
                productEntry.find('input').val('');
                productEntry.find('select').val('');
                $('#productsList').append(productEntry);
            });

            $(document).on('click', '.remove-product', function() {
                if ($('.product-entry').length > 1) {
                    $(this).closest('.product-entry').remove();
                }
            });

            $('#addSaleForm').submit(function(e) {
                e.preventDefault();
                showLoading();

                $.ajax({
                    url: 'add_sale.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        hideLoading();
                        $('#addSaleModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        alert('Error adding sale: ' + error);
                    }
                });
            });
        });

        function viewSale(saleId) {
            showLoading();
            $.ajax({
                url: 'get_sale_details.php',
                method: 'GET',
                data: { sale_id: saleId },
                success: function(response) {
                    hideLoading();
                    $('#saleDetailsContent').html(response);
                    $('#viewSaleModal').modal('show');
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    alert('Error fetching sale details: ' + error);
                }
            });
        }

        function deleteSale(saleId) {
            if (confirm('Are you sure you want to delete this sale?')) {
                showLoading();
                $.ajax({
                    url: 'delete_sale.php',
                    method: 'POST',
                    data: { sale_id: saleId },
                    success: function(response) {
                        hideLoading();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        alert('Error deleting sale: ' + error);
                    }
                });
            }
        }

        function showLoading() {
            $('#loadingSpinner').addClass('active');
        }

        function hideLoading() {
            $('#loadingSpinner').removeClass('active');
        }

        // Responsive sidebar
        if (window.innerWidth <= 768) {
            document.getElementById('sidebar').classList.add('collapsed');
            document.getElementById('mainContent').classList.add('expanded');
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.getElementById('mainContent').classList.add('expanded');
            } else {
                document.getElementById('sidebar').classList.remove('collapsed');
                document.getElementById('mainContent').classList.remove('expanded');
            }
        });
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