<?php require '../config.php';
header('Content-Type: application/json; charset=utf-8');
$stmt = $pdo->query("SELECT order_code, customer_name, pickup_location, dropoff_location, vehicle_type, passengers, accept_date, status FROM trips_accepted WHERE driver_id = 1 ORDER BY accept_date DESC");
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
?>