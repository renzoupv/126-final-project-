<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

try {
    // First get all completed orders info
    $stmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.order_reference,
            DATE_FORMAT(o.order_date, '%Y-%m-%d') AS order_date,
            c.name AS customer_name,
            o.payment_status,
            o.order_status,
            o.retrieval_status,
            o.total_amount,
            o.is_completed,
            o.delivery_type,
            o.order_weight,
            o.delivery_fee,
            o.delivery_location 
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.is_completed = 1
        ORDER BY o.completed_at DESC
    ");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Prepare a statement to get addons per order
    $stmtAddons = $pdo->prepare("
        SELECT 
            oa.order_id,
            a.addon_id,
            a.name AS addon_name,
            a.price,
            oa.quantity
        FROM order_addons oa
        JOIN addons a ON oa.addon_id = a.addon_id
        WHERE oa.order_id = ?
    ");

    // For each order, get its addons and attach them
    foreach ($orders as &$order) {
        $stmtAddons->execute([$order['order_id']]);
        $order['addons'] = $stmtAddons->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($order); // break reference

    echo json_encode([
        'success' => true,
        'orders' => $orders
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
