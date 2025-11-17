<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['pending_id'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
    exit;
}

$id = (int)$_POST['pending_id'];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT * FROM trips_pending WHERE id = ?");
    $stmt->execute([$id]);
    $trip = $stmt->fetch();

    if (!$trip) throw new Exception("Không tìm thấy chuyến đi");

    // Tạo mã đơn duy nhất
    do {
        $code = strtoupper(substr($trip['pickup_location'],0,2) . substr($trip['dropoff_location'],0,3) . '-' . rand(1000,9999));
        $check = $pdo->prepare("SELECT 1 FROM trips_accepted WHERE order_code = ?");
        $check->execute([$code]);
    } while ($check->fetch());

    $pdo->prepare("INSERT INTO trips_accepted 
        (order_code, customer_name, pickup_location, dropoff_location, vehicle_type, passengers, accept_date, status, revenue, driver_id)
        VALUES (?, ?, ?, ?, ?, ?, CURDATE(), 'Đã nhận', 2000000, 1)")
        ->execute([$code, $trip['customer_name'], $trip['pickup_location'], $trip['dropoff_location'], $trip['vehicle_type'], $trip['passengers']]);

    $pdo->prepare("DELETE FROM trips_pending WHERE id = ?")->execute([$id]);
    $pdo->commit();

    echo json_encode(['success' => true, 'message' => "Nhận đơn thành công! Mã đơn: $code"]);
} catch (Exception $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>