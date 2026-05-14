<?php
$id = (int)($_GET['id'] ?? 0); 
$sql = "SELECT s.*, u.login, c.name AS category_name FROM services s LEFT JOIN users u ON s.author_id = u.id LEFT JOIN categories c ON s.category_id = c.id WHERE s.id = $id";
$s = mysqli_fetch_assoc(mysqli_query($link, $sql));

if (!$s || ($s['visible'] == 0 && empty($_SESSION['admin']))) {
    die("<main class='content'><div class='form-card'><h2>Помилка: Сторінку не знайдено</h2><a href='index.php?action=services' class='action-link link-view'>← До списку послуг</a></div></main>");
}
?>

<main class="content">
    <div class="form-card">
        <h2><?= htmlspecialchars($s['name']) ?></h2>
        
        <?php if (!empty($s['category_name'])): ?>
            <p class="specialization-label mb-20">Категорія: <?= htmlspecialchars($s['category_name']) ?></p>
        <?php endif; ?>
        
        <p class="bio-content"><?= nl2br(htmlspecialchars($s['description'])) ?></p>
        
        <div class="price-tag"><?= $s['price'] ?> грн</div>

        <p class="meta-text">Автор: <?= htmlspecialchars($s['login'] ?? 'Невідомо') ?></p>
        <p class="meta-text">Дата публікації: <?= htmlspecialchars($s['date']) ?></p>
        
        <hr class="hr-custom">
        <a href="index.php?action=services" class="action-link link-view">← Повернутися до всіх послуг</a>
    </div>
</main>