<?php 
// ============================================================
// GESTIÓN DE PONENTES - CRUD COMPLETO
// ============================================================
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /views/login.php");
    exit;
}

require_once '../../config/database.php';
$db = (new Database())->getConnection();

// ============================================================
// ELIMINAR PONENTE
// ============================================================
if(isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $db->prepare("DELETE FROM speakers WHERE id = ?")->execute([$id]);
    header("Location: ponentes.php?msg=eliminado");
    exit;
}

// ============================================================
// CREAR PONENTE
// ============================================================
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $stmt = $db->prepare("INSERT INTO speakers (name, institution, country, bio, conference_title) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['institution'], $_POST['country'], $_POST['bio'], $_POST['conference_title']]);
    header("Location: ponentes.php?msg=creado");
    exit;
}

// ============================================================
// EDITAR PONENTE
// ============================================================
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $stmt = $db->prepare("UPDATE speakers SET name=?, institution=?, country=?, bio=?, conference_title=? WHERE id=?");
    $stmt->execute([$_POST['name'], $_POST['institution'], $_POST['country'], $_POST['bio'], $_POST['conference_title'], $_POST['id']]);
    header("Location: ponentes.php?msg=editado");
    exit;
}

// ============================================================
// OBTENER PONENTE PARA EDITAR
// ============================================================
$ponente_editar = null;
if(isset($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM speakers WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $ponente_editar = $stmt->fetch();
}

// ============================================================
// LISTAR PONENTES
// ============================================================
$ponentes = $db->query("SELECT * FROM speakers ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ponentes - TESCo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ============================================ */
        /* ESTILOS PARA GESTIÓN DE PONENTES             */
        /* ============================================ */
        body { background: #f8fafc; }
        .admin-header { background: #0f766e; color: white; padding: 15px 0; margin-bottom: 30px; }
        .admin-header .container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .admin-header a { color: white; text-decoration: none; margin-left: 20px; }
        
        .btn-accion { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; border: none; cursor: pointer; display: inline-block; }
        .btn-editar { background: #f4b400; color: #003366; }
        .btn-eliminar { background: #dc2626; color: white; }
        .btn-agregar { background: #2e7d32; color: white; padding: 10px 24px; border-radius: 25px; border: none; font-weight: 600; margin-bottom: 20px; }
        
        .form-card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #2e7d32; }
        .form-card label { color: #003366; font-weight: 600; display: block; margin-bottom: 5px; }
        .form-card input, .form-card select, .form-card textarea { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; }
        
        .table-panel { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-panel th { background: #0f766e; color: white; padding: 12px; }
        .table-panel td { padding: 12px; vertical-align: middle; }
        .action-buttons { display: flex; gap: 5px; flex-wrap: wrap; }
        
        footer { background: #0f172a; color: white; text-align: center; padding: 20px; margin-top: 50px; }
        
        @media (max-width: 768px) {
            .admin-header .container { flex-direction: column; gap: 10px; text-align: center; }
            .d-flex.justify-content-between { flex-direction: column; gap: 10px; text-align: center; }
            .action-buttons { flex-direction: column; }
            .btn-accion, .btn-agregar { width: 100%; margin: 2px 0; }
            .table-responsive { font-size: 0.7rem; }
            .table-panel th, .table-panel td { padding: 6px; }
            .form-card .row { flex-direction: column; }
            .form-card .col-md-6 { width: 100%; margin-bottom: 10px; }
        }
    </style>
</head>
<body>

<!-- HEADER SENCILLO -->
<header class="admin-header">
    <div class="container">
        <h2><i class="fas fa-microphone-alt"></i> TESCo - Ponentes</h2>
        <div><a href="/views/index.php">Inicio</a><a href="dashboard.php">Dashboard</a><a href="/controllers/LogoutController.php">Salir</a></div>
    </div>
</header>

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:#0f766e;"><i class="fas fa-microphone-alt"></i> Ponentes Internacionales</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php if($_GET['msg']=='creado') echo '✅ Ponente creado'; elseif($_GET['msg']=='editado') echo '✏️ Ponente editado'; elseif($_GET['msg']=='eliminado') echo '🗑️ Ponente eliminado'; ?></div>
    <?php endif; ?>

    <button class="btn-agregar" onclick="toggleForm()"><i class="fas fa-plus"></i> Nuevo Ponente</button>

    <!-- Formulario crear -->
    <div id="formCrear" style="display:none;" class="form-card">
        <h4>Nuevo Ponente</h4>
        <form method="POST"><input type="hidden" name="crear" value="1">
            <div class="row">
                <div class="col-md-6 mb-2"><label>Nombre</label><input type="text" name="name" required></div>
                <div class="col-md-6 mb-2"><label>Institución</label><input type="text" name="institution" required></div>
                <div class="col-md-6 mb-2"><label>País</label><input type="text" name="country" required></div>
                <div class="col-md-6 mb-2"><label>Título</label><input type="text" name="conference_title" required></div>
                <div class="col-md-12 mb-2"><label>Biografía</label><textarea name="bio" rows="3"></textarea></div>
                <div class="col-md-12"><button type="submit" style="background:#2e7d32;color:white;" class="btn">Guardar</button><button type="button" class="btn btn-secondary" onclick="toggleForm()">Cancelar</button></div>
            </div>
        </form>
    </div>

    <!-- Formulario editar -->
    <div id="formEditar" style="display:none;" class="form-card">
        <h4>Editar Ponente</h4>
        <form method="POST"><input type="hidden" name="editar" value="1"><input type="hidden" name="id" id="edit_id">
            <div class="row">
                <div class="col-md-6 mb-2"><label>Nombre</label><input type="text" name="name" id="edit_name" required></div>
                <div class="col-md-6 mb-2"><label>Institución</label><input type="text" name="institution" id="edit_institution" required></div>
                <div class="col-md-6 mb-2"><label>País</label><input type="text" name="country" id="edit_country" required></div>
                <div class="col-md-6 mb-2"><label>Título</label><input type="text" name="conference_title" id="edit_title" required></div>
                <div class="col-md-12 mb-2"><label>Biografía</label><textarea name="bio" id="edit_bio" rows="3"></textarea></div>
                <div class="col-md-12"><button type="submit" style="background:#2e7d32;color:white;" class="btn">Guardar</button><button type="button" class="btn btn-secondary" onclick="cerrarEdicion()">Cancelar</button></div>
            </div>
        </form>
    </div>

    <!-- Tabla de ponentes -->
    <div class="table-responsive table-panel">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Nombre</th><th>Institución</th><th>País</th><th>Título</th><th>Acciones</th></tr></thead>
            <tbody><?php foreach($ponentes as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                <td><?php echo htmlspecialchars($p['institution']); ?></td>
                <td><?php echo htmlspecialchars($p['country']); ?></td>
                <td><?php echo htmlspecialchars($p['conference_title']); ?></td>
                <td class="action-buttons">
                    <button class="btn-accion btn-editar" onclick='editar(<?php echo $p['id']; ?>,"<?php echo addslashes($p['name']); ?>","<?php echo addslashes($p['institution']); ?>","<?php echo addslashes($p['country']); ?>","<?php echo addslashes($p['conference_title']); ?>","<?php echo addslashes($p['bio']); ?>")'>Editar</button>
                    <a href="?eliminar=<?php echo $p['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar?')">Eliminar</a>
                </td>
            </tr><?php endforeach; ?></tbody>
        </table>
    </div>
</div>

<script>
function toggleForm(){var f=document.getElementById('formCrear');f.style.display=f.style.display==='none'?'block':'none';}
function editar(id,n,i,c,t,b){document.getElementById('edit_id').value=id;document.getElementById('edit_name').value=n;document.getElementById('edit_institution').value=i;document.getElementById('edit_country').value=c;document.getElementById('edit_title').value=t;document.getElementById('edit_bio').value=b;document.getElementById('formEditar').style.display='block';window.scrollTo({top:document.getElementById('formEditar').offsetTop-20,behavior:'smooth'});}
function cerrarEdicion(){document.getElementById('formEditar').style.display='none';}
</script>

<footer><p>&copy; 2027 TESCo Congress</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>