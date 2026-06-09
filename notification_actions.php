<?php
require_once __DIR__ . '/app/helpers.php';
require_login();
verify_csrf();

$user = current_user();
$stmt = db()->prepare("SELECT id FROM notifications WHERE audience IN ('all', ?)");
$stmt->execute([$user['role']]);
$notifications = $stmt->fetchAll();
$stmt = db()->prepare('INSERT OR IGNORE INTO notification_reads(user_id, notification_id, read_at) VALUES (?, ?, ?)');

foreach ($notifications as $notification) {
    $stmt->execute([$user['id'], (int) $notification['id'], date('Y-m-d H:i:s')]);
}

flash('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
redirect($_POST['back'] ?? 'index.php');
