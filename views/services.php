<?php
$sql = "SELECT * FROM services ";
if (empty($_SESSION['admin'])) {
    $sql .= " WHERE visible = 1 ";
}
$sql .= " ORDER BY date DESC";
$result = mysqli_query($link, $sql);
?>

<main class="content">
    <h2>Наші послуги</h2>

    <?php if (!empty($_SESSION)): ?>
        <div style="margin-bottom: 40px;">
            <a href="index.php?action=service_form" class="btn-submit" style="display: inline-block; width: auto; text-decoration: none; padding: 15px 30px;">
                + Створити нову послугу
            </a>
        </div>
    <?php endif; ?>

    <div class="services-list">
        <?php while ($s = mysqli_fetch_assoc($result)): ?>
            <div class="service-card">
                <?php if (!empty($_SESSION['admin'])): ?>
                    <span class="status-badge <?= $s['visible'] ? 'badge-visible' : 'badge-hidden' ?>">
                        <?= $s['visible'] ? 'Опубліковано' : 'Чернетка' ?>
                    </span>
                <?php endif; ?>

                <h3><?= htmlspecialchars($s['name']) ?></h3>
                <p class="service-description"><?= nl2br(htmlspecialchars($s['description'])) ?></p>
                <div class="price-tag"><?= $s['price'] ?> грн</div>
                
                <div class="card-actions">
                    <a href="index.php?action=view_service&id=<?= $s['id'] ?>" class="action-link link-view">Детальніше</a>
                    
                    <?php if (!empty($_SESSION['admin'])): ?>
                        <a href="index.php?action=service_form&id=<?= $s['id'] ?>" class="action-link link-edit">Редагувати</a>
                        <a href="index.php?action=delete_service&id=<?= $s['id'] ?>" 
                           class="action-link link-delete" 
                           onclick="return confirm('Видалити?')">Видалити</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>