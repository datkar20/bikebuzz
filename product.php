<?php
require_once __DIR__ . '/app/layout.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.id = ?');
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    http_response_code(404);
    exit('Không tìm thấy sản phẩm.');
}

$rating = product_rating((int) $product['id']);
$stmt = db()->prepare('SELECT r.*, u.name AS user_name FROM reviews r JOIN users u ON u.id = r.user_id WHERE r.product_id = ? ORDER BY r.id DESC');
$stmt->execute([(int) $product['id']]);
$reviews = $stmt->fetchAll();

$userReview = null;
if (current_user()) {
    $stmt = db()->prepare('SELECT * FROM reviews WHERE user_id = ? AND product_id = ?');
    $stmt->execute([current_user()['id'], (int) $product['id']]);
    $userReview = $stmt->fetch();
}

render_header($product['name'], 'products');
?>
<a class="btn btn-outline-dark mb-3" href="<?= url('products.php') ?>"><i class="bi bi-arrow-left me-1"></i>Quay lại danh sách xe</a>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="bb-card p-4 text-center">
            <img class="img-fluid product-detail-img" src="<?= e(product_image($product['image'])) ?>" alt="<?= e($product['name']) ?>">
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bb-card p-4 h-100">
            <div class="text-muted mb-1"><?= e($product['brand']) ?> · <?= e($product['category_name'] ?? 'Khac') ?></div>
            <h1 class="page-title h2 mb-2"><?= e($product['name']) ?></h1>
            <div class="mb-3">
                <span class="rating-stars"><i class="bi bi-star-fill"></i></span>
                <span class="text-muted"><?= $rating['total'] ? e((string) $rating['average']) . '/5 từ ' . (int) $rating['total'] . ' đánh giá' : 'Chưa có đánh giá' ?></span>
            </div>
            <div class="h3 fw-bold text-success mb-3"><?= money((int) $product['price']) ?></div>
            <p class="text-muted"><?= e($product['description']) ?></p>
            <div class="mb-4"><span class="badge badge-soft">Còn <?= (int) $product['stock'] ?> xe</span></div>
            <div class="d-flex flex-wrap gap-2">
                <?php if (current_user()): ?>
                    <form method="post" action="<?= url('cart_actions.php') ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <input type="hidden" name="back" value="product.php?id=<?= (int) $product['id'] ?>">
                        <button class="btn btn-brand"><i class="bi bi-cart-plus-fill me-1"></i>Thêm vào giỏ</button>
                    </form>
                    <form method="post" action="<?= url('wishlist_actions.php') ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="toggle">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <input type="hidden" name="back" value="product.php?id=<?= (int) $product['id'] ?>">
                        <button class="btn btn-outline-dark"><i class="bi <?= is_wishlisted((int) $product['id']) ? 'bi-heart-fill text-danger' : 'bi-heart' ?> me-1"></i>Yêu thích</button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-brand" href="<?= url('auth/login.php') ?>">Đăng nhập để thêm vào giỏ</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-5">
        <div class="bb-card p-4">
            <h2 class="h5 fw-bold mb-3">Đánh giá sản phẩm</h2>
            <?php if (current_user()): ?>
                <form method="post" action="<?= url('review_actions.php') ?>">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Số sao</label>
                        <select class="form-select" name="rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>" <?= (int) ($userReview['rating'] ?? 5) === $i ? 'selected' : '' ?>><?= $i ?> sao</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nhận xét</label>
                        <textarea class="form-control" name="comment" rows="4" required><?= e($userReview['comment'] ?? '') ?></textarea>
                    </div>
                    <button class="btn btn-brand">Gửi đánh giá</button>
                </form>
            <?php else: ?>
                <div class="text-muted">Đăng nhập để viết đánh giá cho sản phẩm này.</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="bb-card p-4">
            <h2 class="h5 fw-bold mb-3">Nhận xét khách hàng</h2>
            <?php if (!$reviews): ?>
                <div class="text-muted">Chưa có nhận xét nào.</div>
            <?php endif; ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review-item">
                    <div class="d-flex justify-content-between gap-2">
                        <div class="fw-bold"><?= e($review['user_name']) ?></div>
                        <div class="rating-stars"><?= str_repeat('<i class="bi bi-star-fill"></i>', (int) $review['rating']) ?></div>
                    </div>
                    <div class="small text-muted mb-1"><?= e($review['created_at']) ?></div>
                    <div><?= e($review['comment']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php render_footer(); ?>
