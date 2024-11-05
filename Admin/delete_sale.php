<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sale_id'])) {
    $sale_id = $_POST['sale_id'];

    try {
        mysqli_begin_transaction($conn);

        // Get products and quantities to restore inventory
        $query = "SELECT product_id, quantity FROM sale_details WHERE sale_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $sale_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            // Restore inventory
            $restore_query = "UPDATE products SET stock = stock + ? WHERE product_id = ?";
            $stmt = mysqli_prepare($conn, $restore_query);
            mysqli_stmt_bind_param($stmt, "ii", $row['quantity'], $row['product_id']);
            mysqli_stmt_execute($stmt);
            
            // Record inventory movement
            $movement_query = "INSERT INTO inventory_movement (product_id, quantity, movement_type, notes) 
                             VALUES (?, ?, 'in', 'Sale cancelled')";
            $stmt = mysqli_prepare($conn, $movement_query);
            mysqli_stmt_bind_param($stmt, "ii", $row['product_id'], $row['quantity']);
            mysqli_stmt_execute($stmt);
        }

        // Delete sale details
        mysqli_query($conn, "DELETE FROM sale_details WHERE sale_id = $sale_id");
        
        // Delete the sale
        mysqli_query($conn, "DELETE FROM sales WHERE sale_id = $sale_id");

        mysqli_commit($conn);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}