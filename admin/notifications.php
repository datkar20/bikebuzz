<?php
require_once __DIR__ . '/../app/layout.php';
require_admin();

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM notifications WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Đã xóa thông báo.');
    } else {
        $title = trim($_POST['title'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $audience = in_array($_POST['audience'] ?? 'all', ['all', 'admin', 'user'], true) ? $_POST['audience'] : 'all';
        if ($title === '' || $message === '') {
            flash('warning', 'Vui lòng nhập tiêu đề và nội dung.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO notifications(title, message, audience, created_at) VALUES (?, ?, ?, ?)');
            $stmt->execute([$title, $message, $audience, date('Y-m-d H:i:s')]);
            flash('success', 'Đã gửi thông báo.');
        }
    }
    redirect('admin/notifications.php');
}

$notifications = $pdo->query('SELECT * FROM notifications ORDER BY id DESC')->fetchAll();
render_header('Thông báo', 'admin-notifications');
?>
<h1 class="page-title h3 mb-3">Thông báo admin/user</h1>
<div class="row g-3">
    <div class="col-lg-4">
        <form class="bb-card p-3" method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="create">
            <h2 class="h5 fw-bold">Tạo thông báo</h2>
            <div class="mb-3"><label class="form-label">Tiêu đề</label><input class="form-control" name="title" required></div>
            <div class="mb-3"><label class="form-label">Nội dung</label><textarea class="form-control" name="message" rows="3" required></textarea></div>
            <div class="mb-3">
                <label class="form-label">Người nhận</label>
                <select class="form-select" name="audience">
                    <option value="all">Tất cả</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button class="btn btn-brand">Gửi thông báo</button>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="bb-card p-3 table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Tiêu đề</th><th>Nội dung</th><th>Nhận</th><th>Ngày</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($notifications as $notice): ?>
                        <tr>
                            <td class="fw-bold"><?= e($notice['title']) ?></td>
                            <td><?= e($notice['message']) ?></td>
                            <td><span class="badge badge-soft"><?= e($notice['audience']) ?></span></td>
                            <td><?= e($notice['created_at']) ?></td>
                            <td class="text-end">
                                <form method="post" data-confirm="Xóa thông báo này?">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $notice['id'] ?>">
                                    <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php render_footer(); ?>
