<?php
/**
 * Technician Dashboard
 */
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
?>
<div class="stats-grid">
    <div class="stat-card cyan"><div class="stat-icon cyan"><i class="fas fa-map-marker-alt"></i></div><div class="stat-info"><span class="stat-value"><?=htmlspecialchars($techInfo['field_location']??'N/A')?></span><span class="stat-label">Assigned Field</span></div></div>
    <div class="stat-card violet"><div class="stat-icon violet"><i class="fas fa-microchip"></i></div><div class="stat-info"><span class="stat-value"><?=count($mySensors)?></span><span class="stat-label">Total Sensors</span></div></div>
    <div class="stat-card emerald"><div class="stat-icon emerald"><i class="fas fa-check-circle"></i></div><div class="stat-info"><span class="stat-value"><?=$statusCounts['active']?></span><span class="stat-label">Active</span></div></div>
    <div class="stat-card red"><div class="stat-icon red"><i class="fas fa-tools"></i></div><div class="stat-info"><span class="stat-value"><?=$statusCounts['maintenance']?></span><span class="stat-label">Maintenance</span></div></div>
</div>
<div class="grid-2">
    <div class="card"><div class="card-header"><h3>Sensor Status</h3></div><div class="chart-container" style="max-height:250px"><canvas id="status-chart"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Sensor Inventory</h3></div><div class="table-wrapper"><table><thead><tr><th>ID</th><th>Type</th><th>Status</th><th>Calibration</th><th></th></tr></thead><tbody>
    <?php foreach($mySensors as $s):$cls=['active'=>'badge-active','maintenance'=>'badge-maintenance','inactive'=>'badge-inactive'];?>
    <tr><td>#<?=$s['sensor_id']?></td><td style="color:var(--text-primary);font-weight:500"><?=htmlspecialchars($s['type'])?></td><td><span class="badge <?=$cls[$s['status']]??'badge-info'?>"><?=$s['status']?></span></td><td style="font-size:12px"><?=$s['last_calibration_date']??'—'?></td><td><button class="btn btn-sm btn-secondary" onclick="calibrateSensor(<?=$s['sensor_id']?>)"><i class="fas fa-wrench"></i></button></td></tr>
    <?php endforeach;?></tbody></table></div></div>
</div>
<div class="card mb-24"><div class="card-header"><h3>Recent Readings</h3></div><div class="table-wrapper"><table><thead><tr><th>Sensor</th><th>Value</th><th>Time</th></tr></thead><tbody>
<?php foreach($recentReadings as $r):?>
<tr><td><span class="badge badge-info"><?=htmlspecialchars($r['sensor_type'])?></span></td><td style="color:var(--text-primary);font-weight:500"><?=$r['value']?> <?=htmlspecialchars($r['unit'])?></td><td style="font-size:12px"><?=$r['timestamp']?></td></tr>
<?php endforeach;if(empty($recentReadings)):?><tr><td colspan="3" class="text-center" style="color:var(--text-muted);padding:24px">No readings</td></tr><?php endif;?></tbody></table></div></div>
<script>
document.addEventListener('DOMContentLoaded',()=>{FarmCharts.renderStatusDoughnut('status-chart',<?=json_encode($statusCounts)?>);});
async function calibrateSensor(id){try{await App.api('sensors.php',{method:'PUT',body:{sensor_id:id,last_calibration_date:new Date().toISOString().split('T')[0],status:'active'}});Toast.success('Sensor calibrated');setTimeout(()=>location.reload(),1000);}catch(e){}}
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
