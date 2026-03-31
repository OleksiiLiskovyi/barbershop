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
        $errors['login'] = 'Логін має містити не менше 4 символів: тільки латинські або кириличні літери, цифри, _ або -.';
    }

    if (empty($password) || !preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{7,}$/', $password)) {
        $errors['password'] = 'Пароль має містити не менше 7 символів, великі та малі літери і хоча б одну цифру.';
    }

    if ($password !== $repeat_password) {
        $errors['repeat_password'] = 'Паролі не співпадають.';
    }

    if (empty($email) || !preg_match('/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-z]{2,}$/i', $email)) {
        $errors['email'] = 'Електронна пошта має бути коректною.';
    }

    if (empty($country) || !preg_match('/^[A-Z]{2}$/', $country) || !isset($countries[$country])) {
        $errors['country'] = 'Оберіть країну зі списку.';
    }

    if (empty($errors)) {
        header('Location: index.php?action=registration_successful');
        exit;
    }
}
?>
<main class="content">
    <h2>Реєстрація</h2>

    <?php if (!empty($errors)): ?>
        <div style="background:#3a2a2a; color:#ffaaaa; padding:15px; margin-bottom:20px; border:1px solid #c9a227;">
            <strong>Помилки заповнення:</strong>
            <ul>
                <?php foreach ($errors as $field => $message): ?>
                    <li><strong><?= ucfirst($field) ?>:</strong> <?= htmlspecialchars($message) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="index.php?action=registration">
        <p><label for="login">Логін:</label><br>
        <input type="text" id="login" name="login" value="<?= htmlspecialchars($login) ?>" required style="width:100%;padding:8px;background:#222;color:#eee;border:1px solid #c9a227;"></p>

        <p><label for="password">Пароль:</label><br>
        <input type="password" id="password" name="password" required style="width:100%;padding:8px;background:#222;color:#eee;border:1px solid #c9a227;"></p>

        <p><label for="repeat_password">Повторіть пароль:</label><br>
        <input type="password" id="repeat_password" name="repeat_password" required style="width:100%;padding:8px;background:#222;color:#eee;border:1px solid #c9a227;"></p>

        <p><label for="email">Електронна пошта:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required style="width:100%;padding:8px;background:#222;color:#eee;border:1px solid #c9a227;"></p>

        <p><label for="country">Країна:</label><br>
        <select id="country" name="country" required style="width:100%;padding:8px;background:#222;color:#eee;border:1px solid #c9a227;">
            <option value="">оберіть країну</option>
            <?php foreach ($countries as $code => $name): ?>
                <option value="<?= $code ?>" <?= ($country === $code) ? 'selected' : '' ?>><?= htmlspecialchars($name) ?></option>
            <?php endforeach; ?>
        </select></p>

        <p><button type="submit" style="background:#c9a227;color:#000;padding:12px 30px;font-size:16px;border:none;cursor:pointer;">Зареєструватися</button></p>
    </form>
</main>