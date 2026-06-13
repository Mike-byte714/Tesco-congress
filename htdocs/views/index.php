<?php include 'partials/header.php'; ?>

<!-- ========== CARRUSEL PRINCIPAL CON IMÁGENES ========== -->
<div id="heroCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2"></button>
    </div>
    <div class="carousel-inner">
        <!-- SLIDE 1 - Conferencia / Audiencia -->
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1920&h=600&fit=crop" 
                 class="d-block w-100" 
                 alt="Conferencia internacional"
                 style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                <h1 class="display-4"><?php echo $translations['hero_title']; ?></h1>
                <p class="lead"><?php echo $translations['hero_sub']; ?></p>
                <a href="registro.php" class="btn btn-warning btn-lg"><?php echo $translations['register_btn']; ?></a>
            </div>
        </div>
        
        <!-- SLIDE 2 - Tecnología / Innovación -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1519389950473-47ba0277781c?w=1920&h=600&fit=crop" 
                 class="d-block w-100" 
                 alt="Innovación tecnológica"
                 style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                <h1 class="display-4">Innovación y Tecnología</h1>
                <p class="lead">Descubre las últimas tendencias en IA, Robótica e IoT</p>
                <a href="registro.php" class="btn btn-warning btn-lg">Registrarse</a>
            </div>
        </div>
        
        <!-- SLIDE 3 - Educación / Estudiantes -->
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=1920&h=600&fit=crop" 
                 class="d-block w-100" 
                 alt="Educación y estudiantes"
                 style="height: 500px; object-fit: cover;">
            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded p-3">
                <h1 class="display-4">Educación del Futuro</h1>
                <p class="lead">Metodologías activas y aprendizaje digital</p>
                <a href="registro.php" class="btn btn-warning btn-lg">Registrarse</a>
            </div>
        </div>
    </div>
    
    <!-- Controles de navegación -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Siguiente</span>
    </button>
</div>

<!-- ========== CUENTA REGRESIVA ========== -->
<section class="text-center my-5">
    <h2 class="mb-4"><?php echo $translations['countdown_title']; ?></h2>
    <div id="countdown" class="row justify-content-center">
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-3 rounded-4 shadow-lg">
                <span id="days" class="display-4 fw-bold">00</span><br>
                <span class="fs-5">Días</span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-3 rounded-4 shadow-lg">
                <span id="hours" class="display-4 fw-bold">00</span><br>
                <span class="fs-5">Horas</span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-3 rounded-4 shadow-lg">
                <span id="minutes" class="display-4 fw-bold">00</span><br>
                <span class="fs-5">Minutos</span>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="bg-primary text-white p-3 rounded-4 shadow-lg">
                <span id="seconds" class="display-4 fw-bold">00</span><br>
                <span class="fs-5">Segundos</span>
            </div>
        </div>
    </div>
</section>

<!-- ========== MAPA INTERACTIVO ========== -->
<section class="my-5">
    <h2 class="text-center mb-4"><?php echo $translations['map_title']; ?></h2>
    <div id="worldMap" style="height: 450px; border-radius: 20px; overflow: hidden; box-shadow: 0 8px 25px rgba(0,0,0,0.1);"></div>
</section>

<!-- ========== PONENTES INTERNACIONALES ========== -->
<section class="my-5">
    <h2 class="text-center mb-4"><?php echo $translations['speakers_title']; ?></h2>
    <div class="row" id="speakersContainer">
        <?php
        require_once '../config/database.php';
        require_once '../models/Speaker.php';
        $db = (new Database())->getConnection();
        $speakerModel = new Speaker($db);
        $speakers = $speakerModel->getAll();
        
        if(empty($speakers)):
            // Ponentes de ejemplo (si no hay datos en BD)
            $exampleSpeakers = [
                ['name' => 'Dr. John Smith', 'institution' => 'University of Cambridge', 'country' => 'Reino Unido', 'icon' => 'fa-user-graduate'],
                ['name' => 'Dra. Emily Johnson', 'institution' => 'MIT', 'country' => 'Estados Unidos', 'icon' => 'fa-user-astronaut'],
                ['name' => 'Dr. Michael Brown', 'institution' => 'University of Toronto', 'country' => 'Canadá', 'icon' => 'fa-user-tie']
            ];
            foreach($exampleSpeakers as $s):
        ?>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4 h-100 shadow-sm border-0 rounded-4">
                    <div class="rounded-circle mx-auto bg-light d-flex align-items-center justify-content-center" style="width: 130px; height: 130px;">
                        <i class="fas <?php echo $s['icon']; ?> fa-3x text-primary"></i>
                    </div>
                    <h4 class="mt-3 fw-bold"><?php echo $s['name']; ?></h4>
                    <p class="text-muted mb-0"><?php echo $s['institution']; ?></p>
                    <p class="text-success"><?php echo $s['country']; ?></p>
                </div>
            </div>
        <?php 
            endforeach;
        else:
            foreach($speakers as $s): 
        ?>
            <div class="col-md-4 mb-4">
                <div class="card text-center p-4 h-100 shadow-sm border-0 rounded-4">
                    <?php if($s['photo'] && file_exists("../" . $s['photo'])): ?>
                        <img src="../<?php echo $s['photo']; ?>" class="rounded-circle mx-auto" width="120" height="120" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle mx-auto bg-light d-flex align-items-center justify-content-center" style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-3x text-primary"></i>
                        </div>
                    <?php endif; ?>
                    <h4 class="mt-3 fw-bold"><?php echo $s['name']; ?></h4>
                    <p class="text-muted mb-0"><?php echo $s['institution']; ?></p>
                    <p class="text-success"><?php echo $s['country']; ?></p>
                </div>
            </div>
        <?php 
            endforeach;
        endif; 
        ?>
    </div>
</section>

<!-- ========== ESTADÍSTICAS ========== -->
<section class="my-5">
    <h2 class="text-center mb-4"><?php echo $translations['stats_title']; ?></h2>
    <div class="row text-center" id="statsDashboard">
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 id="totalParticipants" class="display-5 fw-bold" style="color: #2e7d32;">0</h3>
                <p class="text-muted mb-0"><?php echo $translations['participants']; ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 id="totalProjects" class="display-5 fw-bold" style="color: #2e7d32;">0</h3>
                <p class="text-muted mb-0"><?php echo $translations['projects']; ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 id="totalCountries" class="display-5 fw-bold" style="color: #2e7d32;">0</h3>
                <p class="text-muted mb-0"><?php echo $translations['countries']; ?></p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card p-4 shadow-sm border-0 rounded-4">
                <h3 id="totalInstitutions" class="display-5 fw-bold" style="color: #2e7d32;">0</h3>
                <p class="text-muted mb-0"><?php echo $translations['institutions']; ?></p>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>