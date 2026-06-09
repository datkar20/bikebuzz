<?php
$user = current_user();
$rating = product_rating((int) $product['id']);
?>
<div class="col-sm-6 col-xl-3">
    <div class="bb-card product-card">
        <a href="<?= url('product.php?id=' . (int) $product['id']) ?>">
            <img class="product-img" src="<?= e(product_image($product['image'])) ?>" alt="<?= e($product['name']) ?>">
        </a>
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div>
                    <div class="small text-muted"><?= e($product['brand']) ?> · <?= e($product['category_name'] ?? 'Khac') ?></div>
                    <h3 class="h6 fw-bold mb-0"><a class="text-dark" href="<?= url('product.php?id=' . (int) $product['id']) ?>"><?= e($product['name']) ?></a></h3>
                </div>
                <?php if ((int) $product['featured'] === 1): ?><span class="badge badge-soft">Hot</span><?php endif; ?>
            </div>
            <div class="small mb-2">
                <span class="rating-stars"><i class="bi bi-star-fill"></i></span>
                <span class="text-muted"><?= $rating['total'] ? e((string) $rating['average']) . '/5 · ' . (int) $rating['total'] . ' đánh giá' : 'Chưa có đánh giá' ?></span>
            </div>
            <p class="small text-muted mb-3"><?= e(excerpt($product['description'])) ?></p>
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div class="fw-bold text-success"><?= money((int) $product['price']) ?></div>
                <div class="product-actions">
                    <?php if ($user): ?>
                        <form method="post" action="<?= url('wishlist_actions.php') ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="back" value="<?= e(current_relative_url()) ?>">
                            <button class="btn btn-outline-dark btn-sm" title="Yêu thích"><i class="bi <?= is_wishlisted((int) $product['id']) ? 'bi-heart-fill text-danger' : 'bi-heart' ?>"></i></button>
                        </form>
                        <form method="post" action="<?= url('cart_actions.php') ?>">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="back" value="<?= e(current_relative_url()) ?>">
                            <button class="btn btn-brand btn-sm btn-cart-add" title="Thêm vào giỏ"><i class="bi bi-cart-plus-fill"></i></button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-outline-dark btn-sm" href="<?= url('auth/login.php') ?>" title="Đăng nhập để thêm giỏ"><i class="bi bi-lock"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
