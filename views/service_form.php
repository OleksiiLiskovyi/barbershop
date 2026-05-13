<?php
if (empty($_SESSION)) {
    die("<main class='content'><div class='form-card'><h2>Доступ заборонено!</h2><p>Тільки авторизовані користувачі можуть додавати послуги.</p></div></main>");
}

$isAdmin = !empty($_SESSION['admin']);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$service = ['name' => '', 'description' => '', 'price' => '', 'visible' => 0];

if ($id > 0) {
    $res = mysqli_query($link, "SELECT * FROM services WHERE id = $id");
    $service = mysqli_fetch_assoc($res);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($link, trim($_POST['name']));
    $desc = mysqli_real_escape_string($link, trim($_POST['description']));
    $price = (float)$_POST['price'];

    if ($isAdmin) {
        $visible = isset($_POST['visible']) ? 1 : 0;
    } else {
        $visible = 0; // не адмін — завжди 0
    }

    if ($id > 0) {
        // не дозволяємо не-адміну змінювати visible навіть при редагуванні
        if ($isAdmin) {
            $sql = "UPDATE services 
                    SET name='$name', description='$desc', price=$price, visible=$visible 
                    WHERE id=$id";
        } else {
            $sql = "UPDATE services 
                    SET name='$name', description='$desc', price=$price 
                    WHERE id=$id";
        }
    } else {
        $author_id = (int)$_SESSION['user_id'];
        $sql = "INSERT INTO services (name, description, price, visible, author_id) 
                VALUES ('$name', '$desc', $price, $visible, $author_id)";
    }

    mysqli_query($link, $sql);
    $cat_list = mysqli_query($link, "SELECT * FROM categories");
    header("Location: index.php?action=services");
    exit;
}
?>

<main class="content">
    <form method="post" class="form-card">
        <h2><?= $id > 0 ? 'Редагування послуги' : 'Нова послуга' ?></h2>

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
        <?php endif; ?>
<label>Категорія послуги</label>
<select name="category_id" required>
    <option value="">— Оберіть категорію —</option>
    <?php 
    mysqli_data_seek($cat_list, 0);
    while($c = mysqli_fetch_assoc($cat_list)): ?>
        <option value="<?= $c['id'] ?>" <?= ($service['category_id'] == $c['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($c['name']) ?>
        </option>
    <?php endwhile; ?>
</select>
        <button type="submit">Зберегти</button>
    </form>
</main>