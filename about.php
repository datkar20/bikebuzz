<?php
require_once __DIR__ . '/app/layout.php';

render_header('Cửa hàng', 'about');
?>
<section class="bb-card p-4 p-lg-5 mb-4">
    <div class="row g-4 align-items-center">
        <div class="col-lg-6">
            <span class="badge badge-soft mb-3">BikeBuzz Store</span>
            <h1 class="page-title mb-3">Cửa hàng xe đạp dành cho người đi xe mỗi ngày.</h1>
            <p class="lead text-muted">BikeBuzz chọn lọc xe theo nhu cầu thực tế: đi làm, luyện tốc độ, leo dốc cuối tuần hoặc chọn chiếc xe đầu tiên cho trẻ em.</p>
            <div class="row g-3 mt-2">
                <div class="col-sm-4"><div class="stat-tile"><div class="h3 fw-bold">8+</div><div class="small text-muted">Mẫu xe có sẵn</div></div></div>
                <div class="col-sm-4"><div class="stat-tile"><div class="h3 fw-bold">24h</div><div class="small text-muted">Giữ xe khi đặt lịch</div></div></div>
                <div class="col-sm-4"><div class="stat-tile"><div class="h3 fw-bold">1:1</div><div class="small text-muted">Tư vấn size khung</div></div></div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="store-panel">
                <h2 class="h5 fw-bold">Địa chỉ showroom</h2>
                <p class="mb-1">123 Nguyễn Văn Cừ, Phường 2, Quận 5, TP. Hồ Chí Minh</p>
                <p class="text-muted">Mở cửa 08:30 - 21:00 từ Thứ 2 đến Chủ nhật</p>
                <div class="ratio ratio-16x9 rounded overflow-hidden">
                    <iframe src="https://www.google.com/maps?q=Nguyen%20Van%20Cu%20District%205%20Ho%20Chi%20Minh%20City&output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="row g-3">
    <div class="col-md-4"><div class="bb-card p-4 h-100"><i class="bi bi-rulers fs-3 text-success"></i><h2 class="h5 fw-bold mt-3">Đo size khung</h2><p class="text-muted mb-0">Tư vấn chiều cao, sải chân và tư thế lái để chọn xe thoải mái hơn.</p></div></div>
    <div class="col-md-4"><div class="bb-card p-4 h-100"><i class="bi bi-shield-check fs-3 text-success"></i><h2 class="h5 fw-bold mt-3">Kiểm tra trước giao</h2><p class="text-muted mb-0">Căn chỉnh phanh, đề, lốp và siết lực các điểm quan trọng trước khi bàn giao.</p></div></div>
    <div class="col-md-4"><div class="bb-card p-4 h-100"><i class="bi bi-arrow-repeat fs-3 text-success"></i><h2 class="h5 fw-bold mt-3">Hỗ trợ sau mua</h2><p class="text-muted mb-0">Bảo dưỡng định kỳ, thay phụ kiện và tư vấn nâng cấp khi bạn cần.</p></div></div>
</div>
<?php render_footer(); ?>
