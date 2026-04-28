<?php
$pageTitle = 'Field Management';
require_once __DIR__ . '/../includes/header.php';
requireLogin();
$pdo = getDB();
$role = $_SESSION['user_role'];
$userId = $_SESSION['user_id'];
$stmt = $pdo->query("CALL GetFarmers()");
$farmers = $stmt->fetchAll();
$stmt->closeCursor();
?>
<div class="action-bar">
    <div class="filter-group">
        <input type="text" id="search-field" class="form-control" placeholder="Search fields..." onkeyup="loadFields()">
        <select class="form-control" id="filter-farmer" onchange="loadFields()">
            <option value="">All Farmers</option>
            <?php foreach($farmers as $f):?><option value="<?=$f['user_id']?>"><?=htmlspecialchars($f['name'])?></option><?php endforeach;?>
        </select>
    </div>
    <?php if(in_array($role,['farmer','dba'])):?>
    <button class="btn btn-primary" onclick="openCreateField()"><i class="fas fa-plus"></i> Add Field</button>
    <?php endif;?>
</div>
<div class="card"><div id="fields-table">Loading...</div></div>
<script>
const farmers = <?=json_encode($farmers)?>;
const fieldFormFields = [
    {name:'field_id',type:'hidden'},
    {name:'location',label:'Location',required:true},
    {name:'size',label:'Size (hectares)',type:'number',step:'0.01',required:true},
    {name:'irrigation_type',label:'Irrigation Type',type:'select',options:['drip','sprinkler','flood','manual'],required:true},
    {name:'farmer_id',label:'Farmer',type:'select',options:farmers.map(f=>({value:f.user_id,label:f.name})),required:true}
];
async function loadFields(){
    const fid=document.getElementById('filter-farmer').value;
    const searchVal=document.getElementById('search-field').value.toLowerCase();
    const url=fid?'fields.php?farmer_id='+fid:'fields.php';
    try{
        let data=await App.api(url);
        if(searchVal) {
            data = data.filter(r => 
                (r.location && r.location.toLowerCase().includes(searchVal)) || 
                (r.irrigation_type && r.irrigation_type.toLowerCase().includes(searchVal)) ||
                (r.farmer_name && r.farmer_name.toLowerCase().includes(searchVal))
            );
        }
        const cols=[
            {key:'field_id',label:'ID',render:v=>'#'+v},
            {key:'location',label:'Location',render:(v)=>`<span style="color:var(--text-primary);font-weight:500">${v}</span>`},
            {key:'size',label:'Size (ha)'},
            {key:'irrigation_type',label:'Irrigation',render:v=>`<span class="badge badge-info">${v}</span>`},
            {key:'farmer_name',label:'Farmer'},
            {key:'sensor_count',label:'Sensors'}
        ];
        const actions=<?=json_encode(in_array($role,['farmer','dba']))?>?(row)=>`<button class="btn btn-sm btn-secondary" onclick='editField(${JSON.stringify(row)})'><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger" onclick="deleteField(${row.field_id},\`${row.location}\`)"><i class="fas fa-trash"></i></button>`
        :null;
        document.getElementById('fields-table').innerHTML=CRUD.buildTable(cols,data,actions);
    }catch(e){document.getElementById('fields-table').innerHTML='<div class="empty-state"><p>Failed to load</p></div>';}
}
function openCreateField(){CRUD.openFormModal('Add Field',fieldFormFields,{farmer_id:<?=$role==='farmer'?$userId:0?>},async(d)=>{try{await App.api('fields.php',{method:'POST',body:d});Toast.success('Field created');Modal.close();loadFields();}catch(e){}});}
function editField(row){CRUD.openFormModal('Edit Field',fieldFormFields,row,async(d)=>{try{await App.api('fields.php',{method:'PUT',body:d});Toast.success('Field updated');Modal.close();loadFields();}catch(e){}});}
function deleteField(id,name){CRUD.confirmDelete(name,async()=>{try{await App.api('fields.php?id='+id,{method:'DELETE'});Toast.success('Field deleted');loadFields();}catch(e){}});}
document.addEventListener('DOMContentLoaded',loadFields);
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
