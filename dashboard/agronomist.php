<?php

$pageTitle = 'Agronomist Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole('agronomist', 'dba');
$pdo = getDB();
$userId = $_SESSION['user_id'];

$analysisCount = $pdo->prepare("SELECT COUNT(*) FROM analyzes WHERE agronomist_id = ?"); $analysisCount->execute([$userId]);
$totalAnalyses = $analysisCount->fetchColumn();

$dataCount = $pdo->query("SELECT COUNT(*) FROM data_table")->fetchColumn();
$fieldCount = $pdo->query("SELECT COUNT(*) FROM field")->fetchColumn();
$soilCount = $pdo->query("SELECT COUNT(*) FROM soil_data")->fetchColumn();

$analyses = $pdo->prepare("SELECT a.*, d.value, d.unit, d.`timestamp` as data_time, f.location as field_location FROM analyzes a JOIN data_table d ON a.data_id = d.data_id JOIN field f ON d.field_id = f.field_id WHERE a.agronomist_id = ? ORDER BY a.analyzed_at DESC LIMIT 10");
$analyses->execute([$userId]);
$recentAnalyses = $analyses->fetchAll();

$unanalyzed = $pdo->query("SELECT d.*, f.location as field_location, s.type as sensor_type FROM data_table d JOIN field f ON d.field_id = f.field_id LEFT JOIN sensor s ON d.sensor_id = s.sensor_id WHERE d.data_id NOT IN (SELECT data_id FROM analyzes) ORDER BY d.`timestamp` DESC LIMIT 10")->fetchAll();

$soilData = $pdo->query("SELECT d.*, sd.ph_level, sd.moisture, sd.nutrient_levels, sd.sample_date, f.location as field_location FROM data_table d INNER JOIN soil_data sd ON d.data_id = sd.data_id JOIN field f ON d.field_id = f.field_id ORDER BY d.`timestamp` DESC LIMIT 50")->fetchAll();
$weatherData = $pdo->query("SELECT d.*, wd.temperature, wd.humidity, wd.rainfall, wd.wind_speed, f.location as field_location FROM data_table d INNER JOIN weather_data wd ON d.data_id = wd.data_id JOIN field f ON d.field_id = f.field_id ORDER BY d.`timestamp` DESC LIMIT 50")->fetchAll();

$dataTypeCounts = [
    'Soil' => (int)$pdo->query("SELECT COUNT(*) FROM soil_data")->fetchColumn(),
    'Weather' => (int)$pdo->query("SELECT COUNT(*) FROM weather_data")->fetchColumn(),
    'Irrigation' => (int)$pdo->query("SELECT COUNT(*) FROM irrigation_data")->fetchColumn(),
    'Equipment' => (int)$pdo->query("SELECT COUNT(*) FROM equipment_data")->fetchColumn()
];

$sensorStatusRows = $pdo->query("SELECT status, COUNT(*) as count FROM sensor GROUP BY status")->fetchAll();
$sensorStatusCounts = [];
foreach ($sensorStatusRows as $row) { $sensorStatusCounts[ucfirst($row['status'])] = (int)$row['count']; }
?>

<div class="stats-grid">
    <div class="stat-card cyan">
        <div class="stat-icon cyan"><i class="fas fa-flask"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $totalAnalyses ?></span><span class="stat-label">Analyses Performed</span></div>
    </div>
    <div class="stat-card violet">
        <div class="stat-icon violet"><i class="fas fa-map-marked-alt"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $fieldCount ?></span><span class="stat-label">Fields Monitored</span></div>
    </div>
    <div class="stat-card emerald">
        <div class="stat-icon emerald"><i class="fas fa-database"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $dataCount ?></span><span class="stat-label">Data Points</span></div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-vial"></i></div>
        <div class="stat-info"><span class="stat-value"><?= $soilCount ?></span><span class="stat-label">Soil Samples</span></div>
    </div>
</div>

<div class="grid-2 mb-24">
    <div class="card">
        <div class="card-header"><h3>Soil Analysis Trends</h3></div>
        <div class="chart-container"><canvas id="soil-chart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Weather Trends</h3></div>
        <div class="chart-container"><canvas id="weather-chart"></canvas></div>
    </div>
</div>

<div class="grid-2 mb-24">
    <div class="card"><div class="card-header"><h3>Data Records by Type</h3></div><div class="chart-container" style="max-height: 220px;"><canvas id="data-type-chart"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Sensor Status Distribution</h3></div><div class="chart-container" style="max-height: 220px;"><canvas id="sensor-status-chart"></canvas></div></div>
</div>

<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>Analysis Queue</h3><span class="badge badge-maintenance"><?= count($unanalyzed) ?> pending</span></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Sensor</th><th>Value</th><th>Field</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach ($unanalyzed as $d): ?>
                    <tr>
                        <td><span class="badge badge-info"><?= htmlspecialchars($d['sensor_type'] ?? 'N/A') ?></span></td>
                        <td style="color:var(--text-primary);font-weight:500"><?= $d['value'] ?> <?= htmlspecialchars($d['unit']) ?></td>
                        <td><?= htmlspecialchars($d['field_location']) ?></td>
                        <td><button class="btn btn-sm btn-primary" onclick="analyzeData(<?= $d['data_id'] ?>)"><i class="fas fa-check"></i> Analyze</button></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($unanalyzed)): ?><tr><td colspan="4" class="text-center" style="color:var(--text-muted);padding:24px">All data analyzed!</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Recent Analyses</h3></div>
        <div class="table-wrapper">
            <table>
                <thead><tr><th>Data</th><th>Field</th><th>Analyzed At</th></tr></thead>
                <tbody>
                <?php foreach ($recentAnalyses as $a): ?>
                    <tr>
                        <td style="color:var(--text-primary);font-weight:500"><?= $a['value'] ?> <?= htmlspecialchars($a['unit']) ?></td>
                        <td><?= htmlspecialchars($a['field_location']) ?></td>
                        <td style="font-size:12px"><?= $a['analyzed_at'] ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recentAnalyses)): ?><tr><td colspan="3" class="text-center" style="color:var(--text-muted);padding:24px">No analyses yet</td></tr><?php endif; ?>
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
async function analyzeData(dataId) {
    try {
        await App.api('analyzes.php', { method: 'POST', body: { agronomist_id: <?= $userId ?>, data_id: dataId } });
        Toast.success('Data analyzed successfully');
        setTimeout(() => location.reload(), 1000);
    } catch (e) {}
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
