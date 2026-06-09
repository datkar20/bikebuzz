<?php
require_once __DIR__ . '/../app/layout.php';
require_admin();

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM categories WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Đã xóa danh mục.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        if ($name === '') {
            flash('warning', 'Tên danh mục không được để trống.');
        } elseif ($action === 'create') {
            $stmt = $pdo->prepare('INSERT INTO categories(name, description) VALUES (?, ?)');
            $stmt->execute([$name, $description]);
            flash('success', 'Đã thêm danh mục.');
        } elseif ($action === 'update') {
            $stmt = $pdo->prepare('UPDATE categories SET name = ?, description = ? WHERE id = ?');
            $stmt->execute([$name, $description, (int) $_POST['id']]);
            flash('success', 'Đã cập nhật danh mục.');
        }
    }
    redirect('admin/categories.php');
}

$edit = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM categories WHERE id = ?');
    $stmt->execute([(int) $_GET['edit']]);
    $edit = $stmt->fetch();
}
$categories = $pdo->query('SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id ORDER BY c.name')->fetchAll();

render_header('Danh mục', 'admin-categories');
?>
<h1 class="page-title h3 mb-3">Quản lý danh mục</h1>
<div class="row g-3">
    <div class="col-lg-4">
        <form class="bb-card p-3" method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="<?= $edit ? 'update' : 'create' ?>">
            <input type="hidden" name="id" value="<?= (int) ($edit['id'] ?? 0) ?>">
            <h2 class="h5 fw-bold"><?= $edit ? 'Sửa danh mục' : 'Thêm danh mục' ?></h2>
            <div class="mb-3"><label class="form-label">Tên</label><input class="form-control" name="name" required value="<?= e($edit['name'] ?? '') ?>"></div>
            <div class="mb-3"><label class="form-label">Mô tả</label><textarea class="form-control" name="description" rows="3"><?= e($edit['description'] ?? '') ?></textarea></div>
            <button class="btn btn-brand"><?= $edit ? 'Cập nhật' : 'Thêm mới' ?></button>
            <?php if ($edit): ?><a class="btn btn-outline-dark" href="<?= url('admin/categories.php') ?>">Hủy</a><?php endif; ?>
        </form>
    </div>
    <div class="col-lg-8">
        <div class="bb-card p-3 table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Tên</th><th>Mô tả</th><th>Sản phẩm</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td class="fw-bold"><?= e($category['name']) ?></td>
                            <td><?= e($category['description']) ?></td>
                            <td><?= (int) $category['product_count'] ?></td>
                            <td class="text-end">
                                <a class="btn btn-outline-dark btn-sm" href="<?= url('admin/categories.php?edit=' . (int) $category['id']) ?>"><i class="bi bi-pencil"></i></a>
                                <form class="d-inline" method="post" data-confirm="Xóa danh mục này? Sản phẩm sẽ chuyển về Khác.">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= (int) $category['id'] ?>">
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
