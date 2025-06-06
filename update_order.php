<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'] ?? null;
$paymentStatus = $data['payment_status'] ?? null;
$orderStatus = $data['order_status'] ?? null;
$retrievalStatus = $data['retrieval_status'] ?? null;
$revert = $data['revert'] ?? false;

if (!$orderId) {
    echo json_encode(['success' => false, 'error' => 'Order ID required']);
    exit;
}

try {
    $pdo->beginTransaction();

    if ($revert) {
        // Revert order to active - ignore status, clear completion
        $stmt = $pdo->prepare("
            UPDATE orders
            SET is_completed = 0,
                completed_at = NULL
            WHERE order_id = :order_id
        ");
        $stmt->execute([':order_id' => $orderId]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Order reverted to active state'
        ]);
        exit;
    }

    // Normal save/update behavior: update statuses and decide if completed
    $shouldBeCompleted = ($paymentStatus === 'Paid') && 
                         ($orderStatus === 'Done') && 
                         (in_array($retrievalStatus, ['Completed']));

    $stmt = $pdo->prepare("
        UPDATE orders 
        SET payment_status = :payment_status,
            order_status = :order_status,
            retrieval_status = :retrieval_status,
            is_completed = :is_completed,
            completed_at = CASE WHEN :is_completed = 1 THEN NOW() ELSE NULL END
        WHERE order_id = :order_id
    ");
    $stmt->execute([
        ':payment_status' => $paymentStatus,
        ':order_status' => $orderStatus,
        ':retrieval_status' => $retrievalStatus,
        ':is_completed' => $shouldBeCompleted ? 1 : 0,
        ':order_id' => $orderId
    ]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'isCompleted' => $shouldBeCompleted,
        'isActive' => !$shouldBeCompleted,
        'message' => $shouldBeCompleted ? 'Order marked as completed' : 'Status updated'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Database error occurred'
    ]);
}
?>
