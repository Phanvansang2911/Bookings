<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? 0;
if (!$id || !is_numeric($id)) {
    echo json_encode(['error' => 'ID không hợp lệ']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM trips_pending WHERE id = ?");
$stmt->execute([$id]);
$trip = $stmt->fetch();

if (!$trip) {
    echo json_encode(['error' => 'Không tìm thấy chuyến']);
    exit;
}

echo json_encode([
    'customer_name' => $trip['customer_name'],
    'pickup' => $trip['pickup_location'],
    'dropoff' => $trip['dropoff_location'],
    'passengers' => $trip['passengers'],
    'vehicle_type' => $trip['vehicle_type'],
    'order_code' => 'DH-' . str_pad($trip['id'], 5, '0', STR_PAD_LEFT)
], JSON_UNESCAPED_UNICODE);
?>