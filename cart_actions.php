<?php
require_once __DIR__ . '/app/helpers.php';
require_login();
verify_csrf();

$user = current_user();
$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM products WHERE id = ?');
$stmt->execute([$productId]);
$product = $stmt->fetch();
if (!$product) {
    flash('error', 'Sản phẩm không tồn tại.');
    redirect('products.php');
}

if ($action === 'add') {
    if ((int) $product['stock'] <= 0) {
        flash('warning', 'Sản phẩm đã hết hàng.');
        redirect('products.php');
    }
    $stmt = db()->prepare('INSERT INTO carts(user_id, product_id, quantity, created_at) VALUES (?, ?, 1, ?) ON CONFLICT(user_id, product_id) DO UPDATE SET quantity = MIN(quantity + 1, ?)');
    $stmt->execute([$user['id'], $productId, date('Y-m-d H:i:s'), (int) $product['stock']]);
    flash('success', 'Đã thêm vào giỏ hàng.');
} elseif ($action === 'update') {
    $quantity = max(1, min((int) ($_POST['quantity'] ?? 1), (int) $product['stock']));
    $stmt = db()->prepare('UPDATE carts SET quantity = ? WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$quantity, $user['id'], $productId]);
    flash('success', 'Đã cập nhật giỏ hàng.');
} elseif ($action === 'remove') {
    $stmt = db()->prepare('DELETE FROM carts WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$user['id'], $productId]);
    flash('success', 'Đã xóa sản phẩm khỏi giỏ.');
}

$back = $_POST['back'] ?? 'cart.php';
redirect($back);
