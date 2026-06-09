<?php
require_once __DIR__ . '/../app/layout.php';
require_admin();

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Đã xóa đánh giá.');
    }
    redirect('admin/reviews.php');
}

$reviews = $pdo->query('SELECT r.*, u.name AS user_name, p.name AS product_name FROM reviews r JOIN users u ON u.id = r.user_id JOIN products p ON p.id = r.product_id ORDER BY r.id DESC')->fetchAll();

render_header('Đánh giá', 'admin-reviews');
?>
<h1 class="page-title h3 mb-3">Quản lý đánh giá</h1>
<div class="bb-card p-3 table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Sản phẩm</th><th>Người dùng</th><th>Số sao</th><th>Nội dung</th><th>Ngày</th><th></th></tr></thead>
        <tbody>
            <?php if (!$reviews): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Chưa có đánh giá nào.</td></tr>
            <?php endif; ?>
            <?php foreach ($reviews as $review): ?>
                <tr>
                    <td class="fw-bold"><?= e($review['product_name']) ?></td>
                    <td><?= e($review['user_name']) ?></td>
                    <td><span class="rating-stars"><?= str_repeat('<i class="bi bi-star-fill"></i>', (int) $review['rating']) ?></span></td>
                    <td><?= e($review['comment']) ?></td>
                    <td><?= e($review['created_at']) ?></td>
                    <td class="text-end">
                        <form method="post" data-confirm="Xóa đánh giá này?">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $review['id'] ?>">
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php render_footer(); ?>
