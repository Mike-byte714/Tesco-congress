<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../partials/header.php'; 

require_once '../../config/database.php';
$db = (new Database())->getConnection();

// ========== CRUD DE ACTIVIDADES ==========

// Crear actividad
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'crear') {
    $day = $_POST['day'];
    $time = $_POST['time'];
    $title = $_POST['title'];
    $activity_type = $_POST['activity_type'];
    $speaker_id = !empty($_POST['speaker_id']) ? $_POST['speaker_id'] : NULL;
    
    $stmt = $db->prepare("INSERT INTO schedule (day, time, title, activity_type, speaker_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$day, $time, $title, $activity_type, $speaker_id]);
    header("Location: programa.php?msg=creado");
    exit;
}

// Editar actividad
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'editar') {
    $id = $_POST['id'];
    $day = $_POST['day'];
    $time = $_POST['time'];
    $title = $_POST['title'];
    $activity_type = $_POST['activity_type'];
    $speaker_id = !empty($_POST['speaker_id']) ? $_POST['speaker_id'] : NULL;
    
    $stmt = $db->prepare("UPDATE schedule SET day=?, time=?, title=?, activity_type=?, speaker_id=? WHERE id=?");
    $stmt->execute([$day, $time, $title, $activity_type, $speaker_id, $id]);
    header("Location: programa.php?msg=editado");
    exit;
}

// Eliminar actividad
if(isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->prepare("DELETE FROM schedule WHERE id = ?")->execute([$id]);
    header("Location: programa.php?msg=eliminado");
    exit;
}

// Obtener actividad para editar
$actividad_editar = null;
if(isset($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM schedule WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $actividad_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Listar actividades (con nombre del ponente)
$actividades = $db->query("
    SELECT s.*, sp.name as speaker_name 
    FROM schedule s 
    LEFT JOIN speakers sp ON s.speaker_id = sp.id 
    ORDER BY s.day, s.time
")->fetchAll();

// Obtener lista de ponentes para el select
$ponentes = $db->query("SELECT id, name FROM speakers ORDER BY name")->fetchAll();
?>

<style>
    .btn-accion { padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; margin: 2px; display: inline-block; text-decoration: none; }
    .btn-editar { background: #f4b400; color: #003366; }
    .btn-editar:hover { background: #e6a800; color: #003366; }
    .btn-eliminar { background: #dc2626; color: white; }
    .btn-eliminar:hover { background: #b91c1c; color: white; }
    .btn-agregar { background: #2e7d32; color: white; padding: 8px 20px; border-radius: 25px; border: none; }
    .btn-agregar:hover { background: #1b5e20; }
    .modal-content { border-radius: 20px; }
    .table-programa { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .tipo-badge { padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
    .tipo-Conferencia { background: #dbeafe; color: #2563eb; }
    .tipo-Taller { background: #fef3c7; color: #d97706; }
    .tipo-Keynote { background: #dcfce7; color: #059669; }
    .tipo-Mesa { background: #f3e8ff; color: #9333ea; }
    .tipo-Inauguración { background: #ffedd5; color: #ea580c; }
    .tipo-Clausura { background: #fed7aa; color: #c2410c; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #003366;"><i class="fas fa-calendar-alt"></i> Programa del Evento</h2>
        <div>
            <a href="dashboard.php" class="btn btn-secondary me-2"><i class="fas fa-arrow-left"></i> Volver</a>
            <button class="btn-agregar" data-bs-toggle="modal" data-bs-target="#modalCrear"><i class="fas fa-plus"></i> Nueva Actividad</button>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg'] == 'creado'): ?>
            <div class="alert alert-success">✅ Actividad creada correctamente.</div>
        <?php elseif($_GET['msg'] == 'editado'): ?>
            <div class="alert alert-success">✏️ Actividad editada correctamente.</div>
        <?php elseif($_GET['msg'] == 'eliminado'): ?>
            <div class="alert alert-success">🗑️ Actividad eliminada correctamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Tabla de actividades -->
    <div class="table-responsive table-programa">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Título</th>
                    <th>Tipo</th>
                    <th>Ponente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($actividades as $a): ?>
                <tr>
                    <td><?php echo $a['id']; ?></td>
                    <td><?php echo date('d/m/Y', strtotime($a['day'])); ?></td>
                    <td><?php echo substr($a['time'], 0, 5); ?></td>
                    <td><strong><?php echo htmlspecialchars($a['title']); ?></strong></td>
                    <td>
                        <span class="tipo-badge tipo-<?php echo str_replace(' ', '', $a['activity_type']); ?>">
                            <?php echo htmlspecialchars($a['activity_type']); ?>
                        </span>
                    </td>
                    <td><?php echo htmlspecialchars($a['speaker_name'] ?? '—'); ?></td>
                    <td>
                        <a href="?editar=<?php echo $a['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i> Editar</a>
                        <a href="?eliminar=<?php echo $a['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar esta actividad?')"><i class="fas fa-trash"></i> Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if(empty($actividades)): ?>
        <div class="alert alert-info text-center mt-4">
            <i class="fas fa-info-circle"></i> No hay actividades programadas aún. ¡Agrega la primera!
        </div>
    <?php endif; ?>
</div>

<!-- MODAL CREAR ACTIVIDAD -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #003366; color: white;">
                <h5 class="modal-title"><i class="fas fa-plus-circle"></i> Nueva Actividad</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="crear">
                    <div class="mb-3">
                        <label>📅 Fecha</label>
                        <input type="date" name="day" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>⏰ Hora</label>
                        <input type="time" name="time" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>📌 Título de la actividad</label>
                        <input type="text" name="title" class="form-control" placeholder="Ej: Conferencia magistral" required>
                    </div>
                    <div class="mb-3">
                        <label>🎭 Tipo</label>
                        <select name="activity_type" class="form-control" required>
                            <option value="Conferencia">🎤 Conferencia</option>
                            <option value="Taller">🛠️ Taller</option>
                            <option value="Keynote">👑 Keynote</option>
                            <option value="Mesa redonda">🔄 Mesa redonda</option>
                            <option value="Inauguración">🎉 Inauguración</option>
                            <option value="Clausura">🏁 Clausura</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>🎙️ Ponente (opcional)</label>
                        <select name="speaker_id" class="form-control">
                            <option value="">-- Seleccionar ponente --</option>
                            <?php foreach($ponentes as $p): ?>
                                <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background: #2e7d32; color: white;">Guardar Actividad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR ACTIVIDAD -->
<?php if($actividad_editar): ?>
<div class="modal fade show" id="modalEditar" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #003366; color: white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Actividad</h5>
                <a href="programa.php" class="btn-close btn-close-white"></a>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="editar">
                    <input type="hidden" name="id" value="<?php echo $actividad_editar['id']; ?>">
                    <div class="mb-3">
                        <label>📅 Fecha</label>
                        <input type="date" name="day" class="form-control" value="<?php echo $actividad_editar['day']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>⏰ Hora</label>
                        <input type="time" name="time" class="form-control" value="<?php echo $actividad_editar['time']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>📌 Título</label>
                        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($actividad_editar['title']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>🎭 Tipo</label>
                        <select name="activity_type" class="form-control" required>
                            <option value="Conferencia" <?php echo $actividad_editar['activity_type'] == 'Conferencia' ? 'selected' : ''; ?>>🎤 Conferencia</option>
                            <option value="Taller" <?php echo $actividad_editar['activity_type'] == 'Taller' ? 'selected' : ''; ?>>🛠️ Taller</option>
                            <option value="Keynote" <?php echo $actividad_editar['activity_type'] == 'Keynote' ? 'selected' : ''; ?>>👑 Keynote</option>
                            <option value="Mesa redonda" <?php echo $actividad_editar['activity_type'] == 'Mesa redonda' ? 'selected' : ''; ?>>🔄 Mesa redonda</option>
                            <option value="Inauguración" <?php echo $actividad_editar['activity_type'] == 'Inauguración' ? 'selected' : ''; ?>>🎉 Inauguración</option>
                            <option value="Clausura" <?php echo $actividad_editar['activity_type'] == 'Clausura' ? 'selected' : ''; ?>>🏁 Clausura</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>🎙️ Ponente</label>
                        <select name="speaker_id" class="form-control">
                            <option value="">-- Ninguno --</option>
                            <?php foreach($ponentes as $p): ?>
                                <option value="<?php echo $p['id']; ?>" <?php echo $actividad_editar['speaker_id'] == $p['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="programa.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn" style="background: #2e7d32; color: white;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include '../partials/footer.php'; ?>