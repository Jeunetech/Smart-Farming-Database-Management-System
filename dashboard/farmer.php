<?php
/**
 * Farmer Dashboard
 */
$pageTitle = 'Farmer Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole('farmer', 'dba');
$pdo = getDB();
$userId = $_SESSION['user_id'];

// Stats
$fieldsStmt = $pdo->prepare("SELECT COUNT(*) FROM field WHERE farmer_id = ?"); $fieldsStmt->execute([$userId]);
$fieldCount = $fieldsStmt->fetchColumn();

$sensorStmt = $pdo->prepare("SELECT COUNT(*) FROM sensor s JOIN field f ON s.field_id = f.field_id WHERE f.farmer_id = ?"); $sensorStmt->execute([$userId]);
$sensorCount = $sensorStmt->fetchColumn();

$activeStmt = $pdo->prepare("SELECT COUNT(*) FROM sensor s JOIN field f ON s.field_id = f.field_id WHERE f.farmer_id = ? AND s.status = 'active'"); $activeStmt->execute([$userId]);
$activeCount = $activeStmt->fetchColumn();

$cropStmt = $pdo->prepare("SELECT COUNT(*) FROM crop c JOIN field f ON c.field_id = f.field_id WHERE f.farmer_id = ?"); $cropStmt->execute([$userId]);
$cropCount = $cropStmt->fetchColumn();

// Fields
$fields = $pdo->prepare("SELECT * FROM field WHERE farmer_id = ? ORDER BY field_id"); $fields->execute([$userId]);
$myFields = $fields->fetchAll();

// Recent Data
$dataStmt = $pdo->prepare("SELECT d.*, f.location as field_location, s.type as sensor_type FROM data_table d JOIN field f ON d.field_id = f.field_id LEFT JOIN sensor s ON d.sensor_id = s.sensor_id WHERE f.farmer_id = ? ORDER BY d.`timestamp` DESC LIMIT 10");
$dataStmt->execute([$userId]);
$recentData = $dataStmt->fetchAll();

// Crops
$cropsStmt = $pdo->prepare("SELECT c.*, f.location as field_location FROM crop c JOIN field f ON c.field_id = f.field_id WHERE f.farmer_id = ? ORDER BY c.planting_date DESC");
$cropsStmt->execute([$userId]);
$crops = $cropsStmt->fetchAll();
?>

<!-- Stats -->
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

<!-- Charts Row -->
<div class="grid-2">
    <div class="card">
        <div class="card-header"><h3>Soil Analysis</h3></div>
        <div class="chart-container"><canvas id="soil-chart"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><h3>Weather Overview</h3></div>
        <div class="chart-container"><canvas id="weather-chart"></canvas></div>
    </div>
</div>

<!-- Fields Table -->
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

<!-- Crops & Recent Data -->
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
document.addEventListener('DOMContentLoaded', () => {
    FarmCharts.renderSoilChart('soil-chart');
    FarmCharts.renderWeatherChart('weather-chart');
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
