<?php
if (empty($_SESSION)) {
    die("<main class='content'><div class='form-card'><h2>Доступ заборонено!</h2><p>Тільки авторизовані користувачі можуть додавати послуги.</p></div></main>");
}

$user_id = intval($_SESSION['user_id']);
$isAdmin = !empty($_SESSION['admin']);
$isBarber = false;

$barber_check = mysqli_query($link, "SELECT id FROM barbers WHERE user_id = $user_id");
if ($barber_check) {
    $isBarber = mysqli_num_rows($barber_check) > 0;
}

if (!$isAdmin && !$isBarber) {
    die("<main class='content'><div class='form-card'><h2>Доступ заборонено!</h2></div></main>");
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$service = ['name' => '', 'description' => '', 'price' => '', 'visible' => ($isAdmin ? 1 : 0), 'category_id' => 0];

if ($id > 0) {
    $res = mysqli_query($link, "SELECT * FROM services WHERE id = $id");
    if ($res && mysqli_num_rows($res) > 0) {
        $service = mysqli_fetch_assoc($res);
    } else {
        $id = 0;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($link, trim($_POST['name'] ?? ''));
    $desc = mysqli_real_escape_string($link, trim($_POST['description'] ?? ''));
    $price = floatval($_POST['price'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);

    $visible = $isAdmin ? (isset($_POST['visible']) ? 1 : 0) : 0;

    if ($id > 0) {
        if ($isAdmin) {
            $sql = "UPDATE services SET name='$name', description='$desc', price=$price, visible=$visible, category_id=$category_id WHERE id=$id";
        } else {
            $author_check = mysqli_query($link, "SELECT author_id FROM services WHERE id=$id");
            $service_data = mysqli_fetch_assoc($author_check);
            
            if ($service_data && $service_data['author_id'] == $user_id) {
                $sql = "UPDATE services SET name='$name', description='$desc', price=$price, category_id=$category_id WHERE id=$id";
            } else {
                die("<main class='content'><div class='form-card'><h2>Помилка: Ви не можете редагувати чужу послугу!</h2></div></main>");
            }
        }
    } else {
        $sql = "INSERT INTO services (name, description, price, visible, author_id, category_id) VALUES ('$name', '$desc', $price, $visible, $user_id, $category_id)";
    }

    if (mysqli_query($link, $sql)) {
        header("Location: index.php?action=services");
        exit;
    } else {
        die("Помилка бази даних при збереженні: " . mysqli_error($link));
    }
}

$cat_list = mysqli_query($link, "SELECT * FROM categories");
?>

<main class="content">
    <form method="post" class="form-card">
        <h2><?= $id > 0 ? 'Редагування послуги' : 'Нова послуга' ?></h2>

        <label>Категорія послуги</label>
        <select name="category_id" required>
            <option value="">— Оберіть категорію —</option>
            <?php 
            if ($cat_list) mysqli_data_seek($cat_list, 0);
            while($c = mysqli_fetch_assoc($cat_list)): ?>
                <option value="<?= $c['id'] ?>" <?= ($service['category_id'] == $c['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Назва послуги</label>
        <input type="text" name="name" value="<?= htmlspecialchars($service['name']) ?>" required>

        <label>Опис</label>
        <textarea name="description" rows="4"><?= htmlspecialchars($service['description']) ?></textarea>

        <label>Ціна (грн)</label>
        <input type="number" step="0.01" name="price" value="<?= $service['price'] ?>" required>

        <?php if ($isAdmin): ?>
            <div class="switch-wrapper">
                <label class="switch">
                    <input type="checkbox" name="visible" value="1" <?= $service['visible'] ? 'checked' : '' ?>>
                    <span class="slider"></span>
                </label>
                <span class="switch-label">Опублікувати на сайті</span>
            </div>
        <?php else: ?>
            <p class="meta-text">ℹ Послуга буде збережена як чернетка.</p>
        <?php endif; ?>

        <button type="submit" class="btn-submit">Зберегти</button>
    </form>
</main>