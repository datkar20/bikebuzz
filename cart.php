<?php
require_once __DIR__ . '/app/layout.php';
require_login();

$user = current_user();
$stmt = db()->prepare('SELECT c.*, p.name, p.brand, p.price, p.stock, p.image FROM carts c JOIN products p ON p.id = c.product_id WHERE c.user_id = ? ORDER BY c.id DESC');
$stmt->execute([$user['id']]);
$items = $stmt->fetchAll();
$total = array_sum(array_map(fn ($item) => (int) $item['price'] * (int) $item['quantity'], $items));

render_header('Gio hang', 'cart');
?>
<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h1 class="page-title h3 mb-1">Gio hang cua <?= e($user['name']) ?></h1>
        <div class="text-muted">Gio hang nay duoc luu rieng theo tai khoan dang nhap.</div>
    </div>
    <a class="btn btn-outline-dark" href="<?= url('products.php') ?>"><i class="bi bi-plus-lg me-1"></i>Them xe</a>
</div>

<div class="bb-card p-3">
    <?php if (!$items): ?>
        <div class="text-center p-5 text-muted">Gio hang trong. Hay chon mot chiec xe hop gu.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>San pham</th>
                        <th>Gia</th>
                        <th style="width: 180px;">So luong</th>
                        <th>Tam tinh</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img class="thumb" src="<?= e(product_image($item['image'])) ?>" alt="<?= e($item['name']) ?>">
                                    <div><div class="fw-bold"><?= e($item['name']) ?></div><div class="small text-muted"><?= e($item['brand']) ?></div></div>
                                </div>
                            </td>
                            <td><?= money((int) $item['price']) ?></td>
                            <td>
                                <form class="d-flex gap-2" method="post" action="<?= url('cart_actions.php') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                    <input class="form-control form-control-sm" type="number" name="quantity" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>">
                                    <button class="btn btn-outline-dark btn-sm"><i class="bi bi-check2"></i></button>
                                </form>
                            </td>
                            <td class="fw-bold"><?= money((int) $item['price'] * (int) $item['quantity']) ?></td>
                            <td>
                                <form method="post" action="<?= url('cart_actions.php') ?>" data-confirm="Xoa san pham khoi gio?">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-end">
            <div class="text-end">
                <div class="text-muted">Tong tam tinh</div>
                <div class="h4 fw-bold text-success"><?= money($total) ?></div>
                <button class="btn btn-brand" disabled>Thanh toan se bo sung sau</button>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php render_footer(); ?>
