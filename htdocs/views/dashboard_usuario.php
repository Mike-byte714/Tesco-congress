<?php 
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include 'partials/header.php'; 

require_once '../config/database.php';
$db = (new Database())->getConnection();

$user_id = $_SESSION['user_id'];

// Obtener proyectos del usuario
$stmt = $db->prepare("SELECT * FROM projects WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->execute([$user_id]);
$proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar proyectos
$totalProyectos = count($proyectos);
$proyectosAceptados = 0;
$proyectosRechazados = 0;
$proyectosPendientes = 0;

foreach($proyectos as $p) {
    if($p['status'] == 'aceptado') $proyectosAceptados++;
    elseif($p['status'] == 'rechazado') $proyectosRechazados++;
    else $proyectosPendientes++;
}
?>

<style>
    .dashboard-user-wrapper {
        background: #f8fafc;
        min-height: calc(100vh - 200px);
        padding: 2rem 0;
    }
    
    /* Tarjeta de bienvenida */
    .welcome-card-user {
        background: linear-gradient(135deg, #003366, #001a33);
        border-radius: 20px;
        padding: 1.8rem;
        margin-bottom: 2rem;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .welcome-text-user h3 {
        font-size: 1.4rem;
        margin-bottom: 0.25rem;
    }
    .welcome-text-user p {
        opacity: 0.9;
        margin: 0;
    }
    .welcome-icon-user {
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .welcome-icon-user i {
        font-size: 2rem;
    }
    
    /* Tarjetas de estadísticas */
    .stats-grid-user {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.2rem;
        margin-bottom: 2rem;
    }
    .stat-card-user {
        background: white;
        border-radius: 16px;
        padding: 1.2rem;
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }
    .stat-card-user:hover {
        transform: translateY(-3px);
    }
    .stat-number-user {
        font-size: 2rem;
        font-weight: 800;
        color: #003366;
    }
    .stat-label-user {
        color: #2e7d32;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    /* Tabla de proyectos */
    .proyectos-section {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .section-title {
        color: #003366;
        font-weight: 700;
        margin-bottom: 1.2rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #2e7d32;
        display: inline-block;
    }
    .btn-enviar {
        background: #2e7d32;
        color: white;
        padding: 10px 24px;
        border-radius: 40px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-enviar:hover {
        background: #1b5e20;
        transform: translateY(-2px);
        color: white;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
    }
    .status-recibido { background: #fef3c7; color: #d97706; }
    .status-en_revision { background: #dbeafe; color: #2563eb; }
    .status-correcciones { background: #fed7aa; color: #c2410c; }
    .status-aceptado { background: #d1fae5; color: #059669; }
    .status-rechazado { background: #fee2e2; color: #dc2626; }
    
    @media (max-width: 768px) {
        .stats-grid-user {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        .welcome-card-user {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }
        .proyectos-section {
            padding: 1rem;
        }
        .table-responsive {
            font-size: 0.8rem;
        }
    }
</style>

<div class="dashboard-user-wrapper">
    <div class="container">
        <!-- Tarjeta de bienvenida -->
        <div class="welcome-card-user">
            <div class="welcome-text-user">
                <h3><i class="fas fa-smile-wink"></i> ¡Hola, <?php echo htmlspecialchars($_SESSION['fullname']); ?>!</h3>
                <p>Bienvenido a tu panel de control del Congreso TESCo 2027</p>
            </div>
            <div class="welcome-icon-user">
                <i class="fas fa-user-circle"></i>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="stats-grid-user">
            <div class="stat-card-user">
                <div class="stat-number-user"><?php echo $totalProyectos; ?></div>
                <div class="stat-label-user">Proyectos enviados</div>
            </div>
            <div class="stat-card-user">
                <div class="stat-number-user"><?php echo $proyectosAceptados; ?></div>
                <div class="stat-label-user">Aceptados</div>
            </div>
            <div class="stat-card-user">
                <div class="stat-number-user"><?php echo $proyectosRechazados; ?></div>
                <div class="stat-label-user">Rechazados</div>
            </div>
            <div class="stat-card-user">
                <div class="stat-number-user"><?php echo $proyectosPendientes; ?></div>
                <div class="stat-label-user">Pendientes</div>
            </div>
        </div>

        <!-- Botón para enviar nuevo proyecto -->
        <div class="mb-4">
            <a href="enviar_proyecto.php" class="btn-enviar">
                <i class="fas fa-plus-circle"></i> Enviar nuevo proyecto
            </a>
        </div>

        <!-- Lista de proyectos -->
        <div class="proyectos-section">
            <h4 class="section-title"><i class="fas fa-file-alt"></i> Mis proyectos</h4>
            
            <?php if(empty($proyectos)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> Aún no has enviado ningún proyecto. 
                    <a href="enviar_proyecto.php" class="alert-link">¡Envía tu primer proyecto!</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Área</th>
                                <th>Fecha de envío</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </table>
                        </thead>
                        <tbody>
                            <?php foreach($proyectos as $p): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['area']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($p['submitted_at'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $p['status']; ?>">
                                        <?php 
                                            $estados = [
                                                'recibido'=>'📋 Recibido', 
                                                'en_revision'=>'🔄 En revisión', 
                                                'correcciones'=>'✏️ Correcciones', 
                                                'aceptado'=>'✅ Aceptado', 
                                                'rechazado'=>'❌ Rechazado'
                                            ];
                                            echo $estados[$p['status']];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if($p['file_path']): ?>
                                        <a href="<?php echo $p['file_path']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-file-pdf"></i> Ver PDF
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Información adicional -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="alert alert-info">
                    <i class="fas fa-lightbulb"></i> 
                    <strong>¿Sabías que?</strong> Los proyectos aceptados serán publicados en la Biblioteca Digital del congreso.
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-success">
                    <i class="fas fa-envelope"></i> 
                    <strong>¿Dudas?</strong> Contáctanos a <a href="mailto:correo@tesco.edu.mx">correo@tesco.edu.mx</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>