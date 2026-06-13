<?php 
session_start();
require_once '../config/database.php';
$db = (new Database())->getConnection();

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "SELECT * FROM projects WHERE status='aceptado' AND (title LIKE :search OR authors LIKE :search OR institution LIKE :search) ORDER BY submitted_at DESC";
$stmt = $db->prepare($sql);
$stmt->execute(['search' => "%$search%"]);
$articulos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biblioteca - TESCo 2027</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f8fafc; }
        .header { background: #003366; color: white; padding: 15px 0; }
        .header a { color: white; text-decoration: none; }
        .card-articulo { background: white; border-radius: 16px; padding: 1.5rem; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .btn-descargar { background: #2e7d32; color: white; padding: 8px 20px; border-radius: 25px; text-decoration: none; }
        .btn-descargar:hover { background: #1b5e20; color: white; }
        .badge-area { background: #003366; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; }
        .search-box { max-width: 500px; margin: 0 auto 2rem; }
        footer { background: #001f3f; color: white; text-align: center; padding: 20px; margin-top: 40px; }
    </style>
</head>
<body>

<div class="header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="m-0">📚 TESCo Congress 2027 - Biblioteca</h2>
            <div>
                <a href="/views/index.php" class="btn btn-outline-light">← Volver al inicio</a>
            </div>
        </div>
    </div>
</div>

<div class="container my-4">
    <div class="text-center mb-4">
        <h3 style="color: #003366;">Memorias del Congreso</h3>
    </div>

    <div class="search-box">
        <form method="GET" action="">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Buscar por título, autor o institución..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit" class="btn" style="background: #003366; color: white;"><i class="fas fa-search"></i> Buscar</button>
            </div>
        </form>
    </div>

    <?php if(empty($articulos)): ?>
        <div class="alert alert-info text-center">No hay artículos publicados aún.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach($articulos as $a): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card-articulo">
                        <span class="badge-area"><?php echo htmlspecialchars($a['area']); ?></span>
                        <h5 class="mt-2" style="color: #003366;"><?php echo htmlspecialchars($a['title']); ?></h5>
                        <p class="text-muted small">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($a['authors']); ?><br>
                            <i class="fas fa-building"></i> <?php echo htmlspecialchars($a['institution']); ?>
                        </p>
                        <a href="<?php echo $a['file_path']; ?>" target="_blank" class="btn-descargar"><i class="fas fa-download"></i> Descargar PDF</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<footer>
    <p>&copy; 2027 Congreso Internacional TESCo</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>