<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../partials/header.php'; 

require_once '../../config/database.php';
$db = (new Database())->getConnection();

$proyectos = $db->query("SELECT p.*, u.fullname as autor FROM projects p JOIN users u ON p.user_id = u.id ORDER BY p.submitted_at DESC")->fetchAll();

if(isset($_GET['action']) && $_GET['action'] == 'cambiar_estado' && isset($_GET['id']) && isset($_GET['status'])) {
    $id = $_GET['id'];
    $status = $_GET['status'];
    $db->prepare("UPDATE projects SET status = ? WHERE id = ?")->execute([$status, $id]);
    header("Location: proyectos.php?msg=ok");
    exit;
}

if(isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->prepare("DELETE FROM projects WHERE id = ?")->execute([$id]);
    header("Location: proyectos.php?msg=delete");
    exit;
}
?>

<style>
    .status-recibido { background: #fef3c7; color: #d97706; padding: 4px 10px; border-radius: 20px; }
    .status-en_revision { background: #dbeafe; color: #2563eb; padding: 4px 10px; border-radius: 20px; }
    .status-aceptado { background: #d1fae5; color: #059669; padding: 4px 10px; border-radius: 20px; }
    .status-rechazado { background: #fee2e2; color: #dc2626; padding: 4px 10px; border-radius: 20px; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #003366;"><i class="fas fa-file-alt"></i> Gestión de Proyectos</h2>
        <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success">Estado actualizado correctamente.</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr><th>ID</th><th>Título</th><th>Autor</th><th>Área</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php foreach($proyectos as $p): ?>
                <tr>
                    <td><?php echo $p['id']; ?></td>
                    <td><?php echo htmlspecialchars($p['title']); ?></td>
                    <td><?php echo htmlspecialchars($p['autor']); ?></td>
                    <td><?php echo htmlspecialchars($p['area']); ?></td>
                    <td><span class="status-<?php echo $p['status']; ?>"><?php echo $p['status']; ?></span></td>
                    <td>
                        <select onchange="cambiarEstado(<?php echo $p['id']; ?>, this.value)" class="form-select form-select-sm d-inline-block w-auto">
                            <option value="">Cambiar</option>
                            <option value="recibido">Recibido</option>
                            <option value="en_revision">En revisión</option>
                            <option value="correcciones">Correcciones</option>
                            <option value="aceptado">Aceptado</option>
                            <option value="rechazado">Rechazado</option>
                        </select>
                        <a href="<?php echo $p['file_path']; ?>" target="_blank" class="btn btn-info btn-sm">PDF</a>
                        <a href="?eliminar=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Eliminar?')">🗑️</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function cambiarEstado(id, status) {
    if(status) window.location.href = '?action=cambiar_estado&id=' + id + '&status=' + status;
}
</script>

<?php include '../partials/footer.php'; ?>