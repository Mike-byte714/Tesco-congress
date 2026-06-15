<?php 
// ============================================================
// GESTIÓN DE PROYECTOS - CRUD COMPLETO
// ============================================================
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /views/login.php");
    exit;
}

require_once '../../config/database.php';
$db = (new Database())->getConnection();

// Cambiar estado del proyecto
if(isset($_GET['cambiar_estado']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $estado = $_GET['estado'];
    $db->prepare("UPDATE projects SET status = ? WHERE id = ?")->execute([$estado, $id]);
    header("Location: proyectos.php?msg=editado");
    exit;
}

// Eliminar proyecto
if(isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $db->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
    header("Location: proyectos.php?msg=eliminado");
    exit;
}

// Listar proyectos
$proyectos = $db->query("SELECT p.*, u.fullname as autor FROM projects p JOIN users u ON p.user_id = u.id ORDER BY p.submitted_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyectos - TESCo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8fafc; }
        .admin-header { background: #0f766e; color: white; padding: 15px 0; margin-bottom: 30px; }
        .admin-header .container { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .admin-header a { color: white; text-decoration: none; margin-left: 20px; }
        
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; display: inline-block; }
        .status-recibido { background: #fef3c7; color: #d97706; }
        .status-en_revision { background: #dbeafe; color: #2563eb; }
        .status-aceptado { background: #d1fae5; color: #059669; }
        .status-rechazado { background: #fee2e2; color: #dc2626; }
        
        .btn-accion { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; border: none; cursor: pointer; display: inline-block; text-decoration: none; }
        .btn-eliminar { background: #dc2626; color: white; }
        .btn-ver { background: #64748b; color: white; }
        
        .table-panel { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .table-panel th { background: #0f766e; color: white; padding: 12px; }
        .table-panel td { padding: 12px; vertical-align: middle; }
        .action-buttons { display: flex; gap: 5px; flex-wrap: wrap; align-items: center; }
        
        footer { background: #0f172a; color: white; text-align: center; padding: 20px; margin-top: 50px; }
        
        @media (max-width: 768px) {
            .admin-header .container { flex-direction: column; gap: 10px; text-align: center; }
            .d-flex.justify-content-between { flex-direction: column; gap: 10px; text-align: center; }
            .action-buttons { flex-direction: column; width: 100%; }
            .btn-accion, select.form-select-sm { width: 100%; margin: 2px 0; }
            .table-responsive { font-size: 0.7rem; }
            .table-panel th, .table-panel td { padding: 6px; }
        }
    </style>
</head>
<body>

<header class="admin-header">
    <div class="container">
        <h2><i class="fas fa-file-alt"></i> TESCo - Proyectos</h2>
        <div><a href="/views/index.php">Inicio</a><a href="dashboard.php">Dashboard</a><a href="/controllers/LogoutController.php">Salir</a></div>
    </div>
</header>

<div class="container py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color:#0f766e;"><i class="fas fa-file-alt"></i> Gestión de Proyectos</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php if($_GET['msg']=='editado') echo '✅ Estado actualizado'; elseif($_GET['msg']=='eliminado') echo '🗑️ Proyecto eliminado'; ?></div>
    <?php endif; ?>

    <div class="table-responsive table-panel">
        <table class="table table-hover">
            <thead><tr><th>ID</th><th>Título</th><th>Autor</th><th>Área</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody><?php foreach($proyectos as $p): ?>
            <tr>
                <td><?php echo $p['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($p['autor']); ?></td>
                <td><?php echo htmlspecialchars($p['area']); ?></td>
                <td><span class="status-badge status-<?php echo $p['status']; ?>"><?php echo $p['status']; ?></span></td>
                <td class="action-buttons">
                    <select onchange="cambiarEstado(<?php echo $p['id']; ?>, this.value)" class="form-select form-select-sm d-inline-block" style="width:auto;">
                        <option value="">Cambiar estado</option>
                        <option value="recibido">Recibido</option>
                        <option value="en_revision">En revisión</option>
                        <option value="correcciones">Correcciones</option>
                        <option value="aceptado">Aceptado</option>
                        <option value="rechazado">Rechazado</option>
                    </select>
                    <a href="<?php echo $p['file_path']; ?>" target="_blank" class="btn-accion btn-ver"><i class="fas fa-file-pdf"></i> PDF</a>
                    <a href="?eliminar=<?php echo $p['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar este proyecto?')"><i class="fas fa-trash"></i> Eliminar</a>
                </td>
            </tr><?php endforeach; ?></tbody>
        </table>
    </div>
</div>

<script>function cambiarEstado(id, estado){if(estado) window.location.href='?cambiar_estado=1&id='+id+'&estado='+estado;}</script>

<footer><p>&copy; 2027 TESCo Congress</p></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>