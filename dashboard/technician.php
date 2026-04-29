<?php

$pageTitle = 'Technician Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole('technician', 'dba');
$pdo = getDB();
$userId = $_SESSION['user_id'];

$techStmt = $pdo->prepare("SELECT t.*, f.location as field_location FROM technician t JOIN field f ON t.field_id = f.field_id WHERE t.user_id = ?");
$techStmt->execute([$userId]);
$techInfo = $techStmt->fetch();
$fieldId = $techInfo['field_id'] ?? 0;

$sensors = $pdo->prepare("SELECT * FROM sensor WHERE field_id = ?"); $sensors->execute([$fieldId]);
$mySensors = $sensors->fetchAll();
$statusCounts = ['active'=>0,'maintenance'=>0,'inactive'=>0];
foreach ($mySensors as $s) { $statusCounts[$s['status']] = ($statusCounts[$s['status']]??0)+1; }

$readings = $pdo->prepare("SELECT d.*,s.type as sensor_type FROM data_table d JOIN sensor s ON d.sensor_id=s.sensor_id WHERE d.field_id=? ORDER BY d.`timestamp` DESC LIMIT 10");
$readings->execute([$fieldId]);
$recentReadings = $readings->fetchAll();

$dtStmt = $pdo->prepare("
    SELECT 
        (SELECT COUNT(*) FROM soil_data sd JOIN data_table d ON sd.data_id = d.data_id WHERE d.field_id = ?) as Soil,
        (SELECT COUNT(*) FROM weather_data wd JOIN data_table d ON wd.data_id = d.data_id WHERE d.field_id = ?) as Weather,
        (SELECT COUNT(*) FROM irrigation_data id JOIN data_table d ON id.data_id = d.data_id WHERE d.field_id = ?) as Irrigation,
        (SELECT COUNT(*) FROM equipment_data ed JOIN data_table d ON ed.data_id = d.data_id WHERE d.field_id = ?) as Equipment
");
$dtStmt->execute([$fieldId, $fieldId, $fieldId, $fieldId]);
$dataTypeCounts = $dtStmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="stats-grid">
    <div class="stat-card cyan"><div class="stat-icon cyan"><i class="fas fa-map-marker-alt"></i></div><div class="stat-info"><span class="stat-value"><?=htmlspecialchars($techInfo['field_location']??'N/A')?></span><span class="stat-label">Assigned Field</span></div></div>
    <div class="stat-card violet"><div class="stat-icon violet"><i class="fas fa-microchip"></i></div><div class="stat-info"><span class="stat-value"><?=count($mySensors)?></span><span class="stat-label">Total Sensors</span></div></div>
    <div class="stat-card emerald"><div class="stat-icon emerald"><i class="fas fa-check-circle"></i></div><div class="stat-info"><span class="stat-value"><?=$statusCounts['active']?></span><span class="stat-label">Active</span></div></div>
    <div class="stat-card red"><div class="stat-icon red"><i class="fas fa-tools"></i></div><div class="stat-info"><span class="stat-value"><?=$statusCounts['maintenance']?></span><span class="stat-label">Maintenance</span></div></div>
</div>
<div class="grid-2 mb-24">
    <div class="card"><div class="card-header"><h3>Data Records by Type</h3></div><div class="chart-container" style="max-height:220px"><canvas id="data-type-chart"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Sensor Status</h3></div><div class="chart-container" style="max-height:220px"><canvas id="status-chart"></canvas></div></div>
</div>
<div class="grid-2">
    <div class="card mb-24" style="grid-column: 1 / -1;"><div class="card-header"><h3>Sensor Inventory</h3></div><div class="table-wrapper"><table><thead><tr><th>ID</th><th>Type</th><th>Status</th><th>Calibration</th><th></th></tr></thead><tbody>
    <?php foreach($mySensors as $s):$cls=['active'=>'badge-active','maintenance'=>'badge-maintenance','inactive'=>'badge-inactive'];?>
    <tr><td>#<?=$s['sensor_id']?></td><td style="color:var(--text-primary);font-weight:500"><?=htmlspecialchars($s['type'])?></td><td><span class="badge <?=$cls[$s['status']]??'badge-info'?>"><?=ucfirst($s['status'])?></span></td><td style="font-size:12px"><?=$s['last_calibration_date']??'—'?></td><td><button class="btn btn-sm btn-secondary" onclick="calibrateSensor(<?=$s['sensor_id']?>)"><i class="fas fa-wrench"></i> Calibrate</button></td></tr>
    <?php endforeach;?></tbody></table></div></div>
</div>
<div class="card mb-24"><div class="card-header"><h3>Recent Readings</h3></div><div class="table-wrapper"><table><thead><tr><th>Sensor</th><th>Value</th><th>Time</th></tr></thead><tbody>
<?php foreach($recentReadings as $r):?>
<tr><td><span class="badge badge-info"><?=htmlspecialchars($r['sensor_type'])?></span></td><td style="color:var(--text-primary);font-weight:500"><?=$r['value']?> <?=htmlspecialchars($r['unit'])?></td><td style="font-size:12px"><?=$r['timestamp']?></td></tr>
<?php endforeach;if(empty($recentReadings)):?><tr><td colspan="3" class="text-center" style="color:var(--text-muted);padding:24px">No readings</td></tr><?php endif;?></tbody></table></div></div>
<script>
const dataTypeLabels = <?=json_encode(array_keys($dataTypeCounts))?>;
const dataTypeValues = <?=json_encode(array_values($dataTypeCounts))?>;
const sensorStatusLabels = <?=json_encode(array_map('ucfirst', array_keys($statusCounts)))?>;
const sensorStatusValues = <?=json_encode(array_values($statusCounts))?>;

document.addEventListener('DOMContentLoaded', () => {
    FarmCharts.renderPieChart('data-type-chart', dataTypeLabels, dataTypeValues, {
        'Soil': CHART_COLORS.amber,
        'Weather': CHART_COLORS.blue,
        'Irrigation': CHART_COLORS.cyan,
        'Equipment': CHART_COLORS.violet
    }, false);
    
    FarmCharts.renderPieChart('status-chart', sensorStatusLabels, sensorStatusValues, {
        'Active': CHART_COLORS.emerald,
        'Maintenance': CHART_COLORS.amber,
        'Inactive': CHART_COLORS.red,
        'Broken': CHART_COLORS.red
    }, true);
});
async function calibrateSensor(id){try{await App.api('sensors.php',{method:'PUT',body:{sensor_id:id,last_calibration_date:new Date().toISOString().split('T')[0],status:'active'}});Toast.success('Sensor calibrated');setTimeout(()=>location.reload(),1000);}catch(e){}}
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
