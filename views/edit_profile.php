<?php
if (empty($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = false;

$sql = "SELECT * FROM users WHERE id = $user_id";
$res = mysqli_query($link, $sql);

if (!$res) {
    die("Помилка бази даних: " . mysqli_error($link));
}

$user = mysqli_fetch_assoc($res);

if (!$user) {
    die("Помилка: Користувача не знайдено.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($link, trim($_POST['first_name']));
    $last_name = mysqli_real_escape_string($link, trim($_POST['last_name']));
    $birthdate = mysqli_real_escape_string($link, $_POST['birthdate']);
    
    $old_pass = $_POST['old_password'] ?? '';
    $new_pass = $_POST['new_password'] ?? '';
    $rep_pass = $_POST['repeat_password'] ?? '';

    $password_sql = "";

    if (!empty($old_pass) || !empty($new_pass)) {
        if (!password_verify($old_pass, $user['password'])) {
            $errors[] = "Старий пароль невірний!";
        } 
        elseif (!empty($new_pass)) {
            if ($new_pass !== $rep_pass) {
                $errors[] = "Новий пароль та підтвердження не збігаються!";
            } else {
                $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
                $password_sql = ", password = '$hashed'";
            }
        } 
        else {
            $errors[] = "Ви ввели старий пароль, але не вказали новий!";
        }
    }

    if (empty($errors)) {
        $update_query = "UPDATE users SET 
                         first_name = '$first_name', 
                         last_name = '$last_name', 
                         birthdate = '$birthdate' 
                         $password_sql 
                         WHERE id = $user_id";
        
        if (mysqli_query($link, $update_query)) {
            $success = true;
            $user['first_name'] = $first_name;
            $user['last_name'] = $last_name;
            $user['birthdate'] = $birthdate;
        } else {
            $errors[] = "Помилка при оновленні даних: " . mysqli_error($link);
        }
    }
}
?>

<main class="content">
    <div class="form-card">
        <h2>Редагування профілю</h2>
        
        <?php if ($success): ?>
            <div class="badge-visible status-badge static mb-20" style="width: 100%; text-align: center;">
                Дані успішно оновлено!
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $e): ?> 
                        <li><?= htmlspecialchars($e) ?></li> 
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post">
            <label>Ім’я</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>

            <label>Прізвище</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>

            <label>Дата народження</label>
            <input type="date" name="birthdate" value="<?= $user['birthdate'] ?>" required>

            <hr class="hr-custom">
            <p class="meta-text mb-20">Змінити пароль</p>

            <label>Старий пароль</label>
            <input type="password" name="old_password">

            <label>Новий пароль</label>
            <input type="password" name="new_password">

            <label>Повторіть пароль</label>
            <input type="password" name="repeat_password">

            <button type="submit" class="btn-submit">Зберегти зміни</button>
        </form>
        
        <div class="mt-15">
            <a href="index.php?action=profile" class="action-link link-view">← Назад до профілю</a>
        </div>
    </div>
</main>