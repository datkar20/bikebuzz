<?php
require_once __DIR__ . '/app/layout.php';

$pdo = db();
$featured = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE featured = 1 ORDER BY p.id DESC LIMIT 4')->fetchAll();
$latest = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC LIMIT 8')->fetchAll();

render_header('Trang chu', 'home');
?>
<section class="hero-bike d-flex align-items-center mb-4">
    <div class="container-fluid position-relative z-1">
        <div class="row align-items-center g-4 hero-content">
            <div class="col-lg-6">
                <span class="badge bg-light text-dark mb-3">Bo suu tap 2026</span>
                <h1 class="display-5 fw-bold mb-3">Chon xe dap phu hop cho moi hanh trinh.</h1>
                <p class="lead mb-4 text-white-50">BikeBuzz cung cap xe dia hinh, xe dua va xe thanh pho chon loc, thong tin ro rang, gia niem yet minh bach va gio hang luu theo tai khoan.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-light fw-bold" href="<?= url('products.php') ?>"><i class="bi bi-bicycle me-1"></i>Kham pha xe</a>
                    <?php if (!current_user()): ?>
                        <a class="btn btn-outline-light fw-bold" href="<?= url('auth/register.php') ?>">Tao tai khoan</a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img class="img-fluid" src="<?= url('assets/img/bikehot1.png') ?>" alt="Cervelo Lamborghini">
            </div>
        </div>
    </div>
</section>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-tile">
            <div class="text-muted small">Danh muc</div>
            <div class="h2 fw-bold"><?= (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() ?></div>
            <div class="small text-muted">Dia hinh, dua, thanh pho, tre em.</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-tile">
            <div class="text-muted small">San pham</div>
            <div class="h2 fw-bold"><?= (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() ?></div>
            <div class="small text-muted">Hang co san, cap nhat lien tuc.</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-tile">
            <div class="text-muted small">Gio hang</div>
            <div class="h2 fw-bold"><?= cart_count() ?></div>
            <div class="small text-muted">San sang dat hang khi can.</div>
        </div>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="page-title h4 mb-0">San pham noi bat</h2>
    <a href="<?= url('products.php') ?>" class="btn btn-outline-dark btn-sm">Tat ca</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ($featured as $product): ?>
        <?php include __DIR__ . '/partials_product_card.php'; ?>
    <?php endforeach; ?>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="page-title h4 mb-0">Moi cap nhat</h2>
</div>
<div class="row g-3">
    <?php foreach ($latest as $product): ?>
        <?php include __DIR__ . '/partials_product_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php render_footer(); ?>
