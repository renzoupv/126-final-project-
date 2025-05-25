<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

try {
    $stmt = $pdo->query("
        SELECT 
            o.order_id,
            o.order_reference,
            DATE_FORMAT(o.order_date, '%Y-%m-%d') AS order_date,
            c.name,
            o.payment_status,
            o.order_status,
            o.retrieval_status,
            o.total_amount,
            o.is_completed
        FROM orders o
        JOIN customers c ON o.customer_id = c.customer_id
        WHERE o.is_completed = 0
        ORDER BY o.order_date DESC
    ");
    
    echo json_encode([
        'success' => true,
        'orders' => $stmt->fetchAll(PDO::FETCH_ASSOC)
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>