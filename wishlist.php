<?php
require_once __DIR__ . '/app/layout.php';
require_login();

$user = current_user();
$stmt = db()->prepare('SELECT p.*, c.name AS category_name, w.created_at AS saved_at FROM wishlists w JOIN products p ON p.id = w.product_id LEFT JOIN categories c ON c.id = p.category_id WHERE w.user_id = ? ORDER BY w.id DESC');
$stmt->execute([$user['id']]);
$products = $stmt->fetchAll();

render_header('Yêu thích', 'wishlist');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="page-title h3 mb-1">Danh sách yêu thích</h1>
        <div class="text-muted">Lưu lại những mẫu xe bạn đang cân nhắc.</div>
    </div>
    <a class="btn btn-outline-dark" href="<?= url('products.php') ?>"><i class="bi bi-bicycle me-1"></i>Xem sản phẩm</a>
</div>

<div class="row g-3">
    <?php if (!$products): ?>
        <div class="col-12"><div class="bb-card p-5 text-center text-muted">Bạn chưa lưu sản phẩm nào.</div></div>
    <?php endif; ?>
    <?php foreach ($products as $product): ?>
        <?php include __DIR__ . '/partials_product_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php render_footer(); ?>
