<div class="menu">
    <ul>
        <li><a href="index.php?action=main">Головна</a></li>
        <li><a href="index.php?action=barbers">Наші майстри</a></li> 
        <li><a href="index.php?action=services">Послуги</a></li>
    </ul>

    <div class="user-section">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="user-info">
                <span class="user-name">
                    <a href="index.php?action=profile">
                        &#9658<?= htmlspecialchars($_SESSION['login']) ?>
                    </a>
                </span>

                <?php if (!empty($_SESSION['admin'])): ?>
                    <span class="admin-status">Адміністратор</span>
                <?php endif; ?>

                <?php 
                $u_id = (int)$_SESSION['user_id'];
                $barber_query = mysqli_query($link, "
                    SELECT c.name 
                    FROM barbers b 
                    JOIN categories c ON b.category_id = c.id 
                    WHERE b.user_id = $u_id 
                    LIMIT 1
                ");
                
                if ($barber_query && $b_data = mysqli_fetch_assoc($barber_query)): ?>
                    <span class="admin-status"><?= htmlspecialchars($b_data['name']) ?></span>
                <?php endif; ?>
            </div>
            <a href="index.php?action=logout" class="logout-button">Вийти</a>
        <?php else: ?>
            <a href="index.php?action=login" class="menu-auth-link">Увійти</a>
            <a href="index.php?action=registration" class="menu-auth-link">Реєстрація</a>
        <?php endif; ?>
    </div>
</div>