<?php
require_once __DIR__ . '/helpers.php';

function render_header(string $title = APP_NAME, string $active = ''): void
{
    $user = current_user();
    $flash = take_flash();
    $notifications = latest_notifications($user['role'] ?? null);
    $unreadNotifications = $user ? unread_notifications($user) : [];
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
                    <button class="btn btn-icon position-relative" data-bs-toggle="dropdown" aria-label="Thông báo">
                        <i class="bi bi-bell"></i>
                        <?php if ($unreadNotifications): ?><span class="notify-dot"></span><?php endif; ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end notify-menu p-2">
                        <div class="d-flex align-items-center justify-content-between px-2 py-1">
                            <div class="small fw-bold text-secondary">Thông báo</div>
                            <?php if ($user && $unreadNotifications): ?>
                                <form method="post" action="<?= url('notification_actions.php') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="back" value="<?= e(current_relative_url()) ?>">
                                    <button class="btn btn-link btn-sm p-0 text-success">Đánh dấu đã đọc</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <?php if (!$notifications): ?>
                            <div class="dropdown-item small text-muted">Chưa có thông báo.</div>
                        <?php endif; ?>
                        <?php foreach ($notifications as $notice): ?>
                            <div class="dropdown-item notify-item">
                                <div class="fw-semibold"><?= e($notice['title']) ?></div>
                                <div class="small text-muted"><?= e($notice['message']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <button class="btn btn-icon position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#cartDrawer" aria-label="Giỏ hàng">
                    <i class="bi bi-bag"></i>
                    <?php if (cart_count() > 0): ?><span class="cart-badge"><?= cart_count() ?></span><?php endif; ?>
                </button>
                <?php if ($user): ?>
                    <a class="btn btn-icon position-relative d-none d-sm-inline-flex" href="<?= url('wishlist.php') ?>" aria-label="Yêu thích">
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
                                <li><a class="dropdown-item" href="<?= url('admin/index.php') ?>">Quản trị</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= url('auth/logout.php') ?>">Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a class="btn btn-outline-dark btn-sm" href="<?= url('auth/login.php') ?>">Đăng nhập</a>
                    <a class="btn btn-brand btn-sm" href="<?= url('auth/register.php') ?>">Tạo tài khoản</a>
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

    <?php render_cart_drawer(); ?>

    <main class="app-main">
    <?php if ($flash): ?>
        <script>window.BIKEBUZZ_FLASH = <?= json_encode($flash) ?>;</script>
    <?php endif; ?>
    <?php
}

function render_sidebar(string $active, ?array $user): void
{
    $items = [
        ['home', 'Trang chủ', 'index.php', 'bi-house'],
        ['products', 'Tất cả xe', 'products.php', 'bi-bicycle'],
        ['mountain', 'Xe địa hình', 'products.php?category=1', 'bi-signpost-split'],
        ['road', 'Xe đạp đua', 'products.php?category=2', 'bi-speedometer'],
        ['city', 'Xe thành phố', 'products.php?category=3', 'bi-buildings'],
        ['brands', 'Thương hiệu', 'brands.php', 'bi-award'],
        ['services', 'Dịch vụ bảo dưỡng', 'services.php', 'bi-tools'],
        ['about', 'Cửa hàng', 'about.php', 'bi-geo-alt'],
    ];
    if ($user) {
        $items[] = ['account', 'Tài khoản', 'account.php', 'bi-person'];
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
            <a class="nav-link <?= $active === 'admin' ? 'active' : '' ?>" href="<?= url('admin/index.php') ?>"><i class="bi bi-speedometer2"></i><span>Tổng quan</span></a>
            <a class="nav-link <?= $active === 'admin-products' ? 'active' : '' ?>" href="<?= url('admin/products.php') ?>"><i class="bi bi-box-seam"></i><span>Sản phẩm</span></a>
            <a class="nav-link <?= $active === 'admin-categories' ? 'active' : '' ?>" href="<?= url('admin/categories.php') ?>"><i class="bi bi-tags"></i><span>Danh mục</span></a>
            <a class="nav-link <?= $active === 'admin-users' ? 'active' : '' ?>" href="<?= url('admin/users.php') ?>"><i class="bi bi-people"></i><span>Người dùng</span></a>
            <a class="nav-link <?= $active === 'admin-reviews' ? 'active' : '' ?>" href="<?= url('admin/reviews.php') ?>"><i class="bi bi-star"></i><span>Đánh giá</span></a>
            <a class="nav-link <?= $active === 'admin-notifications' ? 'active' : '' ?>" href="<?= url('admin/notifications.php') ?>"><i class="bi bi-megaphone"></i><span>Thông báo</span></a>
        </div>
    <?php endif; ?>
    <?php
}

function render_cart_drawer(): void
{
    $items = cart_items();
    $total = array_sum(array_map(fn ($item) => (int) $item['price'] * (int) $item['quantity'], $items));
    ?>
    <div class="offcanvas offcanvas-end cart-drawer" tabindex="-1" id="cartDrawer">
        <div class="offcanvas-header border-bottom">
            <div>
                <h5 class="offcanvas-title fw-bold">Giỏ hàng</h5>
                <div class="small text-muted"><?= cart_count() ?> sản phẩm</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Đóng"></button>
        </div>
        <div class="offcanvas-body">
            <?php if (!current_user()): ?>
                <div class="text-center py-5">
                    <div class="h5 fw-bold">Đăng nhập để lưu giỏ hàng</div>
                    <p class="text-muted">Giỏ hàng của bạn sẽ được đồng bộ theo tài khoản.</p>
                    <a class="btn btn-brand" href="<?= url('auth/login.php') ?>">Đăng nhập</a>
                </div>
            <?php elseif (!$items): ?>
                <div class="text-center py-5 text-muted">Giỏ hàng đang trống.</div>
            <?php else: ?>
                <div class="cart-drawer-list">
                    <?php foreach ($items as $item): ?>
                        <div class="cart-drawer-item">
                            <img src="<?= e(product_image($item['image'])) ?>" alt="<?= e($item['name']) ?>">
                            <div class="flex-grow-1">
                                <div class="fw-bold"><?= e($item['name']) ?></div>
                                <div class="small text-muted"><?= e($item['brand']) ?> · <?= money((int) $item['price']) ?></div>
                                <form class="d-flex gap-2 mt-2" method="post" action="<?= url('cart_actions.php') ?>">
                                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                    <input type="hidden" name="action" value="update">
                                    <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                    <input type="hidden" name="back" value="<?= e(current_relative_url()) ?>">
                                    <input class="form-control form-control-sm cart-qty" type="number" name="quantity" min="1" max="<?= (int) $item['stock'] ?>" value="<?= (int) $item['quantity'] ?>">
                                    <button class="btn btn-outline-dark btn-sm"><i class="bi bi-check2"></i></button>
                                </form>
                            </div>
                            <form method="post" action="<?= url('cart_actions.php') ?>" data-confirm="Xóa sản phẩm khỏi giỏ?">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                                <input type="hidden" name="back" value="<?= e(current_relative_url()) ?>">
                                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <?php if (current_user() && $items): ?>
            <div class="offcanvas-footer border-top p-3">
                <div class="d-flex justify-content-between mb-3">
                    <span class="text-muted">Tạm tính</span>
                    <strong class="text-success"><?= money($total) ?></strong>
                </div>
                <button class="btn btn-brand w-100" disabled>Thanh toán tại cửa hàng</button>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function render_footer(): void
{
    ?>
    </main>
    <footer class="app-footer">
        <div class="footer-inner">
            <div>
                <div class="footer-brand">BikeBuzz</div>
                <p>Showroom xe đạp tại TP. Hồ Chí Minh, chuyên tư vấn xe địa hình, xe đua và xe đi phố phù hợp nhu cầu sử dụng hằng ngày.</p>
            </div>
            <div>
                <div class="footer-title">Cửa hàng</div>
                <a href="<?= url('about.php') ?>">Giới thiệu</a>
                <a href="<?= url('services.php') ?>">Bảo dưỡng</a>
                <a href="<?= url('products.php') ?>">Sản phẩm</a>
            </div>
            <div>
                <div class="footer-title">Liên hệ</div>
                <p>123 Nguyễn Văn Cừ, Quận 5, TP. Hồ Chí Minh</p>
                <p>Hotline: 0909 123 456</p>
                <p>Email: hello@bikebuzz.vn</p>
            </div>
        </div>
    </footer>
    <script src="<?= url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= url('assets/js/bikebuzz.js') ?>"></script>
    </body>
    </html>
    <?php
}
