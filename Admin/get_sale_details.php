<?php
require_once '../config.php';

if (isset($_GET['sale_id'])) {
    $sale_id = $_GET['sale_id'];
    
    $query = "SELECT s.*, 
        sd.quantity, sd.price_per_unit, sd.subtotal,
        p.name as product_name,
        c.name as category_name
        FROM sales s
        JOIN sale_details sd ON s.sale_id = sd.sale_id
        JOIN products p ON sd.product_id = p.product_id
        JOIN categories c ON p.category_id = c.category_id
        WHERE s.sale_id = ?";
        
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $sale_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $sale_details = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    if (count($sale_details) > 0) {
        $first_row = $sale_details[0];
        ?>
        <div class="sale-info mb-4">
            <h6>Sale #<?= $sale_id ?></h6>
            <p class="mb-1">Customer: <?= htmlspecialchars($first_row['customer_name']) ?></p>
            <p class="mb-1">Date: <?= date('Y-m-d H:i', strtotime($first_row['transaction_date'])) ?></p>
            <p class="mb-1">Payment Method: <?= ucfirst($first_row['payment_method']) ?></p>
            <?php if ($first_row['notes']): ?>
                <p class="mb-1">Notes: <?= htmlspecialchars($first_row['notes']) ?></p>
            <?php endif; ?>
        </div>
        
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($sale_details as $detail): 
                        $total += $detail['subtotal'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($detail['product_name']) ?></td>
                            <td><?= htmlspecialchars($detail['category_name']) ?></td>
                            <td><?= $detail['quantity'] ?></td>
                            <td>Rp <?= number_format($detail['price_per_unit'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($detail['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-primary">
                        <td colspan="4" class="text-end"><strong>Total</strong></td>
                        <td><strong>Rp <?= number_format($total, 0, ',', '.') ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        echo '<p class="text-center">No sale details found.</p>';
    }
}