<?php
if (empty($_SESSION['user_id'])) {
    header('Location: index.php?action=login');
    exit;
}

$user_id = $_SESSION['user_id'];
$isAdmin = !empty($_SESSION['admin']);

$sql = "SELECT login, email, first_name, last_name, birthdate FROM users WHERE id = $user_id";
$res = mysqli_query($link, $sql);
$user = mysqli_fetch_assoc($res);
?>

<main class="content">
    <div class="form-card">
        <h2>Мій профіль</h2>
        <div class="user-details bio-content">
            <p><strong>Логін:</strong> <?= htmlspecialchars($user['login']) ?></p>
            <p><strong>Ім’я:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
            <p><strong>Прізвище:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
            <p><strong>Дата народження:</strong> <?= $user['birthdate'] ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        </div>
        
        <div class="card-actions">
            <a href="index.php?action=edit_profile" class="action-link link-edit">Редагувати дані</a>
        </div>
    </div>

    <?php if ($isAdmin): ?>
    <div class="form-card admin-panel-accent">
        <h2 class="accent-title">Панель адміністратора</h2>
        <p class="panel-desc">Керування налаштуваннями сайту та персоналом.</p>
        
        <a href="index.php?action=categories" class="btn-submit admin-btn">
             Керування категоріями
        </a>
        
        <a href="index.php?action=manage_barbers" class="btn-submit admin-btn">
             Керування майстрами
        </a>
    </div>
    <?php endif; ?>
</main>