<?php
if (empty($_SESSION['admin'])) {
    die("Доступ заборонено!");
}

$edit_cat = null;

if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res = mysqli_query($link, "SELECT * FROM categories WHERE id = $edit_id");
    $edit_cat = mysqli_fetch_assoc($res);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $name = mysqli_real_escape_string($link, trim($_POST['cat_name']));
    if (!empty($name)) {
        if (isset($_POST['cat_id']) && (int)$_POST['cat_id'] > 0) {
            $cat_id = (int)$_POST['cat_id'];
            mysqli_query($link, "UPDATE categories SET name = '$name' WHERE id = $cat_id");
        } else {
            mysqli_query($link, "INSERT INTO categories (name) VALUES ('$name')");
        }
        header("Location: index.php?action=categories");
        exit;
    }
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    mysqli_query($link, "DELETE FROM categories WHERE id = $id");
    header("Location: index.php?action=categories");
    exit;
}

$categories = mysqli_query($link, "SELECT * FROM categories");
?>

<main class="content">
    <div class="form-card">
        <div class="clearfix mb-20">
            <h2 style="float: left; margin: 0; border: none; padding: 0;">
                <?= $edit_cat ? 'Редагувати категорію' : 'Керування категоріями' ?>
            </h2>
            <a href="index.php?action=profile" class="action-link link-view" style="float: right; margin-top: 10px;">
                ← Назад до профілю
            </a>
        </div>
        
        <form method="post" class="mb-30">
            <?php if ($edit_cat): ?>
                <input type="hidden" name="cat_id" value="<?= $edit_cat['id'] ?>">
            <?php endif; ?>
            
            <input type="text" name="cat_name" 
                   value="<?= $edit_cat ? htmlspecialchars($edit_cat['name']) : '' ?>" 
                   placeholder="Назва категорії" required>
            
            <button type="submit" name="save_category" class="btn-submit">
                <?= $edit_cat ? 'Оновити' : 'Додати' ?>
            </button>
            
            <?php if ($edit_cat): ?>
                <div class="mt-15">
                    <a href="index.php?action=categories" class="action-link link-view">Скасувати</a>
                </div>
            <?php endif; ?>
        </form>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Назва категорії</th>
                    <th style="text-align: right;">Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['name']) ?></td>
                        <td style="text-align: right;">
                            <a href="index.php?action=categories&edit_id=<?= $c['id'] ?>" class="action-link link-edit">Редагувати</a>
                            <a href="index.php?action=categories&delete_id=<?= $c['id'] ?>" 
                               onclick="return confirm('Видалити категорію?')" 
                               class="action-link link-delete">Видалити</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>