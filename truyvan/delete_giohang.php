<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$magh = isset($_POST['magh']) ? trim($_POST['magh']) : '';
// Kiểm tra nếu có trường nào rỗng
if (empty($magh)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng cung cấp mã giỏ hàng'), JSON_UNESCAPED_UNICODE);
    exit;
}   
// Xóa giỏ hàng
$delete_query = "DELETE FROM giohang WHERE magh = '$magh';";
if (mysqli_query($conn, $delete_query)) {
    echo json_encode(array(
        'success' => true,
        'message' => 'Xóa thành công'
    ), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array(
        'success' => false, 
        'message' => 'Lỗi khi xóa giỏ hàng: ' . mysqli_error($conn)
    ), JSON_UNESCAPED_UNICODE);
}
$conn->close();
?>