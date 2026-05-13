<?php
$id = (int)($_GET['id'] ?? 0); 

$sql = "SELECT s.*, u.login
        FROM services s
        LEFT JOIN users u ON s.author_id = u.id
        WHERE s.id = $id";

$result = mysqli_query($link, $sql);
$s = mysqli_fetch_assoc($result);

if (!$s || ($s['visible'] == 0 && empty($_SESSION['admin']))) {
    echo "<main class='content'>
            <div class='form-card'>
                <h2>Помилка: Сторінку не знайдено</h2>
                <p>Такої послуги не існує або вона була видалена.</p>
                <br>
                <a href='index.php?action=services' class='action-link link-view'>← До списку послуг</a>
            </div>
          </main>";
} else {
?>
<main class="content">
    <div class="form-card">
        <span class="status-badge badge-visible">Деталі послуги</span>
        
        <h2><?= htmlspecialchars($s['name']) ?></h2>
        
        <p class="service-description">
            <?= nl2br(htmlspecialchars($s['description'])) ?>
        </p>
        
        <div class="price-tag">Вартість: <?= $s['price'] ?> грн</div>

        <p style="color: #aaa; font-size: 14px; margin-top: 10px;">
            Автор: <?= htmlspecialchars($s['login'] ?? 'Невідомо') ?>
        </p>
        
        <p style="color: #666; font-size: 13px; margin-top: 10px;">
            Дата публікації: <?= htmlspecialchars($s['date']) ?>
        </p>
        
        <hr style="border: 0; border-top: 1px solid #333; margin: 25px 0;">
        
        <a href="index.php?action=services" class="action-link link-view">
            ← Повернутися до всіх послуг
        </a>
    </div>
</main>
<?php } ?>