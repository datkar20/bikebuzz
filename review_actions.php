<?php
require_once __DIR__ . '/app/helpers.php';
require_login();
verify_csrf();

$user = current_user();
$productId = (int) ($_POST['product_id'] ?? 0);
$rating = max(1, min(5, (int) ($_POST['rating'] ?? 5)));
$comment = trim($_POST['comment'] ?? '');

$stmt = db()->prepare('SELECT id FROM products WHERE id = ?');
$stmt->execute([$productId]);
if (!$stmt->fetchColumn()) {
    flash('error', 'San pham khong ton tai.');
    redirect('products.php');
}

if ($comment === '') {
    flash('warning', 'Vui long nhap noi dung danh gia.');
    redirect('product.php?id=' . $productId);
}

$stmt = db()->prepare('INSERT INTO reviews(user_id, product_id, rating, comment, created_at) VALUES (?, ?, ?, ?, ?) ON CONFLICT(user_id, product_id) DO UPDATE SET rating = excluded.rating, comment = excluded.comment, created_at = excluded.created_at');
$stmt->execute([$user['id'], $productId, $rating, $comment, date('Y-m-d H:i:s')]);
flash('success', 'Da luu danh gia cua ban.');
redirect('product.php?id=' . $productId);
