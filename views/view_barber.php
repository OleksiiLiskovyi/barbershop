<?php
$id = isset($_GET['id']) ? intval($_GET['id']) : 0; 
$isAdmin = !empty($_SESSION['admin']); 
$current_user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null; 

if (isset($_GET['delete_comment_id'])) {
    $del_id = intval($_GET['delete_comment_id']);
    if ($del_id > 0) {
        if ($isAdmin) {
            mysqli_query($link, "DELETE FROM comments WHERE id = $del_id");
        } elseif ($current_user_id) {
            mysqli_query($link, "DELETE FROM comments WHERE id = $del_id AND user_id = $current_user_id");
        }
    }
    header("Location: index.php?action=view_barber&id=$id"); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_comment']) && $current_user_id) {
    $text = mysqli_real_escape_string($link, trim($_POST['comment_text'] ?? ''));
    $c_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    
    if (!empty($text)) {
        if ($c_id > 0) {
            mysqli_query($link, "UPDATE comments SET comment_text = '$text' WHERE id = $c_id AND user_id = $current_user_id");
        } else {
            mysqli_query($link, "INSERT INTO comments (user_id, barber_id, comment_text) VALUES ($current_user_id, $id, '$text')");
        }
        header("Location: index.php?action=view_barber&id=$id"); 
        exit;
    }
}

$b = null;
if ($id > 0) {
    $barber_res = mysqli_query($link, "SELECT u.first_name, u.last_name, b.image, b.bio, c.name as cat_name FROM barbers b JOIN users u ON b.user_id = u.id JOIN categories c ON b.category_id = c.id WHERE b.id = $id");
    if ($barber_res) {
        $b = mysqli_fetch_assoc($barber_res);
    }
}

if (!$b) {
    die("<main class='content'><div class='form-card'><h2>Майстра не знайдено</h2><br><a href='index.php?action=barbers' class='action-link link-view'>← До списку майстрів</a></div></main>");
}

$comments_res = mysqli_query($link, "SELECT c.*, u.login FROM comments c JOIN users u ON c.user_id = u.id WHERE c.barber_id = $id ORDER BY c.created_at DESC");

$edit_id = isset($_GET['edit_comment_id']) ? intval($_GET['edit_comment_id']) : 0; 
$edit_text = '';
if ($edit_id > 0 && $current_user_id) {
    $res_edit = mysqli_query($link, "SELECT comment_text FROM comments WHERE id = $edit_id AND user_id = $current_user_id");
    if ($res_edit && $row_edit = mysqli_fetch_assoc($res_edit)) {
        $edit_text = $row_edit['comment_text'];
    }
}
?>
<main class="content">
    <div class="form-card clearfix">
        <div class="barber-profile-photo">
            <img src="uploads/<?= htmlspecialchars($b['image'] ?? 'default_barber.png') ?>" alt="Майстер">
        </div>

        <div class="barber-info-wrap">
            <div class="name-accent-line">
                <h1><?= htmlspecialchars($b['first_name']) ?><br><?= htmlspecialchars($b['last_name']) ?></h1>
            </div>
            <p class="specialization-label">Спеціалізація: <?= htmlspecialchars($b['cat_name']) ?></p>
            <div class="horizontal-divider"></div>
        </div>

        <div class="text-justify">
            <h3 class="mb-20">Про майстра</h3>
            <p class="bio-content"><?= nl2br(htmlspecialchars($b['bio'])) ?></p>
        </div>

        <div class="clearfix"></div>
        <hr class="hr-custom">

        <div class="reviews-section">
            <h3 class="mb-20"><?= $edit_id > 0 ? 'Редагувати відгук' : 'Відгуки клієнтів' ?></h3>
            <?php if ($current_user_id): ?>
                <form method="post" class="mb-30">
                    <input type="hidden" name="comment_id" value="<?= $edit_id ?>">
                    <textarea name="comment_text" rows="3" required placeholder="Ваш відгук..."><?= htmlspecialchars($edit_text) ?></textarea>
                    <button type="submit" name="save_comment" class="btn-submit"><?= $edit_id > 0 ? 'Зберегти зміни' : 'Надіслати відгук' ?></button>
                    <?php if ($edit_id > 0): ?><a href="index.php?action=view_barber&id=<?= $id ?>" class="action-link link-view mt-15">Скасувати</a><?php endif; ?>
                </form>
            <?php endif; ?>

            <div class="comments-list">
                <?php while ($comm = mysqli_fetch_assoc($comments_res)): ?>
                    <div class="comment-box">
                        <div class="comment-top"><span class="comment-user"><?= htmlspecialchars($comm['login']) ?></span><span class="comment-date"><?= $comm['created_at'] ?></span></div>
                        <p class="comment-text"><?= nl2br(htmlspecialchars($comm['comment_text'])) ?></p>
                        <div class="comment-footer">
                            <?php if ($current_user_id == $comm['user_id']): ?>
                                <a href="index.php?action=view_barber&id=<?= $id ?>&edit_comment_id=<?= $comm['id'] ?>" class="action-link link-edit">Редагувати</a>
                            <?php endif; ?>
                            <?php if ($isAdmin || $current_user_id == $comm['user_id']): ?>
                                <a href="index.php?action=view_barber&id=<?= $id ?>&delete_comment_id=<?= $comm['id'] ?>" onclick="return confirm('Видалити?')" class="action-link link-delete">Видалити</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <a href="index.php?action=barbers" class="action-link link-view mt-20">← До списку майстрів</a>
    </div>
</main>