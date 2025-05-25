<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'] ?? null;

if (!$orderId) {
    echo json_encode(['success' => false, 'error' => 'Order ID required']);
    exit;
}

try {
    // In a real system, you might have a separate history table
    // For now, we'll just flag it as completed in the same table
    $stmt = $pdo->prepare("UPDATE orders SET is_completed = 1 WHERE order_id = ?");
    $stmt->execute([$orderId]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>