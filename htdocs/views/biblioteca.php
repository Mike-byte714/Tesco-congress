<?php 
session_start();
require_once '../config/database.php';
$db = (new Database())->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM projects WHERE status='aceptado' AND (title LIKE :search OR authors LIKE :search OR institution LIKE :search) ORDER BY submitted_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🟢 IMPORTANTE: Usamos el header.php que ya tiene la lógica de admin
include 'partials/header.php'; 
?>

<!-- ========== CONTENIDO DE BIBLIOTECA ========== -->
<div class="biblioteca-wrapper">
    <div class="container">
        <div class="page-title">
            <h2><i class="fas fa-book"></i> Biblioteca Digital</h2>
            <p>Memorias del Congreso Internacional TESCo 2027</p>
        </div>

        <!-- Buscador -->
        <div class="search-box">
            <form method="GET" action="">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por título, autor o institución..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn" style="background: #003366; color: white;"><i class="fas fa-search"></i> Buscar</button>
                </div>
                <?php if($search): ?>
                    <div class="text-center mt-2">
                        <a href="/views/biblioteca.php" class="text-decoration-none small" style="color: #2e7d32;">Limpiar búsqueda</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Resultados -->
        <?php if(empty($articulos)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No hay artículos publicados aún.
                <?php if($search): ?>
                    <br>No se encontraron resultados para "<strong><?php echo htmlspecialchars($search); ?></strong>"
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach($articulos as $a): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card-articulo">
                            <span class="badge-area"><?php echo htmlspecialchars($a['area']); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($a['title']); ?></h5>
                            <div class="card-info">
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($a['authors']); ?>
                            </div>
                            <div class="card-info">
                                <i class="fas fa-building"></i> <?php echo htmlspecialchars($a['institution']); ?>
                            </div>
                            <div class="card-info">
                                <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y', strtotime($a['submitted_at'])); ?>
                            </div>
                            <a href="<?php echo $a['file_path']; ?>" target="_blank" class="btn-descargar">
                                <i class="fas fa-download"></i> Descargar PDF
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* 🎨 ESTILOS EXCLUSIVOS DE BIBLIOTECA (no afectan al header) */
    .biblioteca-wrapper { padding: 2rem 0; min-height: calc(100vh - 200px); }
    .page-title { text-align: center; margin-bottom: 2rem; }
    .page-title h2 { color: #003366; font-weight: 700; font-size: 2rem; }
    .page-title p { color: #2e7d32; font-weight: 500; }
    
    .search-box { max-width: 500px; margin: 0 auto 2rem; }
    .search-box .input-group input { border-radius: 50px 0 0 50px; border: 1.5px solid #e2e8f0; padding: 12px 20px; }
    .search-box .input-group button { border-radius: 0 50px 50px 0; }
    
    .card-articulo { background: white; border-radius: 20px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 8px 20px rgba(0,0,0,0.05); transition: all 0.3s ease; height: 100%; border: 1px solid rgba(0,51,102,0.08); }
    .card-articulo:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,51,102,0.1); }
    .badge-area { background: #003366; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; display: inline-block; margin-bottom: 12px; font-weight: 600; }
    .card-title { color: #003366; font-weight: 700; font-size: 1.2rem; margin-bottom: 0.75rem; }
    .card-info { color: #64748b; font-size: 0.85rem; margin-bottom: 0.5rem; }
    .card-info i { width: 20px; color: #2e7d32; }
    .btn-descargar { background: #2e7d32; color: white; border-radius: 40px; padding: 8px 20px; text-decoration: none; display: inline-block; font-weight: 600; font-size: 0.85rem; transition: all 0.3s; margin-top: 1rem; width: 100%; text-align: center; }
    .btn-descargar:hover { background: #1b5e20; transform: translateY(-2px); }
    
    @media (max-width: 768px) {
        .page-title h2 { font-size: 1.5rem; }
        .card-title { font-size: 1rem; }
    }
</style>

<?php include 'partials/footer.php'; ?>