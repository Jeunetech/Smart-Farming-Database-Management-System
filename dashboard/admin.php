<?php
/**
 * Admin (DBA) Dashboard
 */
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole('dba');
$pdo = getDB();

$userCount = $pdo->query("SELECT COUNT(*) FROM `user`")->fetchColumn();
$fieldCount = $pdo->query("SELECT COUNT(*) FROM field")->fetchColumn();
$sensorCount = $pdo->query("SELECT COUNT(*) FROM sensor")->fetchColumn();
$dataCount = $pdo->query("SELECT COUNT(*) FROM data_table")->fetchColumn();

$users = $pdo->query("SELECT user_id, name, email, permissions_level, experience_level FROM `user` ORDER BY user_id")->fetchAll();
foreach ($users as &$u) { $u['role'] = getUserRole($u['user_id'], $pdo); }

$recentData = $pdo->query("SELECT d.*, f.location as field_location FROM data_table d JOIN field f ON d.field_id = f.field_id ORDER BY d.`timestamp` DESC LIMIT 8")->fetchAll();
?>
<div class="stats-grid">
    <div class="stat-card cyan"><div class="stat-icon cyan"><i class="fas fa-users"></i></div><div class="stat-info"><span class="stat-value"><?=$userCount?></span><span class="stat-label">Users</span></div></div>
    <div class="stat-card violet"><div class="stat-icon violet"><i class="fas fa-map-marked-alt"></i></div><div class="stat-info"><span class="stat-value"><?=$fieldCount?></span><span class="stat-label">Fields</span></div></div>
    <div class="stat-card emerald"><div class="stat-icon emerald"><i class="fas fa-microchip"></i></div><div class="stat-info"><span class="stat-value"><?=$sensorCount?></span><span class="stat-label">Sensors</span></div></div>
    <div class="stat-card amber"><div class="stat-icon amber"><i class="fas fa-database"></i></div><div class="stat-info"><span class="stat-value"><?=$dataCount?></span><span class="stat-label">Data Records</span></div></div>
</div>
<div class="grid-2">
    <div class="card"><div class="card-header"><h3>Soil Trends</h3></div><div class="chart-container"><canvas id="soil-chart"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Weather Trends</h3></div><div class="chart-container"><canvas id="weather-chart"></canvas></div></div>
</div>
<div class="card mb-24">
    <div class="card-header"><h3>User Management</h3><a href="/pages/users.php" class="btn btn-sm btn-primary"><i class="fas fa-users-cog"></i> Manage All</a></div>
    <div class="table-wrapper"><table><thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Permissions</th></tr></thead><tbody>
    <?php foreach($users as $u):?>
    <tr><td>#<?=$u['user_id']?></td><td style="color:var(--text-primary);font-weight:500"><?=htmlspecialchars($u['name'])?></td><td><?=htmlspecialchars($u['email'])?></td><td><span class="badge badge-info"><?=ucfirst($u['role'])?></span></td><td><?=$u['permissions_level']?></td></tr>
    <?php endforeach;?></tbody></table></div>
</div>
<div class="card mb-24">
    <div class="card-header"><h3>Recent Activity</h3></div>
    <div class="table-wrapper"><table><thead><tr><th>Data ID</th><th>Value</th><th>Field</th><th>Timestamp</th></tr></thead><tbody>
    <?php foreach($recentData as $d):?>
    <tr><td>#<?=$d['data_id']?></td><td style="color:var(--text-primary)"><?=$d['value']?> <?=htmlspecialchars($d['unit'])?></td><td><?=htmlspecialchars($d['field_location'])?></td><td style="font-size:12px"><?=$d['timestamp']?></td></tr>
    <?php endforeach;?></tbody></table></div>
</div>
<script>
document.addEventListener('DOMContentLoaded',()=>{FarmCharts.renderSoilChart('soil-chart');FarmCharts.renderWeatherChart('weather-chart');});
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
