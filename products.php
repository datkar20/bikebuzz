<?php
require_once __DIR__ . '/app/layout.php';

$q = trim($_GET['q'] ?? '');
$categoryId = (int) ($_GET['category'] ?? 0);
$categories = db()->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$params = [];
$where = [];
if ($q !== '') {
    $where[] = '(p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)';
    $params = array_merge($params, ["%$q%", "%$q%", "%$q%"]);
}
if ($categoryId > 0) {
    $where[] = 'p.category_id = ?';
    $params[] = $categoryId;
}
$sql = 'SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY p.id DESC';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

render_header('San pham', 'products');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="page-title h3 mb-1">San pham xe dap</h1>
        <div class="text-muted">Khach chua dang nhap chi xem san pham; dang nhap moi them duoc vao gio.</div>
    </div>
</div>

<form class="bb-card p-3 mb-4" method="get">
    <div class="row g-2">
        <div class="col-md-7">
            <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Tim ten xe, thuong hieu, mo ta...">
        </div>
        <div class="col-md-3">
            <select class="form-select" name="category">
                <option value="0">Tat ca danh muc</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2 d-grid">
            <button class="btn btn-brand"><i class="bi bi-search me-1"></i>Tim</button>
        </div>
    </div>
</form>

<div class="row g-3">
    <?php if (!$products): ?>
        <div class="col-12"><div class="bb-card p-4 text-center text-muted">Khong tim thay san pham phu hop.</div></div>
    <?php endif; ?>
    <?php foreach ($products as $product): ?>
        <?php include __DIR__ . '/partials_product_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php render_footer(); ?>
