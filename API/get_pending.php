<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

$sql = "SELECT id, customer_name, pickup_location, dropoff_location, vehicle_type, passengers 
        FROM trips_pending 
        WHERE 1=1";
$params = [];

if (!empty($_GET['date'])) {
    $sql .= " AND DATE(created_at) = ?";
    $params[] = $_GET['date'];
}
if (!empty($_GET['pickup'])) {
    $sql .= " AND pickup_location LIKE ?";
    $params[] = '%' . $_GET['pickup'] . '%';
}
if (!empty($_GET['dropoff'])) {
    $sql .= " AND dropoff_location LIKE ?";
    $params[] = '%' . $_GET['dropoff'] . '%';
}

$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>