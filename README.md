# BikeBuzz

BikeBuzz la web ban xe dap viet bang PHP thuan va Bootstrap. Du an co san:

- Login, dang ky, dang xuat.
- Phan quyen `admin` va `user`.
- Admin sidebar day du: dashboard, CRUD san pham, CRUD danh muc, quan ly nguoi dung, tao/xoa thong bao.
- User sidebar: trang chu, san pham, gio hang, tai khoan.
- Khach chua dang nhap chi xem san pham; dang nhap moi them vao gio.
- Gio hang luu theo tai khoan trong database.
- Thong bao gon dep bang SweetAlert2.
- UI responsive voi Bootstrap, sidebar desktop/offcanvas mobile, animation nhe.
- Anh san pham co the upload local vao `uploads/` hoac dan URL Cloudinary/Firebase Storage.

## Chay du an

Can PHP co extension `pdo_sqlite`.

```bash
php -S localhost:8000
```

File chay chinh la `index.php`. Mo `http://localhost:8000/index.php`.

Database SQLite se tu tao tai `data/bikebuzz.sqlite` trong lan chay dau tien.

## Tai khoan mau

- Admin: `admin@bikebuzz.test` / `admin123`
- User: `user@bikebuzz.test` / `user123`

## Goi y tinh nang tiep theo

- Thanh toan online MoMo/VNPAY.
- Don hang va lich su mua hang.
- Wishlist.
- Danh gia, binh luan san pham.
- Loc theo gia, thuong hieu, size khung.
- Tich hop Cloudinary hoac Firebase Storage that bang API key.
- Backup database va dashboard doanh thu.
