<?php
require_once __DIR__ . '/../app/layout.php';

if (current_user()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = (int) $user['id'];
        flash('success', 'Dang nhap thanh cong.');
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'index.php');
    }

    flash('error', 'Email hoac mat khau khong dung.');
    redirect('auth/login.php');
}

render_header('Dang nhap', '');
?>
<div class="auth-wrap">
    <div class="bb-card auth-card p-4">
        <h1 class="h3 fw-bold mb-1">Dang nhap</h1>
        <p class="text-muted mb-4">Them xe vao gio va quan ly tai khoan BikeBuzz.</p>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input class="form-control" type="email" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Mat khau</label>
                <input class="form-control" type="password" name="password" required>
            </div>
            <button class="btn btn-brand w-100">Dang nhap</button>
        </form>
        <div class="mt-3 text-center">Chua co tai khoan? <a href="<?= url('auth/register.php') ?>">Dang ky</a></div>
    </div>
</div>
<?php render_footer(); ?>
