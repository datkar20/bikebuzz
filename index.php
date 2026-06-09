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
                <span class="badge bg-light text-dark mb-3">Bộ sưu tập 2026</span>
                <h1 class="display-5 fw-bold mb-3">Chọn xe đạp phù hợp cho mọi hành trình.</h1>
                <p class="lead mb-4 text-white-50">BikeBuzz cung cấp xe địa hình, xe đua và xe thành phố chọn lọc, thông tin rõ ràng, giá niêm yết minh bạch và giỏ hàng lưu theo tài khoản.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a class="btn btn-light fw-bold" href="<?= url('products.php') ?>"><i class="bi bi-bicycle me-1"></i>Khám phá xe</a>
                    <?php if (!current_user()): ?>
                        <a class="btn btn-outline-light fw-bold" href="<?= url('auth/register.php') ?>">Tạo tài khoản</a>
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
            <div class="text-muted small">Danh mục</div>
            <div class="h2 fw-bold"><?= (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn() ?></div>
            <div class="small text-muted">Địa hình, đua, thành phố, trẻ em.</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-tile">
            <div class="text-muted small">Sản phẩm</div>
            <div class="h2 fw-bold"><?= (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn() ?></div>
            <div class="small text-muted">Hàng có sẵn, cập nhật liên tục.</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-tile">
            <div class="text-muted small">Giỏ hàng</div>
            <div class="h2 fw-bold"><?= cart_count() ?></div>
            <div class="small text-muted">Sẵn sàng đặt hàng khi cần.</div>
        </div>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="page-title h4 mb-0">Sản phẩm nổi bật</h2>
    <a href="<?= url('products.php') ?>" class="btn btn-outline-dark btn-sm">Tất cả</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ($featured as $product): ?>
        <?php include __DIR__ . '/partials_product_card.php'; ?>
    <?php endforeach; ?>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="page-title h4 mb-0">Mới cập nhật</h2>
</div>
<div class="row g-3">
    <?php foreach ($latest as $product): ?>
        <?php include __DIR__ . '/partials_product_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php render_footer(); ?>
