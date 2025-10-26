<?php
include 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$masp = isset($_POST['masp']) ? trim($_POST['masp']) : '';
$tensanpham = isset($_POST['tensanpham']) ? trim($_POST['tensanpham']) : '';
$giaban = isset($_POST['giaban']) ? trim($_POST['giaban']) : '';
$giagoc = isset($_POST['giagoc']) ? trim($_POST['giagoc']) : '';
$mota = isset($_POST['mota']) ? trim($_POST['mota']) : '';
if (empty($masp) || empty($tensanpham) || !isset($giaban) || !isset($giagoc) ) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc để cập nhật sản phẩm']);
    exit;
}

$query = "UPDATE sanpham 
          SET tensanpham='$tensanpham', giaban='$giaban', giagoc='$giagoc', mota='$mota'
          WHERE masp='$masp'";
if (mysqli_query($conn, $query)) {
    echo json_encode(['success' => true, 'message' => 'Cập nhật sản phẩm thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật sản phẩm']);
}
$conn->close();
?>
