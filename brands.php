<?php
require_once __DIR__ . '/app/layout.php';

$brands = db()->query('SELECT brand, COUNT(*) AS total, MIN(price) AS min_price FROM products GROUP BY brand ORDER BY brand')->fetchAll();

render_header('Thương hiệu', 'brands');
?>
<h1 class="page-title h3 mb-3">Thương hiệu xe</h1>
<div class="row g-3">
    <?php foreach ($brands as $brand): ?>
        <div class="col-sm-6 col-xl-3">
            <a class="bb-card brand-card p-4 d-block h-100" href="<?= url('products.php?q=' . urlencode($brand['brand'])) ?>">
                <div class="brand-mark"><?= e(substr($brand['brand'], 0, 1)) ?></div>
                <h2 class="h5 fw-bold mt-3 mb-1"><?= e($brand['brand']) ?></h2>
                <div class="text-muted small"><?= (int) $brand['total'] ?> mẫu xe</div>
                <div class="fw-bold text-success mt-3">Từ <?= money((int) $brand['min_price']) ?></div>
            </a>
        </div>
    <?php endforeach; ?>
</div>
<?php render_footer(); ?>
