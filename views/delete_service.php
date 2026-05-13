<?php
if (empty($_SESSION['admin'])) {
    die("Доступ лише для адміністратора!");
}

$id = (int)($_GET['id'] ?? 0);

$check_sql = "SELECT id FROM services WHERE id = $id";
$check_res = mysqli_query($link, $check_sql);

if (mysqli_num_rows($check_res) > 0) {
    mysqli_query($link, "DELETE FROM services WHERE id = $id");
    echo "<main class='content'>
            <div class='form-card'>
                <h2>Успішно видалено!</h2>
                <p>Інформацію про послугу видалено з бази даних.</p>
                <br>
                <a href='index.php?action=services' class='action-link link-edit'>До списку послуг</a>
            </div>
          </main>";
} else {
    echo "<main class='content'>
            <div class='form-card'>
                <h2>Помилка: Запис не знайдено</h2>
            </div>
          </main>";
}
?>