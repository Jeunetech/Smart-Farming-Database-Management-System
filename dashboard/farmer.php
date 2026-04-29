<?php

$pageTitle = 'Farmer Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole('farmer', 'dba');
$pdo = getDB();
$userId = $_SESSION['user_id'];

$fieldsStmt = $pdo->prepare("SELECT COUNT(*) FROM field WHERE farmer_id = ?"); $fieldsStmt->execute([$userId]);
$fieldCount = $fieldsStmt->fetchColumn();

$sensorStmt = $pdo->prepare("SELECT COUNT(*) FROM sensor s JOIN field f ON s.field_id = f.field_id WHERE f.farmer_id = ?"); $sensorStmt->execute([$userId]);
$sensorCount = $sensorStmt->fetchColumn();

$activeStmt = $pdo->prepare("SELECT COUNT(*) FROM sensor s JOIN field f ON s.field_id = f.field_id WHERE f.farmer_id = ? AND s.status = 'active'"); $activeStmt->execute([$userId]);
$activeCount = $activeStmt->fetchColumn();

$cropStmt = $pdo->prepare("SELECT COUNT(*) FROM crop c JOIN field f ON c.field_id = f.field_id WHERE f.farmer_id = ?"); $cropStmt->execute([$userId]);
$cropCount = $cropStmt->fetchColumn();

$fields = $pdo->prepare("SELECT * FROM field WHERE farmer_id = ? ORDER BY field_id"); $fields->execute([$userId]);
$myFields = $fields->fetchAll();

$dataStmt = $pdo->prepare("SELECT d.*, f.location as field_location, s.type as sensor_type FROM data_table d JOIN field f ON d.field_id = f.field_id LEFT JOIN sensor s ON d.sensor_id = s.sensor_id WHERE f.farmer_id = ? ORDER BY d.`timestamp` DESC LIMIT 10");
$dataStmt->execute([$userId]);
$recentData = $dataStmt->fetchAll();

$cropsStmt = $pdo->prepare("SELECT c.*, f.location as field_location FROM crop c JOIN field f ON c.field_id = f.field_id WHERE f.farmer_id = ? ORDER BY c.planting_date DESC");
$cropsStmt->execute([$userId]);
$crops = $cropsStmt->fetchAll();

$soilDataStmt = $pdo->prepare("SELECT d.*, sd.ph_level, sd.moisture, sd.nutrient_levels, sd.sample_date, f.location as field_location FROM data_table d INNER JOIN soil_data sd ON d.data_id = sd.data_id JOIN field f ON d.field_id = f.field_id WHERE f.farmer_id = ? ORDER BY d.`timestamp` DESC LIMIT 50");
$soilDataStmt->execute([$userId]);
$soilData = $soilDataStmt->fetchAll();

$weatherDataStmt = $pdo->prepare("SELECT d.*, wd.temperature, wd.humidity, wd.rainfall, wd.wind_speed, f.location as field_location FROM data_table d INNER JOIN weather_data wd ON d.data_id = wd.data_id JOIN field f ON d.field_id = f.field_id WHERE f.farmer_id = ? ORDER BY d.`timestamp` DESC LIMIT 50");
$weatherDataStmt->execute([$userId]);
$weatherData = $weatherDataStmt->fetchAll();

$dtStmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM soil_data sd JOIN data_table d ON sd.data_id = d.data_id JOIN field f ON d.field_id = f.field_id WHERE f.farmer_id = ?) as Soil,
        (SELECT COUNT(*) FROM weather_data wd JOIN data_table d ON wd.data_id = d.data_id JOIN field f ON d.field_id = f.field_id WHERE f.farmer_id = ?) as Weather,
        (SELECT COUNT(*) FROM irrigation_data id JOIN data_table d ON id.data_id = d.data_id JOIN field f ON d.field_id = f.field_id WHERE f.farmer_id = ?) as Irrigation,
        (SELECT COUNT(*) FROM equipment_data ed JOIN data_table d ON ed.data_id = d.data_id JOIN field f ON d.field_id = f.field_id WHERE f.farmer_id = ?) as Equipment
");
$dtStmt->execute([$userId, $userId, $userId, $userId]);
$dataTypeCounts = $dtStmt->fetch(PDO::FETCH_ASSOC);

$statusStmt = $pdo->prepare("SELECT s.status, COUNT(*) as count FROM sensor s JOIN field f ON s.field_id = f.field_id WHERE f.farmer_id = ? GROUP BY s.status");
$statusStmt->execute([$userId]);
$sensorStatusRows = $statusStmt->fetchAll();
$sensorStatusCounts = [];
foreach ($sensorStatusRows as $row) { $sensorStatusCounts[ucfirst($row['status'])] = (int)$row['count']; }
?>

<div class="stats-grid">
    <div class="stat-card cyan">
        <div class="stat-icon cyan"><i class="fas fa-map-marked-alt"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $fieldCount ?></span><span class="stat-label">Total Fields</span></div>
    </div>
    <div class="stat-card violet">
        <div class="stat-icon violet"><i class="fas fa-microchip"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $sensorCount ?></span><span class="stat-label">Total Sensors</span></div>
    </div>
    <div class="stat-card emerald">
        <div class="stat-icon emerald"><i class="fas fa-signal"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $activeCount ?></span><span class="stat-label">Active Sensors</span></div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-leaf"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $cropCount ?></span><span class="stat-label">Total Crops</span></div>
    </div>
</div>

<div class="grid-2 mb-24">
    <div class="card">
        <div class="card-header"><h3>Soil Analysis</h3></div>
        <div class="chart-container"><canvas id="soil-chart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Weather Overview</h3></div>
        <div class="chart-container"><canvas id="weather-chart"></canvas></div>
    </div>
</div>

<div class="grid-2 mb-24">
    <div class="card"><div class="card-header"><h3>Data Records by Type</h3></div><div class="chart-container" style="max-height: 220px;"><canvas id="data-type-chart"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Sensor Status Distribution</h3></div><div class="chart-container" style="max-height: 220px;"><canvas id="sensor-status-chart"></canvas></div></div>
</div>

<div class="card mb-24">
    <div class="card-header">
        <h3>My Fields</h3>
        <a href="/pages/fields.php" class="btn btn-sm btn-secondary"><i class="fas fa-external-link-alt"></i> Manage</a>
    </div>
    <div class="table-wrapper">
        <table>
            <thead><tr><th>Location</th><th>Size (ha)</th><th>Irrigation</th><th>Sensors</th></tr></thead>
            <tbody>
            <?php foreach ($myFields as $f):
                $sc = $pdo->prepare("SELECT COUNT(*) FROM sensor WHERE field_id = ?"); $sc->execute([$f['field_id']]);
            ?>
                <tr>
                    <td style="color:var(--text-primary);font-weight:500"><?= htmlspecialchars($f['location']) ?></td>
                    <td><?= $f['size'] ?></td>
                    <td><span class="badge badge-info"><?= htmlspecialchars($f['irrigation_type']) ?></span></td>
                    <td><?= $sc->fetchColumn() ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($myFields)): ?><tr><td colspan="4" class="text-center" style="color:var(--text-muted);padding:24px">No fields found</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>Crops</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Crop</th><th>Field</th><th>Planted</th><th>Yield</th></tr></thead>
                <tbody>
                <?php foreach ($crops as $c): ?>
                    <tr>
                        <td style="color:var(--text-primary);font-weight:500"><?= htmlspecialchars($c['name']) ?></td>
                        <td><?= htmlspecialchars($c['field_location']) ?></td>
                        <td><?= $c['planting_date'] ?></td>
                        <td><?= $c['yield_value'] ? $c['yield_value'] . ' ' . $c['yield_unit'] : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($crops)): ?><tr><td colspan="4" class="text-center" style="color:var(--text-muted);padding:24px">No crops</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Recent Sensor Data</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Sensor</th><th>Value</th><th>Field</th><th>Time</th></tr></thead>
                <tbody>
                <?php foreach ($recentData as $d): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= htmlspecialchars($d['sensor_type'] ?? 'N/A') ?></span></td>
                        <td style="color:var(--text-primary);font-weight:500"><?= $d['value'] ?> <?= htmlspecialchars($d['unit']) ?></td>
                        <td><?= htmlspecialchars($d['field_location']) ?></td>
                        <td style="font-size:12px"><?= $d['timestamp'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentData)): ?><tr><td colspan="4" class="text-center" style="color:var(--text-muted);padding:24px">No data</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const inlineSoilData = <?=json_encode($soilData)?>;
const inlineWeatherData = <?=json_encode($weatherData)?>;
const dataTypeLabels = <?=json_encode(array_keys($dataTypeCounts))?>;
const dataTypeValues = <?=json_encode(array_values($dataTypeCounts))?>;
const sensorStatusLabels = <?=json_encode(array_keys($sensorStatusCounts))?>;
const sensorStatusValues = <?=json_encode(array_values($sensorStatusCounts))?>;

document.addEventListener('DOMContentLoaded', () => {
    FarmCharts.renderSoilChart('soil-chart', null, inlineSoilData);
    FarmCharts.renderWeatherChart('weather-chart', null, inlineWeatherData);
    
    FarmCharts.renderPieChart('data-type-chart', dataTypeLabels, dataTypeValues, {
        'Soil': CHART_COLORS.amber,
        'Weather': CHART_COLORS.blue,
        'Irrigation': CHART_COLORS.cyan,
        'Equipment': CHART_COLORS.violet
    }, false);
    
    FarmCharts.renderPieChart('sensor-status-chart', sensorStatusLabels, sensorStatusValues, {
        'Active': CHART_COLORS.emerald,
        'Maintenance': CHART_COLORS.amber,
        'Inactive': CHART_COLORS.red,
        'Broken': CHART_COLORS.red
    }, true);
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
