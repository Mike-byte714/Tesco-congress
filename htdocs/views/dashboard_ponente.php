<?php 
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'speaker') {
    header("Location: login.php");
    exit;
}
include 'partials/header.php'; 

require_once '../config/database.php';
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

// Obtener datos del ponente
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Obtener ponencias del ponente (si existe en tabla speakers)
$stmt = $db->prepare("SELECT * FROM speakers WHERE email = ?");
$stmt->execute([$user['email']]);
$ponente_info = $stmt->fetch();
?>

<style>
    .ponente-wrapper { background: #f8fafc; min-height: calc(100vh - 200px); padding: 2rem 0; }
    .welcome-card { background: linear-gradient(135deg, #0f766e, #0a5c55); border-radius: 20px; padding: 1.5rem; margin-bottom: 2rem; color: white; }
    .btn-ponente { background: #2e7d32; color: white; padding: 10px 24px; border-radius: 25px; border: none; font-weight: 600; transition: 0.3s; }
    .btn-ponente:hover { background: #1b5e20; transform: translateY(-2px); }
    .info-card { background: white; border-radius: 20px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 4px solid #f4b400; }
</style>

<div class="ponente-wrapper">
    <div class="container">
        <div class="welcome-card">
            <h3><i class="fas fa-microphone-alt"></i> Panel del Ponente</h3>
            <p>Bienvenido, <?php echo $_SESSION['fullname']; ?>. Aquí puedes gestionar tu información como ponente.</p>
        </div>

        <?php if(!$ponente_info): ?>
            <div class="info-card">
                <h4><i class="fas fa-user-plus"></i> Completa tu perfil de ponente</h4>
                <p>Para aparecer en la sección de ponentes, completa tu información profesional.</p>
                <button class="btn-ponente" data-bs-toggle="modal" data-bs-target="#modalCompletarPerfil">
                    <i class="fas fa-edit"></i> Completar perfil
                </button>
            </div>
        <?php else: ?>
            <div class="info-card">
                <h4><i class="fas fa-check-circle"></i> Tu información como ponente</h4>
                <p><strong>Institución:</strong> <?php echo htmlspecialchars($ponente_info['institution']); ?></p>
                <p><strong>País:</strong> <?php echo htmlspecialchars($ponente_info['country']); ?></p>
                <p><strong>Título de conferencia:</strong> <?php echo htmlspecialchars($ponente_info['conference_title']); ?></p>
                <p><strong>Biografía:</strong> <?php echo htmlspecialchars($ponente_info['bio']); ?></p>
                <button class="btn-ponente" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">
                    <i class="fas fa-edit"></i> Editar perfil
                </button>
            </div>
        <?php endif; ?>

        <div class="info-card">
            <h4><i class="fas fa-calendar-alt"></i> Tus próximas actividades</h4>
            <p>Las conferencias y talleres asignados aparecerán aquí.</p>
            <a href="programa.php" class="btn-ponente">Ver programa completo</a>
        </div>

        <div class="info-card">
            <h4><i class="fas fa-file-alt"></i> Tus proyectos</h4>
            <p>Gestiona los proyectos que has enviado al congreso.</p>
            <a href="enviar_proyecto.php" class="btn-ponente">Enviar nuevo proyecto</a>
            <a href="dashboard_usuario.php" class="btn-ponente" style="background:#64748b;">Ver mis proyectos</a>
        </div>
    </div>
</div>

<!-- Modal para completar perfil -->
<div class="modal fade" id="modalCompletarPerfil" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #0f766e; color: white;">
                <h5 class="modal-title">Completar perfil de ponente</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="../controllers/SpeakerProfileController.php">
                <div class="modal-body">
                    <input type="hidden" name="user_email" value="<?php echo $user['email']; ?>">
                    <div class="mb-3"><label>Institución</label><input type="text" name="institution" class="form-control" required></div>
                    <div class="mb-3"><label>País</label><input type="text" name="country" class="form-control" required></div>
                    <div class="mb-3"><label>Título de tu conferencia</label><input type="text" name="conference_title" class="form-control" required></div>
                    <div class="mb-3"><label>Biografía</label><textarea name="bio" rows="3" class="form-control"></textarea></div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="completar_perfil" class="btn" style="background:#2e7d32; color:white;">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>