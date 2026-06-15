<?php 
// ============================================================
// GESTIÓN DE CONVOCATORIAS
// ============================================================
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /views/login.php");
    exit;
}

require_once '../../config/database.php';
$db = (new Database())->getConnection();

// Crear convocatoria
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $stmt = $db->prepare("INSERT INTO calls (title, description, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['title'], $_POST['description'], $_POST['start_date'], $_POST['end_date'], isset($_POST['is_active']) ? 1 : 0]);
    header("Location: convocatorias.php?msg=creado");
    exit;
}

// Eliminar convocatoria
if(isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $db->prepare("DELETE FROM calls WHERE id = ?")->execute([$id]);
    header("Location: convocatorias.php?msg=eliminado");
    exit;
}

$convocatorias = $db->query("SELECT * FROM calls ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Convocatorias - TESCo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8fafc; }
        .admin-header { background: #0f766e; color: white; padding: 15px 0; margin-bottom: 30px; }
        .admin-header .container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .admin-header a { color: white; text-decoration: none; margin-left: 20px; }
        
        .btn-agregar { background: #2e7d32; color: white; padding: 10px 24px; border-radius: 25px; border: none; font-weight: 600; margin-bottom: 20px; }
        .btn-eliminar { background: #dc2626; color: white; padding: 5px 12px; border-radius: 20px; border: none; cursor: pointer; display: inline-block; }
        
        .form-card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 25px; border-left: 4px solid #2e7d32; }
        .form-card label { color: #003366; font-weight: 600; display: block; margin-bottom: 5px; }
        .form-card input, .form-card textarea, .form-card select { width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; }
        
        .table-panel { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-panel th { background: #0f766e; color: white; padding: 12px; }
        .table-panel td { padding: 12px; vertical-align: middle; }
        
        footer { background: #0f172a; color: white; text-align: center; padding: 20px; margin-top: 50px; }
        
        @media (max-width: 768px) {
            .admin-header .container { flex-direction: column; gap: 10px; text-align: center; }
            .d-flex.justify-content-between { flex-direction: column; gap: 10px; text-align: center; }
            .btn-agregar { width: 100%; }
            .table-responsive { font-size: 0.7rem; }
            .table-panel th, .table-panel td { padding: 6px; }
            .form-card .row { flex-direction: column; }
            .form-card .col-md-6 { width: 100%; margin-bottom: 10px; }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <div class="container">
        <h2><i class="fas fa-calendar-alt"></i> TESCo - Convocatorias</h2>
        <div><a href="/views/index.php">Inicio</a><a href="dashboard.php">Dashboard</a><a href="/controllers/LogoutController.php">Salir</a></div>
    </div>
</header>

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:#0f766e;"><i class="fas fa-calendar-alt"></i> Convocatorias</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php if($_GET['msg']=='creado') echo '✅ Convocatoria creada'; elseif($_GET['msg']=='eliminado') echo '🗑️ Convocatoria eliminada'; ?></div>
    <?php endif; ?>

    <button class="btn-agregar" onclick="toggleForm()"><i class="fas fa-plus"></i> Nueva Convocatoria</button>

    <div id="formCrear" style="display:none;" class="form-card">
        <h4>Nueva Convocatoria</h4>
        <form method="POST"><input type="hidden" name="crear" value="1">
            <div class="row">
                <div class="col-md-12 mb-2"><label>Título</label><input type="text" name="title" required></div>
                <div class="col-md-6 mb-2"><label>Fecha inicio</label><input type="date" name="start_date" required></div>
                <div class="col-md-6 mb-2"><label>Fecha fin</label><input type="date" name="end_date" required></div>
                <div class="col-md-6 mb-2"><label>Activa</label><select name="is_active"><option value="1">Sí</option><option value="0">No</option></select></div>
                <div class="col-md-12 mb-2"><label>Descripción</label><textarea name="description" rows="3"></textarea></div>
                <div class="col-md-12"><button type="submit" style="background:#2e7d32;color:white;" class="btn">Guardar</button><button type="button" class="btn btn-secondary" onclick="toggleForm()">Cancelar</button></div>
            </div>
        </form>
    </div>

    <div class="table-responsive table-panel">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Título</th><th>Descripción</th><th>Inicio</th><th>Fin</th><th>Activa</th><th>Acciones</th></tr></thead>
            <tbody><?php foreach($convocatorias as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($c['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($c['description']); ?></td>
                <td><?php echo $c['start_date']; ?></td>
                <td><?php echo $c['end_date']; ?></td>
                <td><?php echo $c['is_active'] ? '✅ Sí' : '❌ No'; ?></td>
                <td><a href="?eliminar=<?php echo $c['id']; ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar esta convocatoria?')"><i class="fas fa-trash"></i> Eliminar</a></td>
            </tr><?php endforeach; ?></tbody>
        </table>
    </div>
</div>

<script>function toggleForm(){var f=document.getElementById('formCrear');f.style.display=f.style.display==='none'?'block':'none';}</script>

<footer><p>&copy; 2027 TESCo Congress</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>