<?php
require_once __DIR__ . '/helpers.php';

function render_header(string $title = APP_NAME, string $active = ''): void
{
    $user = current_user();
    $flash = take_flash();
    $notifications = latest_notifications($user['role'] ?? null);
    ?>
    <!doctype html>
    <html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> - <?= APP_NAME ?></title>
        <link rel="icon" href="<?= url('assets/img/logobikebuzz.png') ?>">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= url('assets/vendor/bootstrap/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" href="<?= url('assets/vendor/bootstrap-icons/bootstrap-icons.css') ?>">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="<?= url('assets/css/bikebuzz.css') ?>">
    </head>
    <body>
    <nav class="navbar navbar-expand-lg bg-white fixed-top border-bottom">
        <div class="container-fluid px-3 px-lg-4">
            <button class="btn btn-icon d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#mainSidebar" aria-label="Mo menu">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
                <img src="<?= url('assets/img/logobikebuzz.png') ?>" alt="BikeBuzz">
                <span>BikeBuzz</span>
            </a>
            <div class="ms-auto d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="btn btn-icon position-relative" data-bs-toggle="dropdown" aria-label="Thong bao">
                        <i class="bi bi-bell"></i>
                        <?php if ($notifications): ?><span class="notify-dot"></span><?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notify-menu p-2">
                        <div class="small fw-bold px-2 py-1 text-secondary">Thong bao moi</div>
                        <?php if (!$notifications): ?>
                            <div class="dropdown-item small text-muted">Chua co thong bao.</div>
                        <?php endif; ?>
                        <?php foreach ($notifications as $notice): ?>
                            <div class="dropdown-item notify-item">
                                <div class="fw-semibold"><?= e($notice['title']) ?></div>
                                <div class="small text-muted"><?= e($notice['message']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a class="btn btn-icon position-relative" href="<?= url('cart.php') ?>" aria-label="Gio hang">
                    <i class="bi bi-bag"></i>
                    <?php if (cart_count() > 0): ?><span class="cart-badge"><?= cart_count() ?></span><?php endif; ?>
                </a>
                <?php if ($user): ?>
                    <a class="btn btn-icon position-relative d-none d-sm-inline-flex" href="<?= url('wishlist.php') ?>" aria-label="Yeu thich">
                        <i class="bi bi-heart"></i>
                        <?php if (wishlist_count() > 0): ?><span class="cart-badge"><?= wishlist_count() ?></span><?php endif; ?>
                    </a>
                <?php endif; ?>
                <?php if ($user): ?>
                    <div class="dropdown">
                        <button class="btn user-pill dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= e($user['name']) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($user['role'] === 'admin'): ?>
                                <li><a class="dropdown-item" href="<?= url('admin/index.php') ?>">Quan tri</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= url('auth/logout.php') ?>">Dang xuat</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="btn btn-outline-dark btn-sm" href="<?= url('auth/login.php') ?>">Dang nhap</a>
                    <a class="btn btn-brand btn-sm" href="<?= url('auth/register.php') ?>">Tao tai khoan</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="offcanvas-lg offcanvas-start app-sidebar" tabindex="-1" id="mainSidebar">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">BikeBuzz</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#mainSidebar"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-3">
            <?php render_sidebar($active, $user); ?>
        </div>
    </div>

    <main class="app-main">
    <?php if ($flash): ?>
        <script>window.BIKEBUZZ_FLASH = <?= json_encode($flash) ?>;</script>
    <?php endif; ?>
    <?php
}

function render_sidebar(string $active, ?array $user): void
{
    $items = [
        ['home', 'Trang chu', 'index.php', 'bi-house'],
        ['products', 'San pham', 'products.php', 'bi-bicycle'],
    ];
    if ($user) {
        $items[] = ['cart', 'Gio hang', 'cart.php', 'bi-bag-check'];
        $items[] = ['wishlist', 'Yeu thich', 'wishlist.php', 'bi-heart'];
        $items[] = ['account', 'Tai khoan', 'account.php', 'bi-person'];
    }
    ?>
    <div class="sidebar-section">Menu</div>
    <div class="nav flex-column gap-1">
        <?php foreach ($items as $item): ?>
            <a class="nav-link <?= $active === $item[0] ? 'active' : '' ?>" href="<?= url($item[2]) ?>">
                <i class="bi <?= $item[3] ?>"></i><span><?= e($item[1]) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
    <?php if ($user && $user['role'] === 'admin'): ?>
        <div class="sidebar-section mt-4">Admin</div>
        <div class="nav flex-column gap-1">
            <a class="nav-link <?= $active === 'admin' ? 'active' : '' ?>" href="<?= url('admin/index.php') ?>"><i class="bi bi-speedometer2"></i><span>Tong quan</span></a>
            <a class="nav-link <?= $active === 'admin-products' ? 'active' : '' ?>" href="<?= url('admin/products.php') ?>"><i class="bi bi-box-seam"></i><span>San pham</span></a>
            <a class="nav-link <?= $active === 'admin-categories' ? 'active' : '' ?>" href="<?= url('admin/categories.php') ?>"><i class="bi bi-tags"></i><span>Danh muc</span></a>
            <a class="nav-link <?= $active === 'admin-users' ? 'active' : '' ?>" href="<?= url('admin/users.php') ?>"><i class="bi bi-people"></i><span>Nguoi dung</span></a>
            <a class="nav-link <?= $active === 'admin-reviews' ? 'active' : '' ?>" href="<?= url('admin/reviews.php') ?>"><i class="bi bi-star"></i><span>Danh gia</span></a>
            <a class="nav-link <?= $active === 'admin-notifications' ? 'active' : '' ?>" href="<?= url('admin/notifications.php') ?>"><i class="bi bi-megaphone"></i><span>Thong bao</span></a>
        </div>
    <?php endif; ?>
    <?php
}

function render_footer(): void
{
    ?>
    </main>
    <script src="<?= url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= url('assets/js/bikebuzz.js') ?>"></script>
    </body>
    </html>
    <?php
}
