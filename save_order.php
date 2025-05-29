<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Enable detailed error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log all received POST data
error_log("Received POST data: " . print_r($_POST, true));

require_once 'db_connection.php';

// Verify database connection
try {
    $pdo->query("SELECT 1");
} catch (PDOException $e) {
    die(json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'details' => $e->getMessage()
    ]));
}

// Get form data with validation
$customerName = trim($_POST['customerName'] ?? '');
$contactNumber = trim($_POST['contactNumber'] ?? '');
$deliveryType = $_POST['deliveryType'] ?? 'Pickup'; // Fixed typo from previous version
$location = trim($_POST['location'] ?? '');
$orderWeight = (float)($_POST['orderWeight'] ?? 0);
$totalAmount = (float)str_replace(['P', 'PHP', ' ', ','], '', $_POST['totalAmount'] ?? '0');
$addons = json_decode($_POST['addons'] ?? '[]', true);
$latitude = isset($_POST['latitude']) ? (float)$_POST['latitude'] : null;
$longitude = isset($_POST['longitude']) ? (float)$_POST['longitude'] : null;

// Validate required fields
if (empty($customerName)) {
    die(json_encode(['success' => false, 'error' => 'Customer name is required']));
}
if (empty($contactNumber)) {
    die(json_encode(['success' => false, 'error' => 'Contact number is required']));
}

// Log before transaction
error_log("Starting transaction for order: $customerName");

try {
    $pdo->beginTransaction();
    
    // 1. Handle customer (insert or update)
    $stmt = $pdo->prepare("INSERT INTO customers (name, contact_number) VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE name = VALUES(name)");
    $stmt->execute([$customerName, $contactNumber]);
    $customerId = $pdo->lastInsertId() ?: $pdo->query("SELECT customer_id FROM customers WHERE contact_number = '$contactNumber'")->fetchColumn();
    
    // 2. Calculate financials
    $deliveryFee = ($deliveryType === 'Delivery') ? 50.00 : 0.00;
    $subtotal = $totalAmount - $deliveryFee;
    
    // 3. Generate order reference
    $orderRef = '#' . str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // 4. Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (
        customer_id, order_date, order_reference, order_weight, 
        delivery_type, delivery_location, subtotal, 
        delivery_fee, total_amount, payment_status, 
        order_status, retrieval_status, latitude, longitude
    ) VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, 'Not yet Paid', 'Not Started', ?, ?, ?)");
    
    $retrievalStatus = ($deliveryType === 'Delivery') ? 'To be Delivered' : 'To be Claimed';
    
    $stmt->execute([
        $customerId, $orderRef, $orderWeight, $deliveryType,
        $deliveryType === 'Delivery' ? $location : null,
        $subtotal, $deliveryFee, $totalAmount, $retrievalStatus,
        $latitude, $longitude
    ]);
    
    $orderId = $pdo->lastInsertId();
    error_log("Inserted order ID: $orderId");
    
    // 5. Insert addons
    if (is_array($addons)) {
        foreach ($addons as $addon) {
            if ($addon['quantity'] > 0) {
                $stmt = $pdo->prepare("INSERT INTO order_addons (order_id, addon_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$orderId, $addon['id'], $addon['quantity']]);
                error_log("Added addon: {$addon['id']} x {$addon['quantity']}");
            }
        }
    }
    
    $pdo->commit();
    
    // Verify the order was actually inserted
    $verifyStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE order_id = ?");
    $verifyStmt->execute([$orderId]);
    $exists = $verifyStmt->fetchColumn();
    
    if (!$exists) {
        throw new Exception("Order verification failed - order not found after insertion");
    }
    
    echo json_encode([
        'success' => true,
        'orderId' => $orderRef,
        'databaseId' => $orderId,
        'message' => 'Order created and verified successfully'
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Database Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database operation failed',
        'details' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("General Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Order processing failed',
        'details' => $e->getMessage()
    ]);
}
?>