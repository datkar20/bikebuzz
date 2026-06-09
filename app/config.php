<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');

define('APP_NAME', 'BikeBuzz');
define('BASE_URL', detect_base_url());
define('DB_PATH', __DIR__ . '/../data/bikebuzz.sqlite');
define('UPLOAD_DIR', __DIR__ . '/../uploads');

function detect_base_url(): string
{
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($dir === '.' || $dir === '/') {
        return '';
    }
    $dir = trim($dir, '/');
    $parts = $dir === '' ? [] : explode('/', $dir);
    $last = end($parts);
    if (in_array($last, ['admin', 'auth'], true)) {
        array_pop($parts);
    }
    return $parts ? '/' . implode('/', $parts) : '';
}

function url(string $path = ''): string
{
    $path = ltrim($path, '/');
    $base = BASE_URL === '' || BASE_URL === '.' ? '' : BASE_URL;
    return $base . '/' . $path;
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (!is_dir(dirname(DB_PATH))) {
        mkdir(dirname(DB_PATH), 0777, true);
    }
    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0777, true);
    }

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    initialize_database($pdo);

    return $pdo;
}

function initialize_database(PDO $pdo): void
{
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'user',
            created_at TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL UNIQUE,
            description TEXT DEFAULT ''
        );

        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER,
            name TEXT NOT NULL,
            brand TEXT NOT NULL,
            price INTEGER NOT NULL,
            stock INTEGER NOT NULL DEFAULT 0,
            image TEXT NOT NULL,
            description TEXT NOT NULL,
            featured INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL,
            FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS carts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL,
            UNIQUE(user_id, product_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS notifications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            message TEXT NOT NULL,
            audience TEXT NOT NULL DEFAULT 'all',
            created_at TEXT NOT NULL
        );

        CREATE TABLE IF NOT EXISTS wishlists (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            created_at TEXT NOT NULL,
            UNIQUE(user_id, product_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
            comment TEXT NOT NULL,
            created_at TEXT NOT NULL,
            UNIQUE(user_id, product_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS notification_reads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            notification_id INTEGER NOT NULL,
            read_at TEXT NOT NULL,
            UNIQUE(user_id, notification_id),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE
        );
    ");

    $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count === 0) {
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('INSERT INTO users(name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(['Quản trị BikeBuzz', 'admin@bikebuzz.test', password_hash('admin123', PASSWORD_DEFAULT), 'admin', $now]);
        $stmt->execute(['Khách hàng BikeBuzz', 'user@bikebuzz.test', password_hash('user123', PASSWORD_DEFAULT), 'user', $now]);
    }

    $categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    if ($categoryCount === 0) {
        $categories = [
            ['Xe đạp địa hình', 'Leo dốc, đi trail và đi phượt cuối tuần.'],
            ['Xe đạp đua', 'Tốc độ cao, khung nhẹ, tối ưu cho đường nhựa.'],
            ['Xe đạp thành phố', 'Thiết kế gọn, tiện lợi cho đi học và đi làm.'],
            ['Xe đạp trẻ em', 'An toàn, dễ điều khiển, màu sắc vui mắt.'],
        ];
        $stmt = $pdo->prepare('INSERT INTO categories(name, description) VALUES (?, ?)');
        foreach ($categories as $category) {
            $stmt->execute($category);
        }
    }

    $productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    if ($productCount === 0) {
        $now = date('Y-m-d H:i:s');
        $products = [
            [1, 'Trek Marlin 7 Gen 3', 'Trek', 18900000, 12, 'assets/img/list/bike1.jpg', 'Xe địa hình cân bằng giữa hiệu năng, độ bền và cảm giác lái êm.', 1],
            [1, 'Giant Talon 1', 'Giant', 16500000, 9, 'assets/img/list/bike2.jpg', 'Phù hợp người mới nâng cấp lên phanh đĩa và truyền động ổn định.', 1],
            [2, 'Specialized Allez Sport', 'Specialized', 24500000, 6, 'assets/img/list/bike3.jpg', 'Khung nhôm nhẹ, geometry nhanh, hợp cho tập luyện tốc độ.', 1],
            [2, 'Cervelo P5X Lamborghini', 'Cervelo', 480000000, 2, 'assets/img/bikehot1.png', 'Phiên bản trưng bày cao cấp dành cho người sưu tầm và thi đấu.', 1],
            [3, 'Momentum iNeed Latte', 'Momentum', 9500000, 18, 'assets/img/list/bike4.jpg', 'Xe thành phố thanh lịch, yên êm, dễ gắn giỏ và phụ kiện.', 0],
            [3, 'Cannondale Quick 4', 'Cannondale', 14200000, 10, 'assets/img/list/bike5.jpg', 'Xe hybrid linh hoạt cho đi làm, tập thể dục và dạo phố.', 0],
            [4, 'RoyalBaby Freestyle 20', 'RoyalBaby', 5200000, 14, 'assets/img/list/bike6.jpg', 'Khung chắc chắn, phanh dễ bóp, phù hợp trẻ em năng động.', 0],
            [1, 'Scott Aspect 940', 'Scott', 17200000, 8, 'assets/img/list/bikes21.jpg', 'Lựa chọn MTB gọn gàng với phuộc trước và lốp bám đường tốt.', 0],
        ];
        $stmt = $pdo->prepare('INSERT INTO products(category_id, name, brand, price, stock, image, description, featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($products as $product) {
            $stmt->execute(array_merge($product, [$now]));
        }
    }

    $notificationCount = (int) $pdo->query('SELECT COUNT(*) FROM notifications')->fetchColumn();
    if ($notificationCount === 0) {
        $stmt = $pdo->prepare('INSERT INTO notifications(title, message, audience, created_at) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Chào mừng đến BikeBuzz', 'Đăng nhập để lưu giỏ hàng, yêu thích sản phẩm và gửi đánh giá.', 'all', date('Y-m-d H:i:s')]);
    }

    normalize_seed_text($pdo);
}

function normalize_seed_text(PDO $pdo): void
{
    $categoryUpdates = [
        ['Xe đạp địa hình', 'Leo dốc, đi trail và đi phượt cuối tuần.', 'Xe dap dia hinh'],
        ['Xe đạp đua', 'Tốc độ cao, khung nhẹ, tối ưu cho đường nhựa.', 'Xe dap dua'],
        ['Xe đạp thành phố', 'Thiết kế gọn, tiện lợi cho đi học và đi làm.', 'Xe dap thanh pho'],
        ['Xe đạp trẻ em', 'An toàn, dễ điều khiển, màu sắc vui mắt.', 'Xe dap tre em'],
    ];
    $stmt = $pdo->prepare('UPDATE categories SET name = ?, description = ? WHERE name = ?');
    foreach ($categoryUpdates as $update) {
        $stmt->execute($update);
    }

    $productUpdates = [
        ['Xe địa hình cân bằng giữa hiệu năng, độ bền và cảm giác lái êm.', 'Trek Marlin 7 Gen 3'],
        ['Phù hợp người mới nâng cấp lên phanh đĩa và truyền động ổn định.', 'Giant Talon 1'],
        ['Khung nhôm nhẹ, geometry nhanh, hợp cho tập luyện tốc độ.', 'Specialized Allez Sport'],
        ['Phiên bản trưng bày cao cấp dành cho người sưu tầm và thi đấu.', 'Cervelo P5X Lamborghini'],
        ['Xe thành phố thanh lịch, yên êm, dễ gắn giỏ và phụ kiện.', 'Momentum iNeed Latte'],
        ['Xe hybrid linh hoạt cho đi làm, tập thể dục và dạo phố.', 'Cannondale Quick 4'],
        ['Khung chắc chắn, phanh dễ bóp, phù hợp trẻ em năng động.', 'RoyalBaby Freestyle 20'],
        ['Lựa chọn MTB gọn gàng với phuộc trước và lốp bám đường tốt.', 'Scott Aspect 940'],
    ];
    $stmt = $pdo->prepare('UPDATE products SET description = ? WHERE name = ?');
    foreach ($productUpdates as $update) {
        $stmt->execute($update);
    }
}
