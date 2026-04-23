<?php
$pageTitle = 'Sensor Data';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
$pdo = getDB();
$fields = $pdo->query("SELECT field_id, location FROM field ORDER BY field_id")->fetchAll();
?>
<div class="action-bar">
    <div class="filter-group">
        <select class="form-control" id="filter-type" onchange="loadData()"><option value="">All Types</option><option value="soil">Soil</option><option value="weather">Weather</option><option value="irrigation">Irrigation</option><option value="equipment">Equipment</option></select>
        <select class="form-control" id="filter-field" onchange="loadData()"><option value="">All Fields</option>
        <?php foreach($fields as $f):?><option value="<?=$f['field_id']?>"><?=htmlspecialchars($f['location'])?></option><?php endforeach;?></select>
    </div>
</div>
<!-- Charts -->
<div class="grid-2 mb-24">
    <div class="card"><div class="card-header"><h3>Soil Analysis</h3></div><div class="chart-container"><canvas id="soil-chart"></canvas></div></div>
    <div class="card"><div class="card-header"><h3>Weather Overview</h3></div><div class="chart-container"><canvas id="weather-chart"></canvas></div></div>
</div>
<!-- Data Table -->
<div class="card"><div class="card-header"><h3>Data Records</h3></div><div id="data-table">Loading...</div></div>
<script>
async function loadData(){
    const type=document.getElementById('filter-type').value;
    const fid=document.getElementById('filter-field').value;
    let url='data.php?';
    if(type)url+='type='+type+'&';
    if(fid)url+='field_id='+fid+'&';
    url+='limit=50';
    try{
        const data=await App.api(url);
        const cols=[
            {key:'data_id',label:'ID',render:v=>'#'+v},
            {key:'value',label:'Value',render:(v,r)=>`<span style="color:var(--text-primary);font-weight:500">${v} ${r.unit}</span>`},
            {key:'field_location',label:'Field'},
            {key:'timestamp',label:'Timestamp',render:v=>App.formatDateTime(v)}
        ];
        // Add type-specific columns
        if(type==='soil'){cols.splice(2,0,{key:'ph_level',label:'pH'},{key:'moisture',label:'Moisture %'});}
        if(type==='weather'){cols.splice(2,0,{key:'temperature',label:'Temp °C'},{key:'humidity',label:'Humidity %'},{key:'rainfall',label:'Rain mm'});}
        document.getElementById('data-table').innerHTML=CRUD.buildTable(cols,data);
    }catch(e){document.getElementById('data-table').innerHTML='<div class="empty-state"><p>Failed to load</p></div>';}
    // Refresh charts
    const fieldId=fid||null;
    FarmCharts.renderSoilChart('soil-chart',fieldId);
    FarmCharts.renderWeatherChart('weather-chart',fieldId);
}
document.addEventListener('DOMContentLoaded',loadData);
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
