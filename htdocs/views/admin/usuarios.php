<?php 
// ============================================================
// GESTIÓN DE USUARIOS - CRUD COMPLETO CON RESPONSIVE
// ============================================================
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: /views/login.php");
    exit;
}

require_once '../../config/database.php';
$db = (new Database())->getConnection();

// ============================================================
// PROCESAR ELIMINACIÓN DE USUARIO
// ============================================================
if(isset($_GET['eliminar'])) {
    $id = (int)$_GET['eliminar'];
    $db->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
    header("Location: usuarios.php?msg=eliminado");
    exit;
}

// ============================================================
// PROCESAR CREACIÓN DE USUARIO
// ============================================================
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['crear'])) {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $institution = $_POST['institution'] ?? '';
    $country = $_POST['country'] ?? '';
    $role = $_POST['role'];
    
    $stmt = $db->prepare("INSERT INTO users (fullname, email, password, institution, country, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$fullname, $email, $password, $institution, $country, $role]);
    header("Location: usuarios.php?msg=creado");
    exit;
}

// ============================================================
// PROCESAR EDICIÓN DE USUARIO
// ============================================================
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $institution = $_POST['institution'] ?? '';
    $country = $_POST['country'] ?? '';
    $role = $_POST['role'];
    
    $stmt = $db->prepare("UPDATE users SET fullname=?, email=?, institution=?, country=?, role=? WHERE id=?");
    $stmt->execute([$fullname, $email, $institution, $country, $role, $id]);
    
    // Si se envió nueva contraseña, la actualizamos
    if(!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $db->prepare("UPDATE users SET password=? WHERE id=?")->execute([$password, $id]);
    }
    header("Location: usuarios.php?msg=editado");
    exit;
}

// ============================================================
// OBTENER USUARIO PARA EDITAR (si viene por GET)
// ============================================================
$usuario_editar = null;
if(isset($_GET['editar'])) {
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['editar']]);
    $usuario_editar = $stmt->fetch();
}

// ============================================================
// LISTAR TODOS LOS USUARIOS
// ============================================================
$usuarios = $db->query("SELECT * FROM users ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Usuarios - TESCo Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ============================================ */
        /* ESTILOS PRINCIPALES                          */
        /* ============================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Header sencillo */
        .admin-header {
            background: #0f766e;
            color: white;
            padding: 15px 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .admin-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .admin-header h2 { margin: 0; font-size: 1.5rem; }
        .admin-header a { color: white; text-decoration: none; margin-left: 20px; }
        .admin-header a:hover { text-decoration: underline; }
        
        /* Botones */
        .btn-accion {
            padding: 6px 14px;
            border-radius: 25px;
            font-size: 0.75rem;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s;
        }
        .btn-editar { background: #f4b400; color: #003366; }
        .btn-editar:hover { background: #e6a800; transform: translateY(-1px); }
        .btn-eliminar { background: #dc2626; color: white; }
        .btn-eliminar:hover { background: #b91c1c; transform: translateY(-1px); }
        .btn-agregar {
            background: #2e7d32;
            color: white;
            padding: 10px 24px;
            border-radius: 30px;
            border: none;
            font-weight: 600;
            margin-bottom: 20px;
            transition: all 0.2s;
        }
        .btn-agregar:hover { background: #1b5e20; transform: translateY(-2px); }
        .btn-secondary { background: #64748b; color: white; padding: 8px 20px; border-radius: 25px; text-decoration: none; display: inline-block; transition: all 0.2s; }
        .btn-secondary:hover { background: #475569; transform: translateY(-1px); }
        
        /* Tarjeta de formulario */
        .form-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.05);
            border-left: 5px solid #2e7d32;
        }
        .form-card h4 {
            color: #0f766e;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 1.3rem;
        }
        .form-card label {
            color: #003366;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            font-size: 0.85rem;
        }
        .form-card input, .form-card select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        .form-card input:focus, .form-card select:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 0 3px rgba(46,125,50,0.1);
        }
        
        /* Tabla */
        .table-panel {
            background: white;
            border-radius: 20px;
            overflow-x: auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .table-panel table {
            width: 100%;
            min-width: 650px;
            border-collapse: collapse;
        }
        .table-panel th {
            background: #0f766e;
            color: white;
            padding: 14px 12px;
            font-weight: 600;
            text-align: left;
        }
        .table-panel td {
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        /* Footer */
        footer { background: #0f172a; color: white; text-align: center; padding: 25px; margin-top: 50px; font-size: 0.85rem; }
        
        /* ============================================ */
        /* RESPONSIVE - MÓVIL Y TABLET                   */
        /* ============================================ */
        
        /* Tablet (768px - 1024px) */
        @media (max-width: 1024px) {
            .admin-header .container { padding: 0 15px; }
            .admin-header h2 { font-size: 1.3rem; }
        }
        
        /* Móvil (hasta 768px) */
        @media (max-width: 768px) {
            body { padding: 0; }
            
            /* Header en columna */
            .admin-header .container {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .admin-header h2 { font-size: 1.2rem; }
            .admin-header div { display: flex; gap: 15px; justify-content: center; }
            .admin-header a { margin: 0; }
            
            /* Contenedor principal */
            .container.py-3 { padding: 15px !important; }
            
            /* Cabecera de página en columna */
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            .d-flex.justify-content-between h2 { font-size: 1.4rem; margin: 0; }
            
            /* Botón agregar a ancho completo */
            .btn-agregar { width: 100%; text-align: center; }
            
            /* Formularios en columna */
            .form-card { padding: 18px; }
            .form-card .row { display: flex; flex-direction: column; }
            .form-card .col-md-6 { width: 100%; margin-bottom: 15px; }
            .form-card .col-md-12 { display: flex; flex-direction: column; gap: 10px; }
            .form-card .col-md-12 .btn { width: 100%; margin: 5px 0; }
            
            /* Botones del formulario */
            .form-card button[type="submit"], 
            .form-card button[type="button"] { width: 100%; margin-bottom: 8px; }
            
            /* Tabla con scroll horizontal */
            .table-panel { overflow-x: auto; -webkit-overflow-scrolling: touch; }
            .table-panel table { min-width: 600px; font-size: 0.8rem; }
            .table-panel th, .table-panel td { padding: 10px 8px; }
            
            /* Botones de acción en columna */
            .action-buttons { flex-direction: column; gap: 6px; }
            .btn-accion { width: 100%; justify-content: center; padding: 8px; font-size: 0.7rem; }
            
            /* Botón volver */
            .btn-secondary { width: 100%; text-align: center; display: block; }
        }
        
        /* Móvil muy pequeño (hasta 480px) */
        @media (max-width: 480px) {
            .container.py-3 { padding: 10px !important; }
            .form-card { padding: 15px; }
            .table-panel table { min-width: 500px; font-size: 0.7rem; }
            .table-panel th, .table-panel td { padding: 8px 6px; }
            h2 { font-size: 1.2rem; }
        }
    </style>
</head>
<body>

<!-- ============================================================ -->
<!-- HEADER SENCILLO -->
<!-- ============================================================ -->
<header class="admin-header">
    <div class="container">
        <h2><i class="fas fa-users"></i> TESCo Admin - Usuarios</h2>
        <div>
            <a href="/views/index.php"><i class="fas fa-home"></i> Inicio</a>
            <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="/controllers/LogoutController.php"><i class="fas fa-sign-out-alt"></i> Salir</a>
        </div>
    </div>
</header>

<!-- ============================================================ -->
<!-- CONTENIDO PRINCIPAL -->
<!-- ============================================================ -->
<div class="container py-3">
    
    <!-- Cabecera y botón volver -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 style="color: #0f766e;"><i class="fas fa-users"></i> Gestión de Usuarios</h2>
        <a href="dashboard.php" class="btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Dashboard</a>
    </div>

    <!-- Mensajes de éxito -->
    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success alert-dismissible fade show" style="border-radius: 16px; padding: 12px 20px;">
            <?php 
                if($_GET['msg'] == 'creado') echo '✅ Usuario creado correctamente.';
                elseif($_GET['msg'] == 'editado') echo '✏️ Usuario editado correctamente.';
                elseif($_GET['msg'] == 'eliminado') echo '🗑️ Usuario eliminado correctamente.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Botón para nuevo usuario -->
    <button class="btn-agregar" onclick="toggleForm()"><i class="fas fa-plus"></i> Nuevo Usuario</button>

    <!-- ============================================================ -->
    <!-- FORMULARIO PARA CREAR USUARIO (oculto inicialmente) -->
    <!-- ============================================================ -->
    <div id="formCrear" style="display:none;" class="form-card">
        <h4><i class="fas fa-user-plus"></i> Nuevo Usuario</h4>
        <form method="POST">
            <input type="hidden" name="crear" value="1">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-user"></i> Nombre completo</label>
                    <input type="text" name="fullname" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-envelope"></i> Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-lock"></i> Contraseña</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-building"></i> Institución</label>
                    <input type="text" name="institution" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-globe"></i> País</label>
                    <input type="text" name="country" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label><i class="fas fa-user-tag"></i> Rol</label>
                    <select name="role" class="form-control">
                        <option value="assistant">👥 Asistente</option>
                        <option value="speaker">🎤 Ponente</option>
                        <option value="reviewer">📋 Revisor</option>
                        <option value="admin">🔧 Administrador</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn" style="background: #2e7d32; color: white;"><i class="fas fa-save"></i> Guardar Usuario</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm()"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- FORMULARIO PARA EDITAR USUARIO (oculto inicialmente) -->
    <!-- ============================================================ -->
    <div id="formEditar" style="display:none;" class="form-card">
        <h4><i class="fas fa-edit"></i> Editar Usuario</h4>
        <form method="POST">
            <input type="hidden" name="editar" value="1">
            <input type="hidden" name="id" id="edit_id">
            <div class="row">
                <div class="col-md-6 mb-3"><label><i class="fas fa-user"></i> Nombre</label><input type="text" name="fullname" id="edit_fullname" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label><i class="fas fa-envelope"></i> Email</label><input type="email" name="email" id="edit_email" class="form-control" required></div>
                <div class="col-md-6 mb-3"><label><i class="fas fa-lock"></i> Nueva contraseña</label><input type="password" name="password" id="edit_password" class="form-control" placeholder="Opcional - dejar vacío para no cambiar"></div>
                <div class="col-md-6 mb-3"><label><i class="fas fa-building"></i> Institución</label><input type="text" name="institution" id="edit_institution" class="form-control"></div>
                <div class="col-md-6 mb-3"><label><i class="fas fa-globe"></i> País</label><input type="text" name="country" id="edit_country" class="form-control"></div>
                <div class="col-md-6 mb-3"><label><i class="fas fa-user-tag"></i> Rol</label>
                    <select name="role" id="edit_role" class="form-control">
                        <option value="assistant">👥 Asistente</option>
                        <option value="speaker">🎤 Ponente</option>
                        <option value="reviewer">📋 Revisor</option>
                        <option value="admin">🔧 Administrador</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn" style="background: #2e7d32; color: white;"><i class="fas fa-save"></i> Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" onclick="cerrarEdicion()"><i class="fas fa-times"></i> Cancelar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- ============================================================ -->
    <!-- TABLA DE USUARIOS CON SCROLL HORIZONTAL EN MÓVIL -->
    <!-- ============================================================ -->
    <div class="table-panel">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Institución</th>
                    <th>País</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($usuarios)): ?>
                    <tr><td colspan="7" class="text-center py-4">No hay usuarios registrados. ¡Agrega el primero!</td></tr>
                <?php else: ?>
                    <?php foreach($usuarios as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($u['fullname']); ?></strong></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td><?php echo $u['role']; ?></td>
                        <td><?php echo htmlspecialchars($u['institution']); ?></td>
                        <td><?php echo htmlspecialchars($u['country']); ?></td>
                        <td class="action-buttons">
                            <button class="btn-accion btn-editar" onclick='editar(<?php echo $u['id']; ?>,"<?php echo addslashes($u['fullname']); ?>","<?php echo addslashes($u['email']); ?>","<?php echo addslashes($u['institution']); ?>","<?php echo addslashes($u['country']); ?>","<?php echo $u['role']; ?>")'>
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn-accion btn-eliminar" onclick="eliminarUsuario(<?php echo $u['id']; ?>)">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================================ -->
<!-- SCRIPTS -->
<!-- ============================================================ -->
<script>
// Mostrar/ocultar formulario de creación
function toggleForm() {
    var f = document.getElementById('formCrear');
    f.style.display = f.style.display === 'none' ? 'block' : 'none';
}

// Cargar datos en formulario de edición
function editar(id, fullname, email, institution, country, role) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_fullname').value = fullname;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_institution').value = institution || '';
    document.getElementById('edit_country').value = country || '';
    document.getElementById('edit_role').value = role;
    document.getElementById('edit_password').value = '';
    document.getElementById('formEditar').style.display = 'block';
    window.scrollTo({ top: document.getElementById('formEditar').offsetTop - 20, behavior: 'smooth' });
}

// Cerrar formulario de edición
function cerrarEdicion() {
    document.getElementById('formEditar').style.display = 'none';
}

// Eliminar usuario con confirmación
function eliminarUsuario(id) {
    if(confirm('⚠️ ¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')) {
        window.location.href = '?eliminar=' + id;
    }
}
</script>

<!-- ============================================================ -->
<!-- FOOTER -->
<!-- ============================================================ -->
<footer>
    <p>&copy; 2027 TESCo Congress - Todos los derechos reservados</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>