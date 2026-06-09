<?php
require_once __DIR__ . '/../app/layout.php';
require_admin();

$pdo = db();
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $postAction = $_POST['action'] ?? '';
    if ($postAction === 'delete') {
        $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
        $stmt->execute([(int) $_POST['id']]);
        flash('success', 'Đã xóa sản phẩm.');
        redirect('admin/products.php');
    }

    $name = trim($_POST['name'] ?? '');
    $brand = trim($_POST['brand'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0) ?: null;
    $price = max(0, (int) ($_POST['price'] ?? 0));
    $stock = max(0, (int) ($_POST['stock'] ?? 0));
    $description = trim($_POST['description'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $currentImage = $_POST['current_image'] ?? '';
    $image = upload_image_or_url($_FILES['image_file'] ?? null, $_POST['image_url'] ?? '', $currentImage);

    if ($name === '' || $brand === '' || $description === '' || $image === '') {
        flash('warning', 'Vui lòng nhập đầy đủ thông tin sản phẩm và ảnh.');
        redirect('admin/products.php?action=' . ($postAction === 'create' ? 'create' : 'edit&id=' . (int) $_POST['id']));
    }

    if ($postAction === 'create') {
        $stmt = $pdo->prepare('INSERT INTO products(category_id, name, brand, price, stock, image, description, featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$categoryId, $name, $brand, $price, $stock, $image, $description, $featured, date('Y-m-d H:i:s')]);
        flash('success', 'Đã thêm sản phẩm.');
    } elseif ($postAction === 'update') {
        $stmt = $pdo->prepare('UPDATE products SET category_id = ?, name = ?, brand = ?, price = ?, stock = ?, image = ?, description = ?, featured = ? WHERE id = ?');
        $stmt->execute([$categoryId, $name, $brand, $price, $stock, $image, $description, $featured, (int) $_POST['id']]);
        flash('success', 'Đã cập nhật sản phẩm.');
    }
    redirect('admin/products.php');
}

if ($action === 'create' || ($action === 'edit' && $id > 0)) {
    $product = ['id' => 0, 'category_id' => '', 'name' => '', 'brand' => '', 'price' => 0, 'stock' => 0, 'image' => '', 'description' => '', 'featured' => 0];
    if ($action === 'edit') {
        $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
        $stmt->execute([$id]);
        $product = $stmt->fetch();
        if (!$product) {
            flash('error', 'Không tìm thấy sản phẩm.');
            redirect('admin/products.php');
        }
    }
    render_header($action === 'create' ? 'Thêm sản phẩm' : 'Sửa sản phẩm', 'admin-products');
    ?>
    <h1 class="page-title h3 mb-3"><?= $action === 'create' ? 'Thêm sản phẩm' : 'Sửa sản phẩm' ?></h1>
    <form class="bb-card p-4" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <input type="hidden" name="action" value="<?= $action === 'create' ? 'create' : 'update' ?>">
        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
        <input type="hidden" name="current_image" value="<?= e($product['image']) ?>">
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label">Tên xe</label><input class="form-control" name="name" required value="<?= e($product['name']) ?>"></div>
            <div class="col-md-6"><label class="form-label">Thương hiệu</label><input class="form-control" name="brand" required value="<?= e($product['brand']) ?>"></div>
            <div class="col-md-4"><label class="form-label">Danh mục</label><select class="form-select" name="category_id"><option value="">Khác</option><?php foreach ($categories as $category): ?><option value="<?= (int) $category['id'] ?>" <?= (int) $product['category_id'] === (int) $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="form-label">Giá</label><input class="form-control" type="number" name="price" min="0" required value="<?= (int) $product['price'] ?>"></div>
            <div class="col-md-4"><label class="form-label">Ton kho</label><input class="form-control" type="number" name="stock" min="0" required value="<?= (int) $product['stock'] ?>"></div>
            <div class="col-md-6"><label class="form-label">Anh URL Cloudinary/Firebase Storage</label><input class="form-control" name="image_url" placeholder="https://..." value="<?= starts_with($product['image'], 'http') ? e($product['image']) : '' ?>"></div>
            <div class="col-md-6"><label class="form-label">Hoac upload anh local</label><input class="form-control" type="file" name="image_file" accept="image/*"></div>
            <?php if ($product['image']): ?><div class="col-12"><img class="thumb" src="<?= e(product_image($product['image'])) ?>" alt=""></div><?php endif; ?>
            <div class="col-12"><label class="form-label">Mô tả</label><textarea class="form-control" name="description" rows="4" required><?= e($product['description']) ?></textarea></div>
            <div class="col-12"><label class="form-check"><input class="form-check-input" type="checkbox" name="featured" <?= (int) $product['featured'] === 1 ? 'checked' : '' ?>> <span class="form-check-label">Sản phẩm nổi bật</span></label></div>
            <div class="col-12 d-flex gap-2"><button class="btn btn-brand">Lưu</button><a class="btn btn-outline-dark" href="<?= url('admin/products.php') ?>">Hủy</a></div>
        </div>
    </form>
    <?php render_footer(); exit; ?>
    <?php
}

$products = $pdo->query('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.id DESC')->fetchAll();
render_header('Quản lý sản phẩm', 'admin-products');
?>
<div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
    <h1 class="page-title h3 mb-0">Quản lý sản phẩm</h1>
    <a class="btn btn-brand" href="<?= url('admin/products.php?action=create') ?>"><i class="bi bi-plus-lg me-1"></i>Thêm sản phẩm</a>
</div>
<div class="bb-card p-3 table-responsive">
    <table class="table align-middle mb-0">
        <thead><tr><th>Sản phẩm</th><th>Danh mục</th><th>Giá</th><th>Kho</th><th>Nổi bật</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><img class="thumb me-2" src="<?= e(product_image($product['image'])) ?>" alt=""> <strong><?= e($product['name']) ?></strong><div class="small text-muted"><?= e($product['brand']) ?></div></td>
                    <td><?= e($product['category_name'] ?? 'Khac') ?></td>
                    <td><?= money((int) $product['price']) ?></td>
                    <td><?= (int) $product['stock'] ?></td>
                    <td><?= (int) $product['featured'] ? '<span class="badge badge-soft">Hot</span>' : '<span class="text-muted">-</span>' ?></td>
                    <td class="text-end">
                        <a class="btn btn-outline-dark btn-sm" href="<?= url('admin/products.php?action=edit&id=' . (int) $product['id']) ?>"><i class="bi bi-pencil"></i></a>
                        <form class="d-inline" method="post" data-confirm="Xóa sản phẩm này?">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php render_footer(); ?>
