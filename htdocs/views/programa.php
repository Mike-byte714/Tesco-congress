<?php 
session_start();
require_once '../config/database.php';
$db = (new Database())->getConnection();

// Obtener actividades con nombre del ponente
$stmt = $db->query("
    SELECT s.*, sp.name as speaker_name 
    FROM schedule s 
    LEFT JOIN speakers sp ON s.speaker_id = sp.id 
    ORDER BY s.day, s.time
");
$actividades = $stmt->fetchAll();

include 'partials/header.php'; 
?>

<style>
    .programa-wrapper { padding: 2rem 0; }
    .page-title { text-align: center; margin-bottom: 2rem; }
    .page-title h2 { color: #003366; font-weight: 700; }
    .page-title p { color: #2e7d32; }
    
    .filtro-dia { max-width: 300px; margin: 0 auto 2rem; }
    
    .card-actividad {
        background: white;
        border-radius: 16px;
        padding: 1.2rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-left: 4px solid #003366;
        transition: all 0.3s;
    }
    .card-actividad:hover {
        transform: translateX(5px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .actividad-hora {
        font-weight: 700;
        color: #003366;
        font-size: 1rem;
    }
    .actividad-titulo {
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
    }
    .actividad-tipo {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-top: 8px;
    }
    .tipo-Conferencia { background: #dbeafe; color: #2563eb; }
    .tipo-Taller { background: #fef3c7; color: #d97706; }
    .tipo-Keynote { background: #dcfce7; color: #059669; }
    .tipo-Mesa { background: #f3e8ff; color: #9333ea; }
    .tipo-Inauguración { background: #ffedd5; color: #ea580c; }
    .tipo-Clausura { background: #fed7aa; color: #c2410c; }
    .actividad-ponente { color: #64748b; font-size: 0.8rem; margin-top: 8px; }
    
    .fecha-separador {
        background: #003366;
        color: white;
        padding: 6px 16px;
        border-radius: 30px;
        display: inline-block;
        margin: 1.5rem 0 1rem;
        font-weight: 600;
    }
</style>

<div class="programa-wrapper">
    <div class="container">
        <div class="page-title">
            <h2><i class="fas fa-calendar-alt"></i> Programa del Congreso</h2>
            <p>Conoce el cronograma de actividades del TESCo 2027</p>
        </div>

        <!-- Filtro por día -->
        <div class="filtro-dia">
            <select id="dayFilter" class="form-select">
                <option value="all">📅 Todos los días</option>
                <?php 
                $days = array_unique(array_column($actividades, 'day'));
                sort($days);
                foreach($days as $d): 
                    $fecha_legible = date('d/m/Y', strtotime($d));
                    echo "<option value='$d'>📌 $fecha_legible</option>";
                endforeach; 
                ?>
            </select>
        </div>

        <div id="scheduleList">
            <?php if(empty($actividades)): ?>
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle"></i> El programa se publicará próximamente.
                </div>
            <?php else: ?>
                <?php 
                $fecha_actual = '';
                foreach($actividades as $a): 
                    $fecha = $a['day'];
                    if($fecha != $fecha_actual):
                        $fecha_actual = $fecha;
                ?>
                    <div class="text-center">
                        <span class="fecha-separador">
                            <i class="fas fa-calendar-day"></i> <?php echo date('d/m/Y', strtotime($fecha)); ?>
                        </span>
                    </div>
                <?php endif; ?>
                
                <div class="card-actividad" data-day="<?php echo $a['day']; ?>">
                    <div class="row align-items-center">
                        <div class="col-md-2">
                            <div class="actividad-hora">
                                <i class="fas fa-clock"></i> <?php echo substr($a['time'], 0, 5); ?>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <h5 class="actividad-titulo"><?php echo htmlspecialchars($a['title']); ?></h5>
                            <?php if(!empty($a['speaker_name'])): ?>
                                <div class="actividad-ponente">
                                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($a['speaker_name']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-3 text-md-end">
                            <span class="actividad-tipo tipo-<?php echo str_replace(' ', '', $a['activity_type']); ?>">
                                <?php echo htmlspecialchars($a['activity_type']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('dayFilter').addEventListener('change', function() {
    let val = this.value;
    document.querySelectorAll('#scheduleList .card-actividad').forEach(card => {
        if(val === 'all' || card.getAttribute('data-day') === val) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>

<?php include 'partials/footer.php'; ?>