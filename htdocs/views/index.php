<?php 
session_start();
require_once '../config/database.php';
$db = (new Database())->getConnection();

// Cargar idioma
$lang = $_SESSION['lang'] ?? 'es';
$translations = [];
$lang_file = "../assets/lang/{$lang}.php";
if (file_exists($lang_file)) {
    $translations = include $lang_file;
}

// ============================================================
// ESTADÍSTICAS REALES DESDE LA BASE DE DATOS
// ============================================================
$totalParticipantes = $db->query("SELECT COUNT(*) FROM users WHERE role='assistant'")->fetchColumn();
$totalProyectos = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$totalPaises = $db->query("SELECT COUNT(DISTINCT country) FROM users WHERE country IS NOT NULL AND country != ''")->fetchColumn();
$totalInstituciones = $db->query("SELECT COUNT(DISTINCT institution) FROM users WHERE institution IS NOT NULL AND institution != ''")->fetchColumn();

// ============================================================
// PONENTES DESDE LA BASE DE DATOS
// ============================================================
$ponentes = $db->query("SELECT * FROM speakers ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

include 'partials/header.php'; 
?>

<!-- ============================================================ -->
<!-- CARRUSEL CON IMÁGENES REALES (cambia cada 5 segundos)       -->
<!-- ============================================================ -->
<div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <!-- Slide 1 - Conferencia / Audiencia -->
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1920&h=600&fit=crop" 
                 class="d-block w-100" 
                 alt="Conferencia internacional"
                 style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                <h1 class="display-4"><?php echo $translations['hero_slide1_title']; ?></h1>
                <p class="lead"><?php echo $translations['hero_subtitle']; ?></p>
                <a href="acceso.php" class="btn btn-warning btn-lg"><?php echo $translations['hero_btn']; ?></a>
            </div>
        </div>
        
        <!-- Slide 2 - Tecnología / Innovación -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1920&h=600&fit=crop" 
                 class="d-block w-100" 
                 alt="Innovación tecnológica"
                 style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                <h1 class="display-4"><?php echo $translations['hero_slide2_title']; ?></h1>
                <p class="lead"><?php echo $translations['hero_slide2_sub']; ?></p>
                <a href="acceso.php" class="btn btn-warning btn-lg"><?php echo $translations['hero_btn']; ?></a>
            </div>
        </div>
        
        <!-- Slide 3 - Educación / Estudiantes -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1920&h=600&fit=crop" 
                 class="d-block w-100" 
                 alt="Educación y estudiantes"
                 style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                <h1 class="display-4"><?php echo $translations['hero_slide3_title']; ?></h1>
                <p class="lead"><?php echo $translations['hero_slide3_sub']; ?></p>
                <a href="acceso.php" class="btn btn-warning btn-lg"><?php echo $translations['hero_btn']; ?></a>
            </div>
        </div>
    </div>
    
    <!-- Controles de navegación del carrusel -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>

<!-- ============================================================ -->
<!-- CUENTA REGRESIVA PARA EL EVENTO                              -->
<!-- ============================================================ -->
<section class="text-center my-5">
    <h2 class="mb-4" style="color: #0f766e;"><?php echo $translations['countdown_title']; ?></h2>
    <div id="countdown" class="row justify-content-center">
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-4 rounded-4 shadow-lg" style="background: #0f766e !important;">
                <span id="days" class="display-4 fw-bold">00</span><br>
                <span class="fs-5"><?php echo $translations['countdown_days']; ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-4 rounded-4 shadow-lg" style="background: #0f766e !important;">
                <span id="hours" class="display-4 fw-bold">00</span><br>
                <span class="fs-5"><?php echo $translations['countdown_hours']; ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-4 rounded-4 shadow-lg" style="background: #0f766e !important;">
                <span id="minutes" class="display-4 fw-bold">00</span><br>
                <span class="fs-5"><?php echo $translations['countdown_minutes']; ?></span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-4 rounded-4 shadow-lg" style="background: #0f766e !important;">
                <span id="seconds" class="display-4 fw-bold">00</span><br>
                <span class="fs-5"><?php echo $translations['countdown_seconds']; ?></span>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================ -->
<!-- MAPA CON DATOS REALES DE LA BASE DE DATOS                    -->
<!-- ============================================================ -->
<section class="my-5">
    <h2 class="text-center mb-4" style="color: #0f766e;"><?php echo $translations['map_title']; ?></h2>
    <div id="worldMap" style="height: 450px; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1);"></div>
</section>

<!-- ============================================================ -->
<!-- PONENTES INTERNACIONALES (DESDE LA BASE DE DATOS)           -->
<!-- ============================================================ -->
<section class="my-5">
    <h2 class="text-center mb-4" style="color: #0f766e;"><?php echo $translations['speakers_title']; ?></h2>
    <div class="row">
        <?php if(empty($ponentes)): ?>
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay ponentes registrados aún. Los ponentes aparecerán aquí.
                </div>
            </div>
        <?php else: ?>
            <?php foreach($ponentes as $p): ?>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4 h-100 shadow-sm border-0 rounded-4">
                    <div class="rounded-circle mx-auto bg-light d-flex align-items-center justify-content-center" style="width: 130px; height: 130px;">
                        <i class="fas fa-user-graduate fa-3x text-primary" style="color: #0f766e !important;"></i>
                    </div>
                    <h4 class="mt-3 fw-bold" style="color: #0f766e;"><?php echo htmlspecialchars($p['name']); ?></h4>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars($p['institution']); ?></p>
                    <p class="text-success"><?php echo htmlspecialchars($p['country']); ?></p>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPonente<?php echo md5($p['name']); ?>">
                        <?php echo $translations['speaker_biography']; ?>
                    </button>
                </div>
            </div>

            <!-- Modal de biografía del ponente -->
            <div class="modal fade" id="modalPonente<?php echo md5($p['name']); ?>" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header" style="background: #0f766e; color: white;">
                            <h5 class="modal-title"><?php echo htmlspecialchars($p['name']); ?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Institución:</strong> <?php echo htmlspecialchars($p['institution']); ?></p>
                            <p><strong>País:</strong> <?php echo htmlspecialchars($p['country']); ?></p>
                            <p><strong>Biografía:</strong> <?php echo htmlspecialchars($p['bio'] ?? 'Especialista en tecnología e innovación educativa.'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================ -->
<!-- ESTADÍSTICAS DEL EVENTO                                      -->
<!-- ============================================================ -->
<section class="my-5">
    <h2 class="text-center mb-4" style="color: #0f766e;"><?php echo $translations['stats_title']; ?></h2>
    <div class="row text-center">
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 class="display-5 fw-bold" style="color: #0f766e;"><?php echo $totalParticipantes ?: '0'; ?></h3>
                <p class="text-muted mb-0"><?php echo $translations['stats_participants']; ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 class="display-5 fw-bold" style="color: #0f766e;"><?php echo $totalProyectos ?: '0'; ?></h3>
                <p class="text-muted mb-0"><?php echo $translations['stats_projects']; ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 class="display-5 fw-bold" style="color: #0f766e;"><?php echo $totalPaises ?: '0'; ?></h3>
                <p class="text-muted mb-0"><?php echo $translations['stats_countries']; ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 class="display-5 fw-bold" style="color: #0f766e;"><?php echo $totalInstituciones ?: '0'; ?></h3>
                <p class="text-muted mb-0"><?php echo $translations['stats_institutions']; ?></p>
            </div>
        </div>
    </div>
</section>

<script>
// ============================================================
// CUENTA REGRESIVA
// ============================================================
function updateCountdown() {
    const eventDate = new Date("Nov 15, 2027 09:00:00").getTime();
    const now = new Date().getTime();
    const diff = eventDate - now;
    
    if (diff < 0) {
        document.getElementById('countdown').innerHTML = "<h3 class='text-success'>🎉 <?php echo $translations['countdown_started']; ?> 🎉</h3>";
        return;
    }
    
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (86400000)) / (3600000));
    const minutes = Math.floor((diff % 3600000) / 60000);
    const seconds = Math.floor((diff % 60000) / 1000);
    
    document.getElementById('days').innerText = days;
    document.getElementById('hours').innerText = hours;
    document.getElementById('minutes').innerText = minutes;
    document.getElementById('seconds').innerText = seconds;
}
setInterval(updateCountdown, 1000);
updateCountdown();

// ============================================================
// MAPA CON LEAFLET (solo países con registros reales)
// ============================================================
if(document.getElementById('worldMap')) {
    window.addEventListener('load', function() {
        var map = L.map('worldMap').setView([20, 0], 2);
        
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', { 
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
            subdomains: 'abcd',
            maxZoom: 19,
            minZoom: 2
        }).addTo(map);
        
        <?php
        // Obtener países reales desde la base de datos
        $stmt = $db->query("SELECT country, COUNT(*) as total FROM users WHERE country IS NOT NULL AND country != '' GROUP BY country ORDER BY total DESC");
        $paisesReales = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Coordenadas de países
        $coordenadas = [
            'mexico' => ['lat' => 23.6345, 'lng' => -102.5528, 'nombre' => 'México'],
            'méxico' => ['lat' => 23.6345, 'lng' => -102.5528, 'nombre' => 'México'],
            'estados unidos' => ['lat' => 37.0902, 'lng' => -95.7129, 'nombre' => 'Estados Unidos'],
            'united states' => ['lat' => 37.0902, 'lng' => -95.7129, 'nombre' => 'Estados Unidos'],
            'usa' => ['lat' => 37.0902, 'lng' => -95.7129, 'nombre' => 'Estados Unidos'],
            'canadá' => ['lat' => 56.1304, 'lng' => -106.3468, 'nombre' => 'Canadá'],
            'canada' => ['lat' => 56.1304, 'lng' => -106.3468, 'nombre' => 'Canadá'],
            'españa' => ['lat' => 40.4637, 'lng' => -3.7492, 'nombre' => 'España'],
            'reino unido' => ['lat' => 55.3781, 'lng' => -3.4360, 'nombre' => 'Reino Unido'],
            'colombia' => ['lat' => 4.5709, 'lng' => -74.2973, 'nombre' => 'Colombia'],
            'argentina' => ['lat' => -38.4161, 'lng' => -63.6167, 'nombre' => 'Argentina'],
            'chile' => ['lat' => -35.6751, 'lng' => -71.5430, 'nombre' => 'Chile'],
            'perú' => ['lat' => -9.1900, 'lng' => -75.0152, 'nombre' => 'Perú'],
            'brasil' => ['lat' => -14.2350, 'lng' => -51.9253, 'nombre' => 'Brasil'],
            'china' => ['lat' => 35.8617, 'lng' => 104.1954, 'nombre' => 'China'],
            'japón' => ['lat' => 36.2048, 'lng' => 138.2529, 'nombre' => 'Japón'],
        ];
        ?>
        
        var paisesMapa = [];
        <?php foreach($paisesReales as $pais): 
            $nombrePais = trim(strtolower($pais['country']));
            $coordenada = isset($coordenadas[$nombrePais]) ? $coordenadas[$nombrePais] : null;
            if($coordenada):
        ?>
            paisesMapa.push({
                lat: <?php echo $coordenada['lat']; ?>,
                lng: <?php echo $coordenada['lng']; ?>,
                nombre: '<?php echo addslashes($coordenada['nombre']); ?>',
                total: <?php echo $pais['total']; ?>
            });
        <?php 
            endif; 
        endforeach; 
        ?>
        
        if(paisesMapa.length === 0) {
            L.marker([20, 0]).addTo(map).bindPopup('Aún no hay participantes registrados.<br>¡Sé el primero en registrarte!');
        } else {
            paisesMapa.forEach(function(pais) {
                L.marker([pais.lat, pais.lng]).addTo(map)
                    .bindPopup('<b>' + pais.nombre + '</b><br>' + pais.total + ' <?php echo $translations['stats_participants']; ?>');
            });
        }
    });
}
</script>

<?php include 'partials/footer.php'; ?>