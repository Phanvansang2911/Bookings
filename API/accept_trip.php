<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Phương thức không đúng']);
    exit;
}

$id = $_POST['pending_id'] ?? 0;
if (!is_numeric($id) || $id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM trips_pending WHERE id = ?");
$stmt->execute([$id]);
$trip = $stmt->fetch();

if (!$trip) {
    echo json_encode(['success' => false, 'message' => 'Chuyến đã được nhận hoặc không tồn tại']);
    exit;
}

try {
    $pdo->beginTransaction();

    $code = strtoupper(substr($trip['pickup_location'], 0, 3)) . 
            substr($trip['dropoff_location'], 0, 3) . 
            '-' . rand(100, 999);

    $insert = $pdo->prepare("INSERT INTO trips_accepted 
        (order_code, customer_name, pickup_location, dropoff_location, vehicle_type, passengers, accept_date, revenue, driver_id)
        VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 1500000, 1)");
    $insert->execute([
        $code,
        $trip['customer_name'],
        $trip['pickup_location'],
        $trip['dropoff_location'],
        $trip['vehicle_type'],
        $trip['passengers']
    ]);

    $pdo->prepare("DELETE FROM trips_pending WHERE id = ?")->execute([$id]);
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Nhận đơn thành công! Mã đơn: ' . $code]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống, vui lòng thử lại']);
}
?>