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
        flash('warning', 'Vui long nhap dung thong tin, mat khau toi thieu 6 ky tu va xac nhan khop.');
        redirect('auth/register.php');
    }

    try {
        $stmt = db()->prepare('INSERT INTO users(name, email, password, role, created_at) VALUES (?, ?, ?, "user", ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
        $_SESSION['user_id'] = (int) db()->lastInsertId();
        flash('success', 'Tao tai khoan thanh cong.');
        redirect('index.php');
    } catch (PDOException $exception) {
        flash('error', 'Email nay da duoc su dung.');
        redirect('auth/register.php');
    }
}

render_header('Tao tai khoan', '');
?>
<div class="auth-wrap">
    <div class="bb-card auth-card p-4">
        <h1 class="h3 fw-bold mb-1">Tao tai khoan</h1>
        <p class="text-muted mb-4">Dang ky de luu gio hang theo tai khoan.</p>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="mb-3">
                <label class="form-label">Ho ten</label>
                <input class="form-control" name="name" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mat khau</label>
                <input class="form-control" type="password" name="password" minlength="6" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nhap lai mat khau</label>
                <input class="form-control" type="password" name="confirm_password" minlength="6" required>
            </div>
            <button class="btn btn-brand w-100">Tao tai khoan</button>
        </form>
        <div class="mt-3 text-center">Da co tai khoan? <a href="<?= url('auth/login.php') ?>">Dang nhap</a></div>
    </div>
</div>
<?php render_footer(); ?>
