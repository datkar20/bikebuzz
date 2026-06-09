<?php
require_once __DIR__ . '/../app/layout.php';

if (current_user()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6 || $password !== $confirm) {
        flash('warning', 'Vui lòng nhập đúng thông tin, mật khẩu tối thiểu 6 ký tự và xác nhận khớp.');
        redirect('auth/register.php');
    }

    try {
        $stmt = db()->prepare('INSERT INTO users(name, email, password, role, created_at) VALUES (?, ?, ?, "user", ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
        $_SESSION['user_id'] = (int) db()->lastInsertId();
        flash('success', 'Tạo tài khoản thành công.');
        redirect('index.php');
    } catch (PDOException $exception) {
        flash('error', 'Email nay da duoc su dung.');
        redirect('auth/register.php');
    }
}

render_header('Tạo tài khoản', '');
?>
<div class="auth-wrap">
    <div class="bb-card auth-card p-4">
        <h1 class="h3 fw-bold mb-1">Tạo tài khoản</h1>
        <p class="text-muted mb-4">Đăng ký để lưu giỏ hàng theo tài khoản.</p>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="mb-3">
                <label class="form-label">Họ tên</label>
                <input class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mật khẩu</label>
                <input class="form-control" type="password" name="password" minlength="6" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nhập lại mật khẩu</label>
                <input class="form-control" type="password" name="confirm_password" minlength="6" required>
            </div>
            <button class="btn btn-brand w-100">Tạo tài khoản</button>
        </form>
        <div class="mt-3 text-center">Đã có tài khoản? <a href="<?= url('auth/login.php') ?>">Đăng nhập</a></div>
    </div>
</div>
<?php render_footer(); ?>
