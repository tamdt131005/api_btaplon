<?php
include 'connect.php';
header('Content-Type: application/json; charset=utf-8');

$masp = isset($_POST['masp']) ? trim($_POST['masp']) : '';
if (empty($masp)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu mã sản phẩm để xóa']);
    exit;
}
$query = "DELETE FROM sanpham WHERE masp='$masp'";
if (mysqli_query($conn, $query)) {
    $qr= "DELETE FROM spban WHERE masp='$masp'";
    mysqli_query($conn, $qr);
    echo json_encode(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi xóa sản phẩm']);
}
$conn->close();
?>