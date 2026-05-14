<?php
$isAdmin = !empty($_SESSION['admin']);
$user_id = (int)($_SESSION['user_id'] ?? 0);
$isBarber = false;

if ($user_id > 0) {
    $barber_check = mysqli_query($link, "SELECT id FROM barbers WHERE user_id = $user_id");
    if ($barber_check) $isBarber = mysqli_num_rows($barber_check) > 0;
}

$active_cat = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

$sql = "SELECT s.*, c.name AS category_name 
        FROM services s 
        LEFT JOIN categories c ON s.category_id = c.id 
        WHERE 1=1 ";

if ($active_cat > 0) {
    $sql .= " AND s.category_id = $active_cat ";
}
if (!$isAdmin) {
    $sql .= " AND s.visible = 1 ";
}
$sql .= " ORDER BY s.date DESC";
$result = mysqli_query($link, $sql);

$categories = mysqli_query($link, "SELECT * FROM categories");
?>

<main class="content">
    <h2>Наші послуги</h2>

    <?php if ($isAdmin || $isBarber): ?>
        <div class="mb-20">
            <a href="index.php?action=service_form" class="btn-submit btn-inline">+ Створити нову послугу</a>
        </div>
    <?php endif; ?>

    <div class="filter-list">
        <?php while ($c = mysqli_fetch_assoc($categories)): 
            $isActive = ($active_cat == $c['id']);
            $href = $isActive ? "index.php?action=services" : "index.php?action=services&category_id=" . $c['id'];
        ?>
            <a href="<?= $href ?>" class="filter-item <?= $isActive ? 'active' : '' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="grid-container service-list">
        <?php while ($s = mysqli_fetch_assoc($result)): ?>
            <div class="service-card">
                
                <div class="badge-wrapper">
                    <?php if ($isAdmin): ?>
                        <span class="status-badge <?= $s['visible'] ? 'badge-visible' : 'badge-hidden' ?> static">
                            <?= $s['visible'] ? 'Опубліковано' : 'Чернетка' ?>
                        </span>
                    <?php endif; ?>
                    
                    <?php if (!empty($s['category_name'])): ?>
                        <span class="status-badge badge-category static">
                            <?= htmlspecialchars($s['category_name']) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <h3 class="with-badges"><?= htmlspecialchars($s['name']) ?></h3>
                
                <div class="price-tag mt-15"><?= $s['price'] ?> грн</div>
                
                <div class="card-actions">
                    <a href="index.php?action=view_service&id=<?= $s['id'] ?>" class="action-link link-view">Детальніше</a>
                    
                    <?php if ($isAdmin || ($isBarber && $s['author_id'] == $user_id)): ?>
                        <a href="index.php?action=service_form&id=<?= $s['id'] ?>" class="action-link link-edit">Редагувати</a>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <a href="index.php?action=delete_service&id=<?= $s['id'] ?>" class="action-link link-delete" onclick="return confirm('Видалити?')">Видалити</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>