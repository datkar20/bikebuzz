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
    ");

    $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($count === 0) {
        $now = date('Y-m-d H:i:s');
        $stmt = $pdo->prepare('INSERT INTO users(name, email, password, role, created_at) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute(['Admin BikeBuzz', 'admin@bikebuzz.test', password_hash('admin123', PASSWORD_DEFAULT), 'admin', $now]);
        $stmt->execute(['Khach hang mau', 'user@bikebuzz.test', password_hash('user123', PASSWORD_DEFAULT), 'user', $now]);
    }

    $categoryCount = (int) $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
    if ($categoryCount === 0) {
        $categories = [
            ['Xe dap dia hinh', 'Leo doc, di trail va di phuot cuoi tuan.'],
            ['Xe dap dua', 'Toc do cao, khung nhe, toi uu cho duong nhua.'],
            ['Xe dap thanh pho', 'Thiet ke gon, tien loi cho di hoc va di lam.'],
            ['Xe dap tre em', 'An toan, de dieu khien, mau sac vui mat.'],
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
            [1, 'Trek Marlin 7 Gen 3', 'Trek', 18900000, 12, 'assets/img/list/bike1.jpg', 'Xe dia hinh can bang giua hieu nang, do ben va cam giac lai em.', 1],
            [1, 'Giant Talon 1', 'Giant', 16500000, 9, 'assets/img/list/bike2.jpg', 'Phu hop nguoi moi nang cap len phanh dia va truyen dong on dinh.', 1],
            [2, 'Specialized Allez Sport', 'Specialized', 24500000, 6, 'assets/img/list/bike3.jpg', 'Khung nhom nhe, geometry nhanh, hop cho tap luyen toc do.', 1],
            [2, 'Cervelo P5X Lamborghini', 'Cervelo', 480000000, 2, 'assets/img/bikehot1.png', 'Phien ban trien lam cao cap danh cho nguoi suu tam va thi dau.', 1],
            [3, 'Momentum iNeed Latte', 'Momentum', 9500000, 18, 'assets/img/list/bike4.jpg', 'Xe thanh pho thanh lich, yen em, de gan gio va phu kien.', 0],
            [3, 'Cannondale Quick 4', 'Cannondale', 14200000, 10, 'assets/img/list/bike5.jpg', 'Xe hybrid linh hoat cho di lam, tap the duc va dao pho.', 0],
            [4, 'RoyalBaby Freestyle 20', 'RoyalBaby', 5200000, 14, 'assets/img/list/bike6.jpg', 'Khung chac chan, phanh de bop, phu hop tre em nang dong.', 0],
            [1, 'Scott Aspect 940', 'Scott', 17200000, 8, 'assets/img/list/bikes21.jpg', 'Lua chon MTB gon gang voi phuoc truoc va lop bam duong tot.', 0],
        ];
        $stmt = $pdo->prepare('INSERT INTO products(category_id, name, brand, price, stock, image, description, featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        foreach ($products as $product) {
            $stmt->execute(array_merge($product, [$now]));
        }
    }

    $notificationCount = (int) $pdo->query('SELECT COUNT(*) FROM notifications')->fetchColumn();
    if ($notificationCount === 0) {
        $stmt = $pdo->prepare('INSERT INTO notifications(title, message, audience, created_at) VALUES (?, ?, ?, ?)');
        $stmt->execute(['Chao mung den BikeBuzz', 'Dang nhap de them xe vao gio hang va luu gio theo tai khoan.', 'all', date('Y-m-d H:i:s')]);
    }
}
