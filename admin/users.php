<?php
require_once __DIR__ . '/../app/layout.php';
require_admin();

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);
    if ($id === (int) current_user()['id'] && in_array($action, ['delete', 'role'], true)) {
        flash('warning', 'Khong nen tu xoa hoac doi quyen tai khoan dang dang nhap.');
        redirect('admin/users.php');
    }
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM users WHERE id = ?');
        $stmt->execute([$id]);
        flash('success', 'Da xoa nguoi dung.');
    } elseif ($action === 'role') {
        $role = $_POST['role'] === 'admin' ? 'admin' : 'user';
        $stmt = $pdo->prepare('UPDATE users SET role = ? WHERE id = ?');
        $stmt->execute([$role, $id]);
        flash('success', 'Da cap nhat quyen.');
    }
    redirect('admin/users.php');
}

$users = $pdo->query('SELECT u.*, COALESCE(SUM(c.quantity), 0) AS cart_items FROM users u LEFT JOIN carts c ON c.user_id = u.id GROUP BY u.id ORDER BY u.id DESC')->fetchAll();
render_header('Nguoi dung', 'admin-users');
?>
<h1 class="page-title h3 mb-3">Quan ly nguoi dung</h1>
<div class="bb-card p-3 table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Nguoi dung</th><th>Email</th><th>Vai tro</th><th>Gio hang</th><th>Ngay tao</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td class="fw-bold"><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td>
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="role">
                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                            <select class="form-select form-select-sm" name="role">
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>user</option>
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>admin</option>
                            </select>
                            <button class="btn btn-outline-dark btn-sm"><i class="bi bi-check2"></i></button>
                        </form>
                    </td>
                    <td><?= (int) $user['cart_items'] ?></td>
                    <td><?= e($user['created_at']) ?></td>
                    <td class="text-end">
                        <form method="post" data-confirm="Xoa nguoi dung nay?">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php render_footer(); ?>
