<?php 
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}
include '../partials/header.php'; 

require_once '../../config/database.php';
$db = (new Database())->getConnection();

// ========== CRUD OPERATIONS ==========

// Agregar usuario
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'crear') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $institution = $_POST['institution'] ?? '';
    $country = $_POST['country'] ?? '';
    $role = $_POST['role'];
    
    $stmt = $db->prepare("INSERT INTO users (fullname, email, password, institution, country, role) VALUES (?, ?, ?, ?, ?, ?)");
    if($stmt->execute([$fullname, $email, $password, $institution, $country, $role])) {
        header("Location: usuarios.php?msg=creado");
    } else {
        header("Location: usuarios.php?error=crear");
    }
    exit;
}

// Editar usuario
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'editar') {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $institution = $_POST['institution'] ?? '';
    $country = $_POST['country'] ?? '';
    $role = $_POST['role'];
    
    $stmt = $db->prepare("UPDATE users SET fullname=?, email=?, institution=?, country=?, role=? WHERE id=?");
    $stmt->execute([$fullname, $email, $institution, $country, $role, $id]);
    
    // Si se envió nueva contraseña
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$password, $id]);
    }
    
    header("Location: usuarios.php?msg=editado");
    exit;
}

// Eliminar usuario
if(isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: usuarios.php?msg=eliminado");
    exit;
}

// Obtener usuario para editar
$usuario_editar = null;
if(isset($_GET['editar']) && is_numeric($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $usuario_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Listar usuarios
$usuarios = $db->query("SELECT * FROM users ORDER BY id")->fetchAll();
?>

<style>
    .btn-accion { padding: 4px 10px; border-radius: 15px; font-size: 0.75rem; margin: 2px; display: inline-block; text-decoration: none; }
    .btn-editar { background: #f4b400; color: #003366; }
    .btn-editar:hover { background: #e6a800; color: #003366; }
    .btn-eliminar { background: #dc2626; color: white; }
    .btn-eliminar:hover { background: #b91c1c; color: white; }
    .btn-agregar { background: #2e7d32; color: white; padding: 8px 20px; border-radius: 25px; border: none; }
    .btn-agregar:hover { background: #1b5e20; }
    .modal-content { border-radius: 20px; }
</style>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #003366;"><i class="fas fa-users"></i> Gestión de Usuarios</h2>
        <div>
            <a href="dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
            <button class="btn btn-agregar" data-bs-toggle="modal" data-bs-target="#modalCrear"><i class="fas fa-plus"></i> Nuevo Usuario</button>
        </div>
    </div>

    <!-- Mensajes -->
    <?php if(isset($_GET['msg'])): ?>
        <?php if($_GET['msg'] == 'creado'): ?>
            <div class="alert alert-success">✅ Usuario creado correctamente.</div>
        <?php elseif($_GET['msg'] == 'editado'): ?>
            <div class="alert alert-success">✏️ Usuario editado correctamente.</div>
        <?php elseif($_GET['msg'] == 'eliminado'): ?>
            <div class="alert alert-success">🗑️ Usuario eliminado correctamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if(isset($_GET['error'])): ?>
        <div class="alert alert-danger">❌ Error al procesar la solicitud.</div>
    <?php endif; ?>

    <!-- Tabla de usuarios -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Institución</th>
                    <th>País</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id']; ?></td>
                    <td><?php echo htmlspecialchars($u['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td>
                        <?php 
                            $roles = ['admin'=>'🔧 Admin', 'assistant'=>'👤 Asistente', 'speaker'=>'🎤 Ponente', 'reviewer'=>'📋 Revisor'];
                            echo $roles[$u['role']] ?? $u['role'];
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($u['institution']); ?></td>
                    <td><?php echo htmlspecialchars($u['country']); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                    <td>
                        <a href="?editar=<?php echo $u['id']; ?>" class="btn-accion btn-editar"><i class="fas fa-edit"></i> Editar</a>
                        <a href="?eliminar=<?php echo $u['id']; ?>" class="btn-accion btn-eliminar" onclick="return confirm('¿Eliminar este usuario?')"><i class="fas fa-trash"></i> Eliminar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- MODAL CREAR USUARIO -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #003366; color: white;">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Usuario</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="crear">
                    <div class="mb-3"><label>Nombre completo</label><input type="text" name="fullname" class="form-control" required></div>
                    <div class="mb-3"><label>Correo electrónico</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Contraseña</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3"><label>Institución</label><input type="text" name="institution" class="form-control"></div>
                    <div class="mb-3"><label>País</label><input type="text" name="country" class="form-control"></div>
                    <div class="mb-3">
                        <label>Rol</label>
                        <select name="role" class="form-control">
                            <option value="assistant">Asistente</option>
                            <option value="speaker">Ponente</option>
                            <option value="reviewer">Revisor</option>
                            <option value="admin">Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn" style="background: #2e7d32; color: white;">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDITAR USUARIO -->
<?php if($usuario_editar): ?>
<div class="modal fade show" id="modalEditar" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #003366; color: white;">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Usuario</h5>
                <a href="usuarios.php" class="btn-close btn-close-white"></a>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="editar">
                    <input type="hidden" name="id" value="<?php echo $usuario_editar['id']; ?>">
                    <div class="mb-3"><label>Nombre completo</label><input type="text" name="fullname" class="form-control" value="<?php echo htmlspecialchars($usuario_editar['fullname']); ?>" required></div>
                    <div class="mb-3"><label>Correo electrónico</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario_editar['email']); ?>" required></div>
                    <div class="mb-3"><label>Nueva contraseña (dejar vacío para no cambiar)</label><input type="password" name="password" class="form-control"></div>
                    <div class="mb-3"><label>Institución</label><input type="text" name="institution" class="form-control" value="<?php echo htmlspecialchars($usuario_editar['institution']); ?>"></div>
                    <div class="mb-3"><label>País</label><input type="text" name="country" class="form-control" value="<?php echo htmlspecialchars($usuario_editar['country']); ?>"></div>
                    <div class="mb-3">
                        <label>Rol</label>
                        <select name="role" class="form-control">
                            <option value="assistant" <?php echo $usuario_editar['role']=='assistant'?'selected':''; ?>>Asistente</option>
                            <option value="speaker" <?php echo $usuario_editar['role']=='speaker'?'selected':''; ?>>Ponente</option>
                            <option value="reviewer" <?php echo $usuario_editar['role']=='reviewer'?'selected':''; ?>>Revisor</option>
                            <option value="admin" <?php echo $usuario_editar['role']=='admin'?'selected':''; ?>>Administrador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn" style="background: #2e7d32; color: white;">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php include '../partials/footer.php'; ?>