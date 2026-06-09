<?php
require_once __DIR__ . '/app/layout.php';

$q = trim($_GET['q'] ?? '');
$categoryId = (int) ($_GET['category'] ?? 0);
$minPrice = (int) ($_GET['min_price'] ?? 0);
$maxPrice = (int) ($_GET['max_price'] ?? 0);
$minRating = (float) ($_GET['rating'] ?? 0);
$sort = $_GET['sort'] ?? 'newest';
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
if ($minPrice > 0) {
    $where[] = 'p.price >= ?';
    $params[] = $minPrice;
}
if ($maxPrice > 0) {
    $where[] = 'p.price <= ?';
    $params[] = $maxPrice;
}
$sql = 'SELECT p.*, c.name AS category_name, COALESCE(AVG(r.rating), 0) AS average_rating, COUNT(r.id) AS total_reviews FROM products p LEFT JOIN categories c ON c.id = p.category_id LEFT JOIN reviews r ON r.product_id = p.id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' GROUP BY p.id';
if ($minRating > 0) {
    $sql .= ' HAVING average_rating >= ?';
    $params[] = $minRating;
}
$orderMap = [
    'price_asc' => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'rating' => 'average_rating DESC, total_reviews DESC',
    'name' => 'p.name ASC',
    'newest' => 'p.id DESC',
];
$sql .= ' ORDER BY ' . ($orderMap[$sort] ?? $orderMap['newest']);
$stmt = db()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

render_header('Sản phẩm', 'products');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <div>
        <h1 class="page-title h3 mb-1">Sản phẩm xe đạp</h1>
        <div class="text-muted">Chọn xe theo nhu cầu, ngân sách và đánh giá từ khách hàng.</div>
    </div>
</div>

<form class="filter-panel mb-4" method="get">
    <div class="filter-heading">
        <div>
            <div class="small text-muted">Bộ lọc</div>
            <div class="fw-bold">Tìm chiếc xe phù hợp</div>
        </div>
        <a class="btn btn-outline-dark btn-sm" href="<?= url('products.php') ?>">Xóa lọc</a>
    </div>
    <div class="row g-3">
        <div class="col-lg-4">
            <label class="form-label">Từ khóa</label>
            <div class="input-icon">
                <i class="bi bi-search"></i>
                <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Tên xe, thương hiệu, mô tả...">
            </div>
        </div>
        <div class="col-sm-6 col-lg-2">
            <label class="form-label">Danh mục</label>
            <select class="form-select" name="category">
                <option value="0">Tất cả</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= $categoryId === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-sm-6 col-lg-2">
            <label class="form-label">Giá từ</label>
            <input class="form-control" type="number" name="min_price" min="0" step="500000" value="<?= $minPrice ?: '' ?>" placeholder="0">
        </div>
        <div class="col-sm-6 col-lg-2">
            <label class="form-label">Giá đến</label>
            <input class="form-control" type="number" name="max_price" min="0" step="500000" value="<?= $maxPrice ?: '' ?>" placeholder="50.000.000">
        </div>
        <div class="col-sm-6 col-lg-2">
            <label class="form-label">Đánh giá</label>
            <select class="form-select" name="rating">
                <option value="0">Tất cả</option>
                <option value="4" <?= $minRating === 4.0 ? 'selected' : '' ?>>Từ 4 sao</option>
                <option value="3" <?= $minRating === 3.0 ? 'selected' : '' ?>>Từ 3 sao</option>
            </select>
        </div>
        <div class="col-sm-6 col-lg-3">
            <label class="form-label">Sắp xếp</label>
            <select class="form-select" name="sort">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Đánh giá cao</option>
                <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Tên A-Z</option>
            </select>
        </div>
        <div class="col-sm-6 col-lg-3 d-grid align-self-end">
            <button class="btn btn-brand"><i class="bi bi-sliders me-1"></i>Áp dụng bộ lọc</button>
        </div>
    </div>
</form>

<div class="row g-3">
    <?php if (!$products): ?>
        <div class="col-12"><div class="bb-card p-4 text-center text-muted">Không tìm thấy sản phẩm phù hợp.</div></div>
    <?php endif; ?>
    <?php foreach ($products as $product): ?>
        <?php include __DIR__ . '/partials_product_card.php'; ?>
    <?php endforeach; ?>
</div>
<?php render_footer(); ?>
