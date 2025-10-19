<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$masp = isset($_POST['masp']) ? trim($_POST['masp']) : '';
$soluong = isset($_POST['soluong']) ? trim($_POST['soluong']) : '1';
// Kiểm tra nếu có trường nào rỗng
if (empty($username) || empty($masp)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'), JSON_UNESCAPED_UNICODE);
    exit;
}
$magh = 'GH' . time() . rand(1000, 9999);
// Kiểm tra username tồn tại
$check_username = "SELECT username FROM taikhoan WHERE username = '$username';";
$result_username = mysqli_query($conn, $check_username);
if (mysqli_num_rows($result_username) === 0) {
    echo json_encode(array('success' => false, 'message' => 'Tài khoản không tồn tại'), JSON_UNESCAPED_UNICODE);
    exit;
}
// Thêm giỏ hàng mới
$query = "INSERT INTO giohang (magh, username, masp, soluong) 
          VALUES ('$magh', '$username', '$masp', '$soluong');";
if (mysqli_query($conn, $query)) {
    echo json_encode(array(
        'success' => true,
        'message' => 'Thêm giỏ hàng thành công'
    ), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array('success' => false, 'message' => 'Thêm giỏ hàng thất bại: ' . mysqli_error($conn)), JSON_UNESCAPED_UNICODE);
}