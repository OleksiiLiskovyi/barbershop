<?php
$errors = [];
$login = $email = $country = '';
$countries = [];

if (file_exists('countries.txt')) {
    $lines = file('countries.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '|') !== false) {
            list($code, $name) = explode('|', $line, 2);
            $countries[trim($code)] = trim($name);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = mysqli_real_escape_string($link, trim($_POST['login'] ?? ''));
    $email = mysqli_real_escape_string($link, trim($_POST['email'] ?? ''));
    $country = mysqli_real_escape_string($link, strtoupper(trim($_POST['country'] ?? '')));
    $password_raw = $_POST['password'] ?? '';
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

    if (empty($errors)) {
        $hashed_password = password_hash($password_raw, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (login, email, password, country, admin) 
                VALUES ('$login', '$email', '$hashed_password', '$country', 0)";

        if (mysqli_query($link, $sql)) {
            header('Location: index.php?action=registration_successful');
            exit;
        } else {
            if (mysqli_errno($link) == 1062) {
                $errors['db'] = 'Цей логін або email вже зайняті';
            } else {
                $errors['db'] = 'Помилка збереження: ' . mysqli_error($link);
            }
        }
    }
}
?>

<main class="content">
    <div class="form-card">
        <h2>Створення акаунту</h2>

        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>Виправте наступні помилки:</strong>
                <ul>
                    <?php foreach ($errors as $msg): ?>
                        <li><?= htmlspecialchars($msg) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?action=registration">
            <label for="login">Ваш логін</label>
            <input type="text" id="login" name="login" value="<?= htmlspecialchars($login) ?>" required>

            <label for="password">Пароль</label>
            <input type="password" id="password" name="password" required>

            <label for="repeat_password">Підтвердіть пароль</label>
            <input type="password" id="repeat_password" name="repeat_password" required>

            <label for="email">Електронна пошта</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>

            <label for="country">Країна походження</label>
            <select id="country" name="country" required>
                <option value="">— оберіть країну —</option>
                <?php foreach ($countries as $code => $name): ?>
                    <option value="<?= $code ?>" <?= ($country === $code) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($name) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Зареєструватися</button>
        </form>

        <p style="text-align: center; margin-top: 30px; color: #666; font-size: 14px;">
            Вже є профіль? <a href="index.php?action=login" class="link-edit">Увійти в кабінет</a>
        </p>
    </div>
</main>