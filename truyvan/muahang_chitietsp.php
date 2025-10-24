<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$madiachi = isset($_POST['madiachi']) ? trim($_POST['madiachi']) : '';
$phuongthucthanhtoan = isset($_POST['phuongthucthanhtoan']) ? trim($_POST['phuongthucthanhtoan']) : '';
$tongtien = isset($_POST['tongtien']) ? trim($_POST['tongtien']) : '';

// Nhận mảng sản phẩm
$masp_array = isset($_POST['masp']) ? $_POST['masp'] : [];
$dongia_array = isset($_POST['dongia']) ? $_POST['dongia'] : [];
$soluong_array = isset($_POST['soluong']) ? $_POST['soluong'] : [];
$ghichu_array = isset($_POST['ghichu']) ? $_POST['ghichu'] : [];
// Kiểm tra thông tin bắt buộc
if (empty($username) || empty($madiachi) || empty($phuongthucthanhtoan) || empty($tongtien)) {
    echo json_encode(array('success' => false, 'message' => 'Thiếu thông tin bắt buộc'), JSON_UNESCAPED_UNICODE);
    exit;
}
// Kiểm tra mảng sản phẩm
if (empty($masp_array) || empty($dongia_array) || empty($soluong_array) ) {
    echo json_encode(array('success' => false, 'message' => 'Thiếu thông tin sản phẩm'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra độ dài mảng phải bằng nhau
if (count($masp_array) != count($dongia_array) || count($masp_array) != count($soluong_array)) {
    echo json_encode(array('success' => false, 'message' => 'Dữ liệu sản phẩm không khớp'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Tạo mã đơn hàng
$madh = 'DH' . time() . rand(10000, 99999);

// Bắt đầu transaction
mysqli_begin_transaction($conn);

try {
    // Thêm đơn hàng
    $query_donhang = "INSERT INTO donhang (madh, username, madiachi, ngaydat, phuongthucthanhtoan, tongtien) 
                      VALUES ('$madh', '$username', '$madiachi', current_timestamp(), '$phuongthucthanhtoan', '$tongtien')";
   mysqli_query($conn, $query_donhang);
    // Thêm chi tiết đơn hàng (loop qua mảng)
    for ($i = 0; $i < count($masp_array); $i++) {
        $mactdh = 'CTDH' . time() . rand(10000, 99999) . $i;
        $masp = mysqli_real_escape_string($conn, trim($masp_array[$i]));
        $dongia = mysqli_real_escape_string($conn, trim($dongia_array[$i]));
        $soluong = mysqli_real_escape_string($conn, trim($soluong_array[$i]));
        $ghichu = mysqli_real_escape_string($conn, trim($ghichu_array[$i]));
        $query_chitietdh = "INSERT INTO chitietdonhang (mactdh, madh, masp, dongia, soluong) 
                           VALUES ('$mactdh', '$madh', '$masp', '$dongia', '$soluong')";
        mysqli_query($conn, $query_chitietdh);
        $matt = 'TT' . time() . rand(10000, 99999);
        $query_trangthaidh = "INSERT INTO trangthaidonhang (matt, mactdh, ngaycapnhat, trangthai, ghichu) 
                            VALUES ('$matt', '$mactdh', current_timestamp(), 'Chờ xác nhận', '$ghichu')";
        mysqli_query($conn, $query_trangthaidh);
    }
    mysqli_commit($conn);
    echo json_encode(array('success' => true, 'message' => 'Thêm đơn hàng thành công'), JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(array('success' => false, 'message' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>