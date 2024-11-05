<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_name = $_POST['customer_name'];
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'];
    $products = $_POST['products'];
    $quantities = $_POST['quantities'];

    try {
        mysqli_begin_transaction($conn);

        // Record the sale
        $stmt = mysqli_prepare($conn, "CALL record_sale(?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $customer_name, $payment_method, $notes);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $sale_row = mysqli_fetch_assoc($result);
        $sale_id = $sale_row['sale_id'];

        // Add sale details for each product
        for ($i = 0; $i < count($products); $i++) {
            $stmt = mysqli_prepare($conn, "CALL add_sale_detail(?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iii", $sale_id, $products[$i], $quantities[$i]);
            mysqli_stmt_execute($stmt);
        }

        mysqli_commit($conn);
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}