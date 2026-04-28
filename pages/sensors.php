<?php
$pageTitle = 'Sensor Management';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
$pdo = getDB();
$role = $_SESSION['user_role'];
$stmt = $pdo->query("CALL GetAllFields()");
$fields = $stmt->fetchAll();
$stmt->closeCursor();
?>
<div class="action-bar">
    <div class="filter-group">
        <input type="text" id="search-sensor" class="form-control" placeholder="Search sensors..." onkeyup="loadSensors()">
        <select class="form-control" id="filter-field" onchange="loadSensors()"><option value="">All Fields</option>
        <?php foreach($fields as $f):?><option value="<?=$f['field_id']?>"><?=htmlspecialchars($f['location'])?></option><?php endforeach;?></select>
        <select class="form-control" id="filter-status" onchange="loadSensors()"><option value="">All Status</option><option value="active">Active</option><option value="maintenance">Maintenance</option><option value="inactive">Inactive</option></select>
    </div>
    <?php if(in_array($role,['technician','dba'])):?>
    <button class="btn btn-primary" onclick="openCreateSensor()"><i class="fas fa-plus"></i> Add Sensor</button>
    <?php endif;?>
</div>
<div class="card"><div id="sensors-table">Loading...</div></div>
<script>
const sFields=<?=json_encode($fields)?>;
const sensorFormFields=[
    {name:'sensor_id',type:'hidden'},
    {name:'type',label:'Sensor Type',type:'select',options:['soil','weather','irrigation','equipment'],required:true},
    {name:'field_id',label:'Field',type:'select',options:sFields.map(f=>({value:f.field_id,label:f.location})),required:true},
    {name:'installation_date',label:'Installation Date',type:'date',required:true,default:new Date().toISOString().split('T')[0]},
    {name:'last_calibration_date',label:'Last Calibration',type:'date'},
    {name:'status',label:'Status',type:'select',options:['active','maintenance','inactive'],required:true,default:'active'}
];
async function loadSensors(){
    let url='sensors.php?';
    const fid=document.getElementById('filter-field').value;
    const st=document.getElementById('filter-status').value;
    const searchVal=document.getElementById('search-sensor').value.toLowerCase();
    if(fid)url+='field_id='+fid+'&';
    if(st)url+='status='+st;
    try{
        let data=await App.api(url);
        if(searchVal) {
            data = data.filter(r => 
                (r.type && r.type.toLowerCase().includes(searchVal)) || 
                (r.field_location && r.field_location.toLowerCase().includes(searchVal)) ||
                (r.status && r.status.toLowerCase().includes(searchVal))
            );
        }
        const cols=[
            {key:'sensor_id',label:'ID',render:v=>'#'+v},
            {key:'type',label:'Type',render:v=>`<span style="color:var(--text-primary);font-weight:500">${v}</span>`},
            {key:'field_location',label:'Field'},
            {key:'status',label:'Status',render:v=>App.statusBadge(v)},
            {key:'installation_date',label:'Installed',render:v=>App.formatDate(v)},
            {key:'last_calibration_date',label:'Calibrated',render:v=>App.formatDate(v)}
        ];
        const canEdit=<?=json_encode(in_array($role,['technician','dba']))?>;
        const actions=canEdit?(row)=>`<button class="btn btn-sm btn-secondary" onclick='editSensor(${JSON.stringify(row)})'><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger" onclick="deleteSensor(${row.sensor_id},\`${row.type}\`)"><i class="fas fa-trash"></i></button>`:null;
        document.getElementById('sensors-table').innerHTML=CRUD.buildTable(cols,data,actions);
    }catch(e){document.getElementById('sensors-table').innerHTML='<div class="empty-state"><p>Failed to load</p></div>';}
}
function openCreateSensor(){CRUD.openFormModal('Add Sensor',sensorFormFields,{},async(d)=>{try{await App.api('sensors.php',{method:'POST',body:d});Toast.success('Sensor created');Modal.close();loadSensors();}catch(e){}});}
function editSensor(row){CRUD.openFormModal('Edit Sensor',sensorFormFields,row,async(d)=>{try{await App.api('sensors.php',{method:'PUT',body:d});Toast.success('Sensor updated');Modal.close();loadSensors();}catch(e){}});}
function deleteSensor(id,name){CRUD.confirmDelete(name+' sensor',async()=>{try{await App.api('sensors.php?id='+id,{method:'DELETE'});Toast.success('Sensor deleted');loadSensors();}catch(e){}});}
document.addEventListener('DOMContentLoaded',loadSensors);
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
