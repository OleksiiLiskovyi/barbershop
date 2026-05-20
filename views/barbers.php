<?php
$active_cat = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

$sql = "SELECT b.id, u.first_name, u.last_name, b.image, b.bio, c.name as cat_name 
        FROM barbers b
        JOIN users u ON b.user_id = u.id
        JOIN categories c ON b.category_id = c.id
        WHERE 1=1 ";

if ($active_cat > 0) {
    $sql .= " AND b.category_id = $active_cat ";
}

$res = mysqli_query($link, $sql);
if (!$res) {
    die("Помилка виконання запиту: " . mysqli_error($link));
}

$categories = mysqli_query($link, "SELECT * FROM categories");
?>

<main class="content">
    <h2>Наші майстри</h2>
    
    <div class="filter-wrapper">
        <?php while ($c = mysqli_fetch_assoc($categories)): 
            $isActive = ($active_cat == $c['id']);
            $href = $isActive ? "index.php?action=barbers" : "index.php?action=barbers&category_id=" . $c['id'];
        ?>
            <a href="<?= $href ?>" class="filter-btn <?= $isActive ? 'active' : '' ?>">
                <?= htmlspecialchars($c['name']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <div class="grid-container barber-grid">
        <?php if (mysqli_num_rows($res) == 0): ?>
            <p class="meta-text">Майстрів у цій категорії поки немає.</p>
        <?php endif; ?>

        <?php while ($b = mysqli_fetch_assoc($res)): ?>
            <div class="service-card">
                <img src="uploads/<?= htmlspecialchars($b['image'] ?? 'default_barber.png') ?>" 
                     class="barber-card-img" alt="Фото майстра">
                
                <h3><?= htmlspecialchars($b['first_name'] . " " . $b['last_name']) ?></h3>
                <p class="accent-text"><?= htmlspecialchars($b['cat_name']) ?></p>
                <div class="card-actions">
                    <a href="index.php?action=view_barber&id=<?= $b['id'] ?>" class="action-link link-view">
                        Детальніше про майстра
                    </a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</main>