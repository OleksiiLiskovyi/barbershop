<?php
$is_admin = isset($_SESSION['admin']) && $_SESSION['admin'] == 1;
$upload_error = '';

if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['new_gallery_photo'])) {
    $file = $_FILES['new_gallery_photo'];
    
    if ($file['error'] === 0) {
        $file_tmp = $file['tmp_name'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);
        
        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
        if (in_array($mime_type, $allowed_mimes) && getimagesize($file_tmp)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'gallery_' . uniqid() . '.' . $ext;
            
            if (!is_dir('img/main')) {
                mkdir('img/main', 0755, true);
            }
            
            if (move_uploaded_file($file_tmp, "img/main/" . $new_filename)) {
                header("Location: index.php?action=main");
                exit;
            } else {
                $upload_error = "Помилка при збереженні файлу.";
            }
        } else {
            $upload_error = "Некоректний формат файлу (дозволено: JPG, PNG, WEBP).";
        }
    } else {
        $upload_error = "Помилка завантаження файлу.";
    }
}

$gallery_photos = glob("img/main/*.{jpg,jpeg,png,webp}", GLOB_BRACE);
?>

<main class="content main-page">
    <section class="about-section">
        <h2>Наша історія</h2>
        <div class="story-container">
            <p><strong>Lama Barbershop</strong> — це не просто перукарня, це простір, де класичні традиції догляду поєднуються з сучасним стилем та характером. Ми заснували цей бренд із чіткою філософією: кожен чоловік заслуговує на бездоганний сервіс, гарячий рушник та майстерно виконану стрижку, яка підкреслює його індивідуальність.</p>
            <p>Наша атмосфера створена для тих, хто цінує якість, гарну розмову та професіоналізм. Ми зібрали команду найкращих майстрів, які володіють як класичними техніками гоління небезпечною бритвою, так і трендовими чоловічими укладками. Завітайте до нас, відчуйте комфорт і вийдіть оновленими.</p>
        </div>
    </section>

    <section class="gallery-section">
        <h3>Атмосфера нашого простору</h3>

        <?php if ($upload_error): ?>
            <p class="error-text" style="color: #ff4d4d; text-align: center;"><?= $upload_error ?></p>
        <?php endif; ?>

        <div class="slider-wrapper">
            <?php if (!empty($gallery_photos)): ?>
                <button class="slider-btn prev-btn" onclick="moveSlide(-1)">&#10094;</button>
                
                <div class="slider-container">
                    <?php foreach ($gallery_photos as $index => $photo_path): ?>
                        <div class="slide <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= htmlspecialchars($photo_path) ?>" alt="Фото барбершопу">
                        </div>
                    <?php endforeach; ?>
                </div>

                <button class="slider-btn next-btn" onclick="moveSlide(1)">&#10095;</button>
            <?php else: ?>
                <p class="no-photos">Галерея наразі порожня. <? = $is_admin ? 'Додайте перше фото!' : '' ?></p>
            <?php endif; ?>
        </div>
        <?php if ($is_admin): ?>
            <div class="admin-upload-zone">
                <form method="post" enctype="multipart/form-data" id="adminGalleryForm">
                    <input type="file" name="new_gallery_photo" id="galleryFileInput" accept="image/*" style="display: none;" onchange="document.getElementById('adminGalleryForm').submit();">
                    <button type="button" class="btn-submit" onclick="document.getElementById('galleryFileInput').click();">
                        Додати фото в галерею
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </section>
</main>

<script>
let currentSlideIdx = 0;

function moveSlide(direction) {
    const slides = document.querySelectorAll('.slide');
    if (slides.length === 0) return;

    slides[currentSlideIdx].classList.remove('active');

    currentSlideIdx += direction;
    if (currentSlideIdx >= slides.length) {
        currentSlideIdx = 0;
    } else if (currentSlideIdx < 0) {
        currentSlideIdx = slides.length - 1;
    }

    slides[currentSlideIdx].classList.add('active');
}
</script>