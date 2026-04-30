<?php
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_input = mysqli_real_escape_string($link, trim($_POST['login'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (empty($login_input) || empty($password)) {
        $errors[] = 'Будь ласка, введіть логін та пароль';
    } else {
        $sql = "SELECT id, login, password, admin FROM users WHERE login = '$login_input' LIMIT 1";
        $result = mysqli_query($link, $sql);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login']   = $user['login'];
            $_SESSION['admin']   = (bool)$user['admin'];

            header('Location: index.php?action=main');
            exit;
        } else {
            $errors[] = 'Невірний логін або пароль';
        }
    }
}
?>

<main class="content">
    <div class="form-card">
        <h2>Вхід до акаунту</h2>
        <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>Помилка авторизації</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="post" action="index.php?action=login">
            <p>
                <label for="login">Логін</label>
                <input type="text" id="login" name="login" required>
            </p>

            <p>
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password" required>
            </p>

            <p>
                <button type="submit">Увійти</button>
            </p>
        </form>

        <p style="text-align: center; margin-top: 25px; color: #ccc;">
            Ще не маєте акаунту? 
            <a href="index.php?action=registration">Зареєструватися</a>
        </p>
    </div>
</main>