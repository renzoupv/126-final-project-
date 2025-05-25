<?php
header('Content-Type: application/json');
require 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $pdo->beginTransaction();

    // 1. First delete from order_addons
    $stmt = $pdo->prepare("DELETE FROM order_addons WHERE order_id = ?");
    $stmt->execute([$data['order_id']]);

    // 2. Then delete from orders (works for both active and historical orders)
    $stmt = $pdo->prepare("DELETE FROM orders WHERE order_id = ?");
    $stmt->execute([$data['order_id']]);

    $pdo->commit();
    
    echo json_encode(['success' => true]);
    
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'error_code' => $e->getCode()
    ]);
}
?>