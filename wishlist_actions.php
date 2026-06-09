<?php
require_once __DIR__ . '/app/helpers.php';
require_login();
verify_csrf();

$user = current_user();
$productId = (int) ($_POST['product_id'] ?? 0);
$action = $_POST['action'] ?? 'toggle';

$stmt = db()->prepare('SELECT id FROM products WHERE id = ?');
$stmt->execute([$productId]);
if (!$stmt->fetchColumn()) {
    flash('error', 'Sản phẩm không tồn tại.');
    redirect('products.php');
}

$stmt = db()->prepare('SELECT id FROM wishlists WHERE user_id = ? AND product_id = ?');
$stmt->execute([$user['id'], $productId]);
$wishlistId = $stmt->fetchColumn();

if ($action === 'remove' || $wishlistId) {
    $stmt = db()->prepare('DELETE FROM wishlists WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$user['id'], $productId]);
    flash('success', 'Đã bỏ khỏi danh sách yêu thích.');
} else {
    $stmt = db()->prepare('INSERT INTO wishlists(user_id, product_id, created_at) VALUES (?, ?, ?)');
    $stmt->execute([$user['id'], $productId, date('Y-m-d H:i:s')]);
    flash('success', 'Đã lưu vào danh sách yêu thích.');
}

$back = $_POST['back'] ?? 'wishlist.php';
redirect($back);
