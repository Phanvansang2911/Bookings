<?php
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

$type = $_GET['type'] ?? 'month';
$year = date('Y');
$month = date('m');
$quarter = ceil($month / 3);
$driver_id = 1;

$sql = "";
$params = [$driver_id];
$labels = [];

if ($type === 'month') {
    $sql = "SELECT CEIL(DAY(accept_date)/7) AS week, COUNT(*) AS trips
            FROM trips_accepted 
            WHERE driver_id = ? AND YEAR(accept_date) = ? AND MONTH(accept_date) = ?
            GROUP BY CEIL(DAY(accept_date)/7)";
    $params[] = $year; $params[] = $month;
    $labels = ["Tuần 1", "Tuần 2", "Tuần 3", "Tuần 4", "Tuần 5"];
} elseif ($type === 'quarter') {
    $sql = "SELECT MONTH(accept_date) AS m, COUNT(*) AS trips
            FROM trips_accepted 
            WHERE driver_id = ? AND YEAR(accept_date) = ? AND QUARTER(accept_date) = ?
            GROUP BY MONTH(accept_date)";
    $params[] = $year; $params[] = $quarter;
    $start = ($quarter - 1) * 3 + 1;
    $labels = ["Tháng $start", "Tháng " . ($start+1), "Tháng " . ($start+2)];
} else {
    $sql = "SELECT MONTH(accept_date) AS m, COUNT(*) AS trips
            FROM trips_accepted WHERE driver_id = ? AND YEAR(accept_date) = ?
            GROUP BY MONTH(accept_date)";
    $params[] = $year;
    $labels = array_map(fn($i) => "Tháng $i", range(1,12));
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$result = [];
foreach ($labels as $i => $label) {
    $key = $type === 'month' ? ($i + 1) : ($i + 1);
    $trips = 0;
    foreach ($rows as $row) {
        $rowKey = $type === 'month' ? $row['week'] : $row['m'];
        if ($rowKey == $key) {
            $trips = (int)$row['trips'];
            break;
        }
    }
    $result[] = ['label' => $label, 'trips' => $trips];
}

echo json_encode($result, JSON_UNESCAPED_UNICODE);
?>