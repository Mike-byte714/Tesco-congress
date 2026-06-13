<?php include 'partials/header.php';
require_once '../config/database.php';
$db = (new Database())->getConnection();
$stmt = $db->query("SELECT s.*, sp.name as speaker_name FROM schedule s LEFT JOIN speakers sp ON s.speaker_id = sp.id ORDER BY s.day, s.time");
$actividades = $stmt->fetchAll();
?>
<h2>Programa del Congreso</h2>
<div class="mb-3">
    <label>Filtrar por día:</label>
    <select id="dayFilter" class="form-select w-auto">
        <option value="all">Todos</option>
        <?php 
        $days = array_unique(array_column($actividades, 'day'));
        foreach($days as $d): echo "<option value='$d'>$d</option>"; endforeach;
        ?>
    </select>
</div>
<div id="scheduleList">
    <?php foreach($actividades as $a): ?>
        <div class="card mb-2" data-day="<?= $a['day'] ?>">
            <div class="card-body">
                <h5><?= $a['title'] ?></h5>
                <p><?= $a['day'] ?> - <?= $a['time'] ?> | Tipo: <?= $a['activity_type'] ?></p>
                <?php if($a['speaker_name']): ?><p>Ponente: <?= $a['speaker_name'] ?></p><?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script>
document.getElementById('dayFilter').addEventListener('change', function() {
    let val = this.value;
    document.querySelectorAll('#scheduleList .card').forEach(card => {
        if(val === 'all' || card.getAttribute('data-day') === val) card.style.display = 'block';
        else card.style.display = 'none';
    });
});
</script>
<?php include 'partials/footer.php'; ?>