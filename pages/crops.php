<?php
$pageTitle = 'Crop Management';
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
        <input type="text" id="search-crop" class="form-control" placeholder="Search crops..." onkeyup="loadCrops()">
        <select class="form-control" id="filter-field" onchange="loadCrops()"><option value="">All Fields</option>
        <?php foreach($fields as $f):?><option value="<?=$f['field_id']?>"><?=htmlspecialchars($f['location'])?></option><?php endforeach;?></select>
    </div>
    <?php if(in_array($role,['farmer','dba'])):?>
    <button class="btn btn-primary" onclick="openCreateCrop()"><i class="fas fa-plus"></i> Add Crop</button>
    <?php endif;?>
</div>
<div class="card"><div id="crops-table">Loading...</div></div>
<script>
const cFields=<?=json_encode($fields)?>;
const cropFormFields=[
    {name:'crop_id',type:'hidden'},
    {name:'name',label:'Crop Name',required:true},
    {name:'field_id',label:'Field',type:'select',options:cFields.map(f=>({value:f.field_id,label:f.location})),required:true},
    {name:'planting_date',label:'Planting Date',type:'date',required:true,default:new Date().toISOString().split('T')[0]},
    {name:'yield_value',label:'Yield Value',type:'number',step:'0.01'},
    {name:'yield_unit',label:'Yield Unit',type:'select',options:['kg','ton','bushel']}
];
async function loadCrops(){
    const fid=document.getElementById('filter-field').value;
    const searchVal=document.getElementById('search-crop').value.toLowerCase();
    const url=fid?'crops.php?field_id='+fid:'crops.php';
    try{
        let data=await App.api(url);
        if(searchVal) {
            data = data.filter(r => 
                (r.name && r.name.toLowerCase().includes(searchVal)) || 
                (r.field_location && r.field_location.toLowerCase().includes(searchVal))
            );
        }
        const cols=[
            {key:'crop_id',label:'ID',render:v=>'#'+v},
            {key:'name',label:'Crop',render:v=>`<span style="color:var(--text-primary);font-weight:500">${v}</span>`},
            {key:'field_location',label:'Field'},
            {key:'planting_date',label:'Planted',render:v=>App.formatDate(v)},
            {key:'yield_value',label:'Yield',render:(v,row)=>v?v+' '+(row.yield_unit||''):'—'}
        ];
        const canEdit=<?=json_encode(in_array($role,['farmer','dba']))?>;
        const actions=canEdit?(row)=>`<button class="btn btn-sm btn-secondary" onclick='editCrop(${JSON.stringify(row)})'><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger" onclick="deleteCrop(${row.crop_id},\`${row.name}\`)"><i class="fas fa-trash"></i></button>`:null;
        document.getElementById('crops-table').innerHTML=CRUD.buildTable(cols,data,actions);
    }catch(e){document.getElementById('crops-table').innerHTML='<div class="empty-state"><p>Failed to load</p></div>';}
}
function openCreateCrop(){CRUD.openFormModal('Add Crop',cropFormFields,{},async(d)=>{try{await App.api('crops.php',{method:'POST',body:d});Toast.success('Crop created');Modal.close();loadCrops();}catch(e){}});}
function editCrop(row){CRUD.openFormModal('Edit Crop',cropFormFields,row,async(d)=>{try{await App.api('crops.php',{method:'PUT',body:d});Toast.success('Crop updated');Modal.close();loadCrops();}catch(e){}});}
function deleteCrop(id,name){CRUD.confirmDelete(name,async()=>{try{await App.api('crops.php?id='+id,{method:'DELETE'});Toast.success('Crop deleted');loadCrops();}catch(e){}});}
document.addEventListener('DOMContentLoaded',loadCrops);
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
