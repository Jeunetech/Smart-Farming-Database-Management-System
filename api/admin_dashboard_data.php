<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

// Ensure the user has admin privileges
requireRole('dba');

header('Content-Type: application/json; charset=utf-8');
$pdo = getDB();

try {
    $response = [];

    // 1. Dashboard Stats
    $stats = $pdo->query("SELECT * FROM v_dashboard_stats")->fetch(PDO::FETCH_ASSOC);
    $response['stats'] = [
        'userCount' => $stats['user_count'] ?? 0,
        'fieldCount' => $stats['field_count'] ?? 0,
        'sensorCount' => $stats['sensor_count'] ?? 0,
        'dataCount' => $stats['data_count'] ?? 0
    ];

    // 2. Users Table (with pagination)
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 5;
    $offset = ($page - 1) * $limit;

    $users = $pdo->query("SELECT user_id, name, email, permissions_level FROM `user` ORDER BY user_id LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);
    $allRoles = getAllUserRoles($pdo);
    foreach ($users as $k => $v) { 
        $users[$k]['role'] = $allRoles[$v['user_id']] ?? 'unknown'; 
    }

    $totalUsers = $pdo->query("SELECT COUNT(*) FROM `user`")->fetchColumn();
    $totalPages = ceil($totalUsers / $limit);

    $response['users'] = [
        'data' => $users,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'limit' => $limit,
            'total_users' => $totalUsers
        ]
    ];

    // 3. Chart Data
    // We only fetch chart data if it's not a pagination request (optimization)
    if (!isset($_GET['page']) || $_GET['page'] == 1 || isset($_GET['full_data'])) {
        $soilData = $pdo->query("SELECT d.*, sd.ph_level, sd.moisture, sd.nutrient_levels, sd.sample_date, f.location as field_location FROM data_table d INNER JOIN soil_data sd ON d.data_id = sd.data_id JOIN field f ON d.field_id = f.field_id ORDER BY d.`timestamp` DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
        $weatherData = $pdo->query("SELECT d.*, wd.temperature, wd.humidity, wd.rainfall, wd.wind_speed, f.location as field_location FROM data_table d INNER JOIN weather_data wd ON d.data_id = wd.data_id JOIN field f ON d.field_id = f.field_id ORDER BY d.`timestamp` DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
        
        $dataTypeCounts = [
            'Soil' => (int)$pdo->query("SELECT COUNT(*) FROM soil_data")->fetchColumn(),
            'Weather' => (int)$pdo->query("SELECT COUNT(*) FROM weather_data")->fetchColumn(),
            'Irrigation' => (int)$pdo->query("SELECT COUNT(*) FROM irrigation_data")->fetchColumn(),
            'Equipment' => (int)$pdo->query("SELECT COUNT(*) FROM equipment_data")->fetchColumn()
        ];
        
        $sensorStatusRows = $pdo->query("SELECT status, COUNT(*) as count FROM sensor GROUP BY status")->fetchAll(PDO::FETCH_ASSOC);
        $sensorStatusCounts = [];
        foreach ($sensorStatusRows as $row) {
            $sensorStatusCounts[ucfirst($row['status'])] = (int)$row['count'];
        }

        $response['charts'] = [
            'soilData' => $soilData,
            'weatherData' => $weatherData,
            'dataType' => [
                'labels' => array_keys($dataTypeCounts),
                'values' => array_values($dataTypeCounts)
            ],
            'sensorStatus' => [
                'labels' => array_keys($sensorStatusCounts),
                'values' => array_values($sensorStatusCounts)
            ]
        ];
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server Error: ' . $e->getMessage()]);
}
