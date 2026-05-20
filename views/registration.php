<?php
$errors = [];
$login = $email = $first_name = $last_name = $birthdate = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login      = mysqli_real_escape_string($link, trim($_POST['login'] ?? ''));
    $email      = mysqli_real_escape_string($link, trim($_POST['email'] ?? ''));
    $first_name = mysqli_real_escape_string($link, trim($_POST['first_name'] ?? ''));
    $last_name  = mysqli_real_escape_string($link, trim($_POST['last_name'] ?? ''));
    $birthdate  = mysqli_real_escape_string($link, $_POST['birthdate'] ?? '');
    
    $password_raw    = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';

    if (empty($login) || !preg_match('/^[a-zA-Zа-яА-Я0-9_-]{4,}$/u', $login)) {
        $errors['login'] = 'Логін має бути від 4 символів';
    }

    if (empty($password_raw) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{7,}$/', $password_raw)) {
        $errors['password'] = 'Пароль занадто слабкий (потрібна велика літера та цифра)';
    }

    if ($password_raw !== $repeat_password) {
        $errors['repeat_password'] = 'Паролі не співпадають';
    }

    if (empty($email) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/i', $email)) {
        $errors['email'] = 'Електронна пошта має бути коректною.';
    }

    if (empty($first_name) || !preg_match('/^[a-zA-Zа-яА-ЯёЁіІїЇєЄґҐ\s\'-]{2,}$/u', $first_name)) {
        $errors['first_name'] = 'Введіть коректне ім’я';
    }

    if (empty($last_name) || !preg_match('/^[a-zA-Zа-яА-ЯёЁіІїЇєЄґҐ\s\'-]{2,}$/u', $last_name)) {
        $errors['last_name'] = 'Введіть коректне прізвище';
    }

    $date_obj = DateTime::createFromFormat('Y-m-d', $birthdate);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $birthdate) {
        $errors['birthdate'] = 'Некоректний формат дати';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password_raw, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (login, email, password, first_name, last_name, birthdate, admin) 
                VALUES ('$login', '$email', '$hashed_password', '$first_name', '$last_name', '$birthdate', 0)";

        if (mysqli_query($link, $sql)) {
            header('Location: index.php?action=registration_successful');
            exit;
        } else {
            $errors['db'] = 'Цей логін або email вже зайняті';
        }
    }
}
?>

<main class="content">
    <div class="form-card">
        <h2>Створення акаунту</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <ul>
                    <?php foreach ($errors as $msg): ?>
                        <li><?= htmlspecialchars($msg) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?action=registration">
            <label for="first_name">Ім’я</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>

            <label for="last_name">Прізвище</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>

            <label for="birthdate">Дата народження</label>
            <input type="date" id="birthdate" name="birthdate" value="<?= htmlspecialchars($birthdate) ?>" required>

            <label for="login">Ваш логін</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($login) ?>" required>

            <label for="email">Електронна пошта</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>

            <label for="repeat_password">Підтвердіть пароль</label>
            <input type="password" id="repeat_password" name="repeat_password" required>

            <button type="submit" class="btn-submit">Зареєструватися</button>
        </form>

        <p class="text-center mt-15">
            Вже є профіль? <a href="index.php?action=login" class="action-link link-edit">Увійти в кабінет</a>
        </p>
    </div>
</main>