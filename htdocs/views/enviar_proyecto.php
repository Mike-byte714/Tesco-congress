<?php include 'partials/header.php'; 
if(!isset($_SESSION['user_id'])) header("Location: login.php");
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <h2>Envío de Proyecto</h2>
        <?php if(isset($_GET['error'])) echo '<div class="alert alert-danger">Error: '.$_GET['error'].'</div>'; ?>
        <form action="../controllers/ProjectController.php?action=submit" method="POST" enctype="multipart/form-data">
            <div class="mb-3"><label>Título del proyecto</label><input type="text" name="title" class="form-control" required></div>
            <div class="mb-3"><label>Resumen</label><textarea name="abstract" rows="4" class="form-control" required></textarea></div>
            <div class="mb-3"><label>Autores (separados por comas)</label><input type="text" name="authors" class="form-control"></div>
            <div class="mb-3"><label>Institución</label><input type="text" name="institution" class="form-control"></div>
            <div class="mb-3"><label>País</label><input type="text" name="country" class="form-control"></div>
            <div class="mb-3"><label>Área temática</label>
                <select name="area" class="form-control">
                    <option>Inteligencia Artificial</option>
                    <option>Internet de las Cosas</option>
                    <option>Educación</option>
                    <option>Desarrollo de Software</option>
                    <option>Robótica</option>
                </select>
            </div>
            <div class="mb-3"><label>Archivo PDF (máx. 5MB)</label><input type="file" name="pdf_file" accept=".pdf" class="form-control" required></div>
            <button type="submit" class="btn btn-primary">Enviar proyecto</button>
        </form>
    </div>
</div>
<?php include 'partials/footer.php'; ?>