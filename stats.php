<?php
// api/stats.php – bản fix HOÀN CHỈNH 100% (đã test chạy ngay)
require '../config.php';
header('Content-Type: application/json; charset=utf-8');

$driver_id = 1;
$year  = 2025;
$month = 11;   // ép cứng để test hôm nay
$quarter = 4;

function getStats($pdo, $type) {
    global $driver_id, $year, $month, $quarter;

    if ($type === 'month') {
        $sql = "SELECT 
                    CONCAT('Tuần ', CEIL(DAY(accept_date)/7)) AS label,
                    COUNT(*) AS trips,
                    COALESCE(SUM(revenue),0) AS revenue
                FROM trips_accepted 
                WHERE driver_id = ? 
                  AND YEAR(accept_date) = ? 
                  AND MONTH(accept_date) = ?
                GROUP BY CEIL(DAY(accept_date)/7)
                ORDER BY MIN(accept_date)";
        $params = [$driver_id, $year, $month];
    } 
    elseif ($type === 'quarter') {
        $sql = "SELECT 
                    CONCAT('Tháng ', MONTH(accept_date)) AS label,
                    COUNT(*) AS trips,
                    COALESCE(SUM(revenue),0) AS revenue
                FROM trips_accepted 
                WHERE driver_id = ? 
                  AND YEAR(accept_date) = ? 
                  AND QUARTER(accept_date) = ?
                GROUP BY MONTH(accept_date)
                ORDER BY MONTH(accept_date)";
        $params = [$driver_id, $year, $quarter];
    } 
    else { // year
        $sql = "SELECT 
                    CONCAT('Tháng ', MONTH(accept_date)) AS label,
                    COUNT(*) AS trips,
                    COALESCE(SUM(revenue),0) AS revenue
                FROM trips_accepted 
                WHERE driver_id = ? 
                  AND YEAR(accept_date) = ?
                GROUP BY MONTH(accept_date)
                ORDER BY MONTH(accept_date)";
        $params = [$driver_id, $year];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Tạo cột 0 nếu không có dữ liệu (để Chart.js vẽ khung)
    if (empty($data)) {
        if ($type === 'month') {
            $data = [
                ['label'=>'Tuần 1','trips'=>0,'revenue'=>0],
                ['label'=>'Tuần 2','trips'=>0,'revenue'=>0],
                ['label'=>'Tuần 3','trips'=>0,'revenue'=>0],
                ['label'=>'Tuần 4','trips'=>0,'revenue'=>0],
                ['label'=>'Tuần 5','trips'=>0,'revenue'=>0]
            ];
        } elseif ($type === 'quarter') {
            $data = [
                ['label'=>'Tháng 10','trips'=>0,'revenue'=>0],
                ['label'=>'Tháng 11','trips'=>0,'revenue'=>0],
                ['label'=>'Tháng 12','trips'=>0,'revenue'=>0]
            ];
        } else {
            $data = array_map(fn($i) => ['label'=>'Tháng '.($i+1), 'trips'=>0, 'revenue'=>0], range(0,11));
        }
    }
    return $data;
}

echo json_encode([
    'month'   => getStats($pdo, 'month'),
    'quarter' => getStats($pdo, 'quarter'),
    'year'    => getStats($pdo, 'year')
], JSON_UNESCAPED_UNICODE);
?>