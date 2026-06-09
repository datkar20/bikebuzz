<?php
require_once __DIR__ . '/../app/layout.php';
require_admin();

$pdo = db();
$stats = [
    'San pham' => ['value' => (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(), 'icon' => 'bi-box-seam'],
    'Danh muc' => ['value' => (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn(), 'icon' => 'bi-tags'],
    'Nguoi dung' => ['value' => (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(), 'icon' => 'bi-people'],
    'Danh gia' => ['value' => (int) $pdo->query('SELECT COUNT(*) FROM reviews')->fetchColumn(), 'icon' => 'bi-star'],
];
$recentProducts = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC LIMIT 6')->fetchAll();

render_header('Admin', 'admin');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="page-title h3 mb-1">Dashboard admin</h1>
        <div class="text-muted">Quan ly san pham, danh muc, nguoi dung va thong bao.</div>
    </div>
    <a class="btn btn-brand" href="<?= url('admin/products.php?action=create') ?>"><i class="bi bi-plus-lg me-1"></i>Them san pham</a>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($stats as $label => $stat): ?>
        <div class="col-sm-6 col-xl-3">
            <div class="stat-tile d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small"><?= e($label) ?></div>
                    <div class="h2 fw-bold mb-0"><?= (int) $stat['value'] ?></div>
                </div>
                <i class="bi <?= e($stat['icon']) ?> fs-2 text-success"></i>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="bb-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h2 class="h5 fw-bold mb-0">San pham moi</h2>
        <a class="btn btn-outline-dark btn-sm" href="<?= url('admin/products.php') ?>">Quan ly</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead><tr><th>Xe</th><th>Danh muc</th><th>Gia</th><th>Kho</th></tr></thead>
            <tbody>
                <?php foreach ($recentProducts as $product): ?>
                    <tr>
                        <td><img class="thumb me-2" src="<?= e(product_image($product['image'])) ?>" alt=""> <strong><?= e($product['name']) ?></strong></td>
                        <td><?= e($product['category_name'] ?? 'Khac') ?></td>
                        <td><?= money((int) $product['price']) ?></td>
                        <td><?= (int) $product['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php render_footer(); ?>
