<?php require '../config.php';
header('Content-Type: application/json; charset=utf-8');

$pickup = $_GET['pickup'] ?? '';
$dropoff = $_GET['dropoff'] ?? '';

$sql = "SELECT id, customer_name, pickup_location, dropoff_location, vehicle_type, passengers FROM trips_pending WHERE 1=1";
$params = [];

if ($pickup !== '') { $sql .= " AND pickup_location = ?"; $params[] = $pickup; }
if ($dropoff !== '') { $sql .= " AND dropoff_location = ?"; $params[] = $dropoff; }

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE);
?>