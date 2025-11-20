<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

$driver_id = 1; // Sau này lấy từ session

$stmt = $pdo->prepare("SELECT * FROM trips_accepted WHERE driver_id = ? ORDER BY accept_date DESC");
$stmt->execute([$driver_id]);
$data = $stmt->fetchAll();

echo json_encode($data, JSON_UNESCAPED_UNICODE);
?>