<?php
header('Content-Type: application/json');
require 'db_connection.php';

try {
    // First get all orders
    $stmt = $pdo->query("
        SELECT 
            o.order_id,
            o.order_reference,
            DATE_FORMAT(o.order_date, '%m/%d/%Y') as date,
            c.name as customer,
            o.order_weight as weight,
            o.subtotal as amount,
            o.delivery_fee,
            o.total_amount as total
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.is_completed = 1
        ORDER BY o.order_date DESC
    ");
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Then get addons for each order
    foreach ($orders as &$order) {
        $stmt = $pdo->prepare("
            SELECT 
                a.name,
                a.price,
                oa.quantity
            FROM order_addons oa
            JOIN addons a ON oa.addon_id = a.addon_id
            WHERE oa.order_id = ?
        ");
        $stmt->execute([$order['order_id']]);
        $order['addons'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Format numeric values
    foreach ($orders as &$order) {
        $order['amount'] = (float)$order['amount'];
        $order['total'] = (float)$order['total'];
        $order['delivery_fee'] = $order['delivery_fee'] ? (float)$order['delivery_fee'] : null;
    }
    
    echo json_encode(['success' => true, 'sales' => $orders]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>