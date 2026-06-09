<?php
require_once __DIR__ . '/app/layout.php';

render_header('Dịch vụ', 'services');
?>
<div class="d-flex flex-wrap align-items-end justify-content-between gap-2 mb-3">
    <div>
        <h1 class="page-title h3 mb-1">Dịch vụ bảo dưỡng</h1>
        <div class="text-muted">Đặt lịch kiểm tra xe tại showroom BikeBuzz.</div>
    </div>
    <a class="btn btn-brand" href="<?= url('about.php') ?>"><i class="bi bi-calendar-check me-1"></i>Liên hệ đặt lịch</a>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="bb-card service-card p-4 h-100">
            <i class="bi bi-wrench-adjustable-circle fs-2 text-success"></i>
            <h2 class="h5 fw-bold mt-3">Gói kiểm tra nhanh</h2>
            <p class="text-muted">Căn phanh, kiểm tra áp suất lốp, siết ốc và vệ sinh truyền động cơ bản.</p>
            <div class="fw-bold text-success">Từ 150.000 VND</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bb-card service-card p-4 h-100">
            <i class="bi bi-gear-wide-connected fs-2 text-success"></i>
            <h2 class="h5 fw-bold mt-3">Căn chỉnh truyền động</h2>
            <p class="text-muted">Căn đề trước/sau, kiểm tra xích, líp, đĩa và thay dây nếu cần.</p>
            <div class="fw-bold text-success">Từ 250.000 VND</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="bb-card service-card p-4 h-100">
            <i class="bi bi-stars fs-2 text-success"></i>
            <h2 class="h5 fw-bold mt-3">Bảo dưỡng tổng thể</h2>
            <p class="text-muted">Vệ sinh sâu, căn chỉnh toàn xe và tư vấn phụ kiện nâng cấp phù hợp.</p>
            <div class="fw-bold text-success">Từ 650.000 VND</div>
        </div>
    </div>
</div>
<?php render_footer(); ?>
