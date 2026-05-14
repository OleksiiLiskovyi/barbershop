<?php
if (empty($_SESSION['admin'])) {
    die("Доступ заборонено!");
}

$edit_barber = null;
$error_msg = '';

if (isset($_GET['edit_id'])) {
    $edit_id = (int)$_GET['edit_id'];
    $res = mysqli_query($link, "SELECT * FROM barbers WHERE id = $edit_id");
    $edit_barber = mysqli_fetch_assoc($res);
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $img_res = mysqli_query($link, "SELECT image FROM barbers WHERE id = $id");
    if ($img_res && $img_data = mysqli_fetch_assoc($img_res)) {
        if (!empty($img_data['image']) && !in_array($img_data['image'], ['default.png'])) {
            @unlink("uploads/" . $img_data['image']);
        }
    }
    mysqli_query($link, "DELETE FROM comments WHERE barber_id = $id");
    mysqli_query($link, "DELETE FROM barbers WHERE id = $id");
    header("Location: index.php?action=manage_barbers");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_barber'])) {
    $cat_id = (int)$_POST['category_id'];
    $bio = mysqli_real_escape_string($link, trim($_POST['bio'] ?? ''));
    $image_update_sql = "";
    
    $image_insert_col = ", image";
    $image_insert_val = ", 'default.png'";

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $file_tmp = $_FILES['photo']['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($mime_type, $allowed_mimes) && getimagesize($file_tmp)) {
            $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid('barber_') . '.' . $ext;
            if (move_uploaded_file($file_tmp, "uploads/" . $new_filename)) {
                $image_update_sql = ", image = '$new_filename'";
                $image_insert_val = ", '$new_filename'";
            }
        } else {
            $error_msg = "Помилка: Некоректний файл зображення.";
        }
    }

    if (empty($error_msg)) {
        if (isset($_POST['barber_id'])) {
            $b_id = (int)$_POST['barber_id'];
            mysqli_query($link, "UPDATE barbers SET category_id = $cat_id, bio = '$bio' $image_update_sql WHERE id = $b_id");
        } else {
            $u_id = (int)$_POST['user_id'];
            if ($u_id > 0) {
                mysqli_query($link, "INSERT INTO barbers (user_id, category_id, bio $image_insert_col) VALUES ($u_id, $cat_id, '$bio' $image_insert_val)");
            }
        }
        header("Location: index.php?action=manage_barbers");
        exit;
    }
}

$available_users = mysqli_query($link, "SELECT id, login FROM users WHERE id NOT IN (SELECT user_id FROM barbers) AND admin = 0");
$categories_list = mysqli_query($link, "SELECT * FROM categories");
$all_barbers = mysqli_query($link, "SELECT b.id, b.image, b.bio, u.login, c.name as cat_name FROM barbers b JOIN users u ON b.user_id = u.id JOIN categories c ON b.category_id = c.id");
?>

<main class="content">
    <div class="form-card">
        <div class="clearfix mb-20">
            <h2 style="float: left; border: none; padding: 0; margin: 0;"><?= $edit_barber ? 'Редагувати майстра' : 'Керування майстрами' ?></h2>
            <a href="index.php?action=profile" class="action-link link-view" style="float: right; margin-top: 10px;">← Назад</a>
        </div>

        <?php if ($error_msg): ?>
            <div class="badge-hidden status-badge static mb-20" style="width: 100%;"><?= $error_msg ?></div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" onsubmit="return confirm('Підтвердити дію?')">
            <?php if ($edit_barber): ?>
                <input type="hidden" name="barber_id" value="<?= $edit_barber['id'] ?>">
                <p class="mb-20">Майстер: <strong><?= htmlspecialchars(mysqli_fetch_assoc(mysqli_query($link, "SELECT login FROM users WHERE id = {$edit_barber['user_id']}"))['login']) ?></strong></p>
            <?php else: ?>
                <label>Оберіть користувача:</label>
                <select name="user_id" required>
                    <option value="">— Оберіть —</option>
                    <?php while ($u = mysqli_fetch_assoc($available_users)): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['login']) ?></option>
                    <?php endwhile; ?>
                </select>
            <?php endif; ?>

            <label>Категорія:</label>
            <select name="category_id" required>
                <?php mysqli_data_seek($categories_list, 0); while ($c = mysqli_fetch_assoc($categories_list)): ?>
                    <option value="<?= $c['id'] ?>" <?= ($edit_barber && $edit_barber['category_id'] == $c['id']) ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Біографія:</label>
            <textarea name="bio" rows="4"><?= $edit_barber ? htmlspecialchars($edit_barber['bio']) : '' ?></textarea>

            <label>Фото майстра:</label>
            <input type="file" name="photo" accept="image/*">

            <button type="submit" name="save_barber" class="btn-submit"><?= $edit_barber ? 'Зберегти зміни' : 'Додати до штату' ?></button>
        </form>

        <h3 class="mt-20 mb-20">Список майстрів</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Фото</th>
                    <th>Логін / Категорія</th>
                    <th class="text-right">Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($b = mysqli_fetch_assoc($all_barbers)): ?>
                    <tr>
                        <td>
                            <img src="uploads/<?= (!empty($b['image']) ? htmlspecialchars($b['image']) : 'default.png') ?>" class="thumb-mini">
                        </td>
                        <td><strong><?= htmlspecialchars($b['login']) ?></strong><br><span class="accent-text"><?= htmlspecialchars($b['cat_name']) ?></span></td>
                        <td class="text-right">
                            <a href="index.php?action=manage_barbers&edit_id=<?= $b['id'] ?>" class="action-link link-edit">Редагувати</a>
                            <a href="index.php?action=manage_barbers&delete_id=<?= $b['id'] ?>" onclick="return confirm('Видалити?')" class="action-link link-delete">Видалити</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>