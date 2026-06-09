<?php $user = current_user(); ?>
<div class="col-sm-6 col-xl-3">
    <div class="bb-card product-card">
        <img class="product-img" src="<?= e(product_image($product['image'])) ?>" alt="<?= e($product['name']) ?>">
        <div class="p-3">
            <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                <div>
                    <div class="small text-muted"><?= e($product['brand']) ?> · <?= e($product['category_name'] ?? 'Khac') ?></div>
                    <h3 class="h6 fw-bold mb-0"><?= e($product['name']) ?></h3>
                </div>
                <?php if ((int) $product['featured'] === 1): ?><span class="badge badge-soft">Hot</span><?php endif; ?>
            </div>
            <p class="small text-muted mb-3"><?= e(excerpt($product['description'])) ?></p>
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div class="fw-bold text-success"><?= money((int) $product['price']) ?></div>
                <?php if ($user): ?>
                    <form method="post" action="<?= url('cart_actions.php') ?>">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                        <button class="btn btn-brand btn-sm" title="Them vao gio"><i class="bi bi-bag-plus"></i></button>
                    </form>
                <?php else: ?>
                    <a class="btn btn-outline-dark btn-sm" href="<?= url('auth/login.php') ?>" title="Dang nhap de them gio"><i class="bi bi-lock"></i></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
