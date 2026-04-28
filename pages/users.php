<?php
$pageTitle = 'User Management';
require_once __DIR__ . '/../includes/header.php';
requireRole('dba');
$pdo = getDB();
$stmt = $pdo->query("CALL GetAllFields()");
$fields = $stmt->fetchAll();
$stmt->closeCursor();
?>
<div class="action-bar">
    <div></div>
    <button class="btn btn-primary" onclick="openCreateUser()"><i class="fas fa-plus"></i> Add User</button>
</div>
<div class="card"><div id="users-table">Loading...</div></div>
<script>
const uFields=<?=json_encode($fields)?>;
const userFormFields=[
    {name:'user_id',type:'hidden'},
    {name:'name',label:'Full Name',required:true},
    {name:'email',label:'Email',type:'email',required:true},
    {name:'password',label:'Password',type:'password'},
    {name:'phone_number',label:'Phone'},
    {name:'role',label:'Role',type:'select',options:[{value:'farmer',label:'Farmer'},{value:'agronomist',label:'Agronomist'},{value:'technician',label:'Technician'},{value:'dba',label:'Admin'}],required:true},
    {name:'permissions_level',label:'Permissions',type:'select',options:['basic','standard','admin'],default:'basic'},
    {name:'experience_level',label:'Experience',type:'select',options:['beginner','intermediate','expert'],default:'beginner'}
];
async function loadUsers(){
    try{
        const data=await App.api('users.php');
        const cols=[
            {key:'user_id',label:'ID',render:v=>'#'+v},
            {key:'name',label:'Name',render:v=>`<span style="color:var(--text-primary);font-weight:500">${v}</span>`},
            {key:'email',label:'Email'},
            {key:'role',label:'Role',render:v=>`<span class="badge badge-info">${v}</span>`},
            {key:'permissions_level',label:'Permissions'},
            {key:'experience_level',label:'Experience'}
        ];
        const actions=(row)=>`<button class="btn btn-sm btn-secondary" onclick='editUser(${JSON.stringify(row)})'><i class="fas fa-edit"></i></button><button class="btn btn-sm btn-danger" onclick="deleteUser(${row.user_id},\`${row.name}\`)"><i class="fas fa-trash"></i></button>`;
        document.getElementById('users-table').innerHTML=CRUD.buildTable(cols,data,actions);
    }catch(e){document.getElementById('users-table').innerHTML='<div class="empty-state"><p>Failed to load</p></div>';}
}
function openCreateUser(){CRUD.openFormModal('Add User',userFormFields,{},async(d)=>{try{await App.api('users.php',{method:'POST',body:d});Toast.success('User created');Modal.close();loadUsers();}catch(e){}});}
function editUser(row){CRUD.openFormModal('Edit User',userFormFields,row,async(d)=>{try{await App.api('users.php',{method:'PUT',body:d});Toast.success('User updated');Modal.close();loadUsers();}catch(e){}});}
function deleteUser(id,name){CRUD.confirmDelete(name,async()=>{try{await App.api('users.php?id='+id,{method:'DELETE'});Toast.success('User deleted');loadUsers();}catch(e){}});}
document.addEventListener('DOMContentLoaded',loadUsers);
</script>
<?php require_once __DIR__.'/../includes/footer.php';?>
