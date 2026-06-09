<?php
require_once __DIR__ . '/app/layout.php';
require_login();
$user = current_user();
render_header('Tài khoản', 'account');
?>
<h1 class="page-title h3 mb-3">Tài khoản</h1>
<div class="bb-card p-4">
    <div class="row g-3">
        <div class="col-md-4 text-muted">Họ tên</div>
        <div class="col-md-8 fw-bold"><?= e($user['name']) ?></div>
        <div class="col-md-4 text-muted">Email</div>
        <div class="col-md-8"><?= e($user['email']) ?></div>
        <div class="col-md-4 text-muted">Vai trò</div>
        <div class="col-md-8"><span class="badge badge-soft"><?= e($user['role']) ?></span></div>
        <div class="col-md-4 text-muted">Ngày tạo</div>
        <div class="col-md-8"><?= e($user['created_at']) ?></div>
    </div>
</div>
<?php render_footer(); ?>
