<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

try {
    // Get all active orders with customer info
    $stmt = $pdo->query("
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
            o.delivery_fee,
            o.order_weight
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.is_completed = 0
        ORDER BY o.order_date DESC
    ");

    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get add-ons per order
    $orderIds = array_column($orders, 'order_id');

    if (!empty($orderIds)) {
        $inQuery = implode(',', array_fill(0, count($orderIds), '?'));

        $addonStmt = $pdo->prepare("
            SELECT 
                oa.order_id,
                a.name AS addon_name,
                a.price,
                oa.quantity
            FROM order_addons oa
            JOIN addons a ON oa.addon_id = a.addon_id
            WHERE oa.order_id IN ($inQuery)
        ");
        $addonStmt->execute($orderIds);
        $addonRows = $addonStmt->fetchAll(PDO::FETCH_ASSOC);

        // Group addons by order_id
        $addonsByOrder = [];
        foreach ($addonRows as $addon) {
            $addonsByOrder[$addon['order_id']][] = $addon;
        }

        // Attach addons to each order
        foreach ($orders as &$order) {
            $order['addons'] = $addonsByOrder[$order['order_id']] ?? [];
        }
    }

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