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
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $country = strtoupper(trim($_POST['country'] ?? ''));

    if (empty($login) || !preg_match('/^[a-zA-Zа-яА-Я0-9_-]{4,}$/u', $login)) {
        $errors['login'] = 'Логін має містити не менше 4 символів (літери, цифри, _ або -)';
    }

    if (empty($password) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{7,}$/', $password)) {
        $errors['password'] = 'Пароль має містити не менше 7 символів, велику та малу літеру і цифру';
    }

    if ($password !== $repeat_password) {
        $errors['repeat_password'] = 'Паролі не співпадають';
    }

    if (empty($email) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/i', $email)) {
        $errors['email'] = 'Введіть коректну електронну пошту';
    }

    if (empty($country) || !preg_match('/^[A-Z]{2}$/', $country) || !isset($countries[$country])) {
        $errors['country'] = 'Оберіть країну зі списку';
    }

    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO users (login, email, password, country, admin) VALUES (:login, :email, :password, :country, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':login' => $login,
                ':email' => $email,
                ':password' => $hashed_password,
                ':country' => $country
            ]);

            header('Location: index.php?action=registration_successful');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors['db'] = 'Користувач з таким логіном або email вже існує';
            } else {
                $errors['db'] = 'Помилка бази даних';
            }
        }
    }
}
?>

<main class="content">
    <h2>Реєстрація нового клієнта</h2>

    <div class="form-card">
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>Помилки заповнення форми</strong>
                <ul>
                    <?php foreach ($errors as $field => $message): ?>
                        <li><strong><?= ucfirst(htmlspecialchars($field)) ?>:</strong> <?= htmlspecialchars($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?action=registration">
            <p>
                <label for="login">Логін</label>
                <input type="text" id="login" name="login" value="<?= htmlspecialchars($login) ?>" required>
            </p>

            <p>
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </p>

            <p>
                <label for="repeat_password">Повторіть пароль</label>
                <input type="password" id="repeat_password" name="repeat_password" required>
            </p>

            <p>
                <label for="email">Електронна пошта</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
            </p>

            <p>
                <label for="country">Країна</label>
                <select id="country" name="country" required>
                    <option value="">— оберіть країну —</option>
                    <?php foreach ($countries as $code => $name): ?>
                        <option value="<?= $code ?>" <?= ($country === $code) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </p>

            <p>
                <button type="submit">Зареєструватися</button>
            </p>
        </form>

        <p style="text-align: center; margin-top: 25px; color: #ccc;">
            Вже маєте акаунт? 
            <a href="index.php?action=login">Увійти</a>
        </p>
    </div>
</main>