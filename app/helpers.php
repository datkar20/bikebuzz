<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, name, email, role, created_at FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user && $user['role'] === 'admin';
}

function require_login(): void
{
    if (!current_user()) {
        flash('warning', 'Ban can dang nhap de su dung tinh nang nay.');
        redirect('auth/login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        http_response_code(403);
        exit('Ban khong co quyen truy cap khu vuc quan tri.');
    }
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function current_relative_url(): string
{
    $script = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');
    $query = $_SERVER['QUERY_STRING'] ?? '';
    return $script . ($query !== '' ? '?' . $query : '');
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function take_flash(): ?array
{
    if (empty($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function money(int|float $amount): string
{
    return number_format((float) $amount, 0, ',', '.') . ' VND';
}

function starts_with(string $value, string $prefix): bool
{
    return substr($value, 0, strlen($prefix)) === $prefix;
}

function excerpt(string $value, int $limit = 92): string
{
    $value = trim($value);
    if (strlen($value) <= $limit) {
        return $value;
    }
    return rtrim(substr($value, 0, $limit - 3)) . '...';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('CSRF token khong hop le.');
    }
}

function product_image(string $image): string
{
    if (starts_with($image, 'http://') || starts_with($image, 'https://')) {
        return $image;
    }
    return url($image);
}

function cart_count(): int
{
    $user = current_user();
    if (!$user) {
        return 0;
    }
    $stmt = db()->prepare('SELECT COALESCE(SUM(quantity), 0) FROM carts WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    return (int) $stmt->fetchColumn();
}

function wishlist_count(): int
{
    $user = current_user();
    if (!$user) {
        return 0;
    }
    $stmt = db()->prepare('SELECT COUNT(*) FROM wishlists WHERE user_id = ?');
    $stmt->execute([$user['id']]);
    return (int) $stmt->fetchColumn();
}

function is_wishlisted(int $productId): bool
{
    $user = current_user();
    if (!$user) {
        return false;
    }
    $stmt = db()->prepare('SELECT 1 FROM wishlists WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$user['id'], $productId]);
    return (bool) $stmt->fetchColumn();
}

function product_rating(int $productId): array
{
    $stmt = db()->prepare('SELECT ROUND(AVG(rating), 1) AS average_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = ?');
    $stmt->execute([$productId]);
    $rating = $stmt->fetch() ?: ['average_rating' => null, 'total_reviews' => 0];
    return [
        'average' => $rating['average_rating'] ? (float) $rating['average_rating'] : 0,
        'total' => (int) $rating['total_reviews'],
    ];
}

function latest_notifications(?string $role = null, int $limit = 5): array
{
    $audiences = ['all'];
    if ($role) {
        $audiences[] = $role;
    }
    $placeholders = implode(',', array_fill(0, count($audiences), '?'));
    $stmt = db()->prepare("SELECT * FROM notifications WHERE audience IN ($placeholders) ORDER BY id DESC LIMIT $limit");
    $stmt->execute($audiences);
    return $stmt->fetchAll();
}

function upload_image_or_url(?array $file, string $imageUrl, string $fallback = ''): string
{
    $imageUrl = trim($imageUrl);
    if ($imageUrl !== '') {
        return $imageUrl;
    }

    if ($file && ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        $type = mime_content_type($file['tmp_name']);
        if (!isset($allowed[$type])) {
            flash('error', 'Chi chap nhan anh JPG, PNG, WEBP hoac GIF.');
            return $fallback;
        }
        $name = 'bike-' . time() . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$type];
        move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $name);
        return 'uploads/' . $name;
    }

    return $fallback;
}
