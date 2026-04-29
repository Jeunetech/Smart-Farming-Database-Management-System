<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
requireRole('dba');
$pdo = getDB();

$stats = $pdo->query("SELECT * FROM v_dashboard_stats")->fetch();
$userCount = $stats['user_count'];
$fieldCount = $stats['field_count'];
$sensorCount = $stats['sensor_count'];
$dataCount = $stats['data_count'];

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

$users = $pdo->query("SELECT user_id, name, email, permissions_level FROM `user` ORDER BY user_id LIMIT $limit OFFSET $offset")->fetchAll();
$allRoles = getAllUserRoles($pdo);
foreach ($users as $k => $v) { $users[$k]['role'] = $allRoles[$v['user_id']] ?? 'unknown'; }

$totalUsers = $pdo->query("SELECT COUNT(*) FROM `user`")->fetchColumn();
$totalPages = ceil($totalUsers / $limit);

$recentData = $pdo->query("SELECT * FROM v_recent_data ORDER BY `timestamp` DESC LIMIT 8")->fetchAll();
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
    
    <?php if($totalPages > 1): ?>
    <div style="padding: 1rem; display: flex; gap: 0.5rem; justify-content: center; align-items: center; border-top: 1px solid var(--border-color);">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary"><i class="fas fa-chevron-left"></i> Prev</a>
        <?php endif; ?>
        
        <?php for($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-secondary' ?>"><?= $i ?></a>
        <?php endfor; ?>
        
        <?php if($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary">Next <i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<div class="card mb-24">
    <div class="card-header">
        <h3>Recent Activity</h3>
        <button class="btn btn-sm btn-primary" onclick="openAddWeatherData()"><i class="fas fa-cloud-sun"></i> Add Weather Data</button>
    </div>
    <div class="table-wrapper"><table><thead><tr><th>Data ID</th><th>Value</th><th>Field</th><th>Timestamp</th></tr></thead><tbody>
    <?php foreach($recentData as $d):?>
    <tr><td>#<?=$d['data_id']?></td><td style="color:var(--text-primary)"><?=$d['value']?> <?=htmlspecialchars($d['unit'])?></td><td><?=htmlspecialchars($d['field_location'])?></td><td style="font-size:12px"><?=$d['timestamp']?></td></tr>
    <?php endforeach;?></tbody></table></div>
</div>
<?php
$stmtFields = $pdo->query("CALL GetAllFields()");
$allFields = $stmtFields->fetchAll();
$stmtFields->closeCursor();

$soilData = $pdo->query("SELECT d.*, sd.ph_level, sd.moisture, sd.nutrient_levels, sd.sample_date, f.location as field_location FROM data_table d INNER JOIN soil_data sd ON d.data_id = sd.data_id JOIN field f ON d.field_id = f.field_id ORDER BY d.`timestamp` DESC LIMIT 50")->fetchAll();
$weatherData = $pdo->query("SELECT d.*, wd.temperature, wd.humidity, wd.rainfall, wd.wind_speed, f.location as field_location FROM data_table d INNER JOIN weather_data wd ON d.data_id = wd.data_id JOIN field f ON d.field_id = f.field_id ORDER BY d.`timestamp` DESC LIMIT 50")->fetchAll();
?>
<script>
const inlineSoilData = <?=json_encode($soilData)?>;
const inlineWeatherData = <?=json_encode($weatherData)?>;
document.addEventListener('DOMContentLoaded', () => {
    FarmCharts.renderSoilChart('soil-chart', null, inlineSoilData);
    FarmCharts.renderWeatherChart('weather-chart', null, inlineWeatherData);
});
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
