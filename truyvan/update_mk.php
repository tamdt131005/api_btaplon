<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';

if (empty($email) || empty($new_password)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp email và mật khẩu mới!']);
    exit;
}
$query = "UPDATE taikhoan SET matkhau = '$new_password' WHERE email = '$email'";
if (mysqli_query($conn, $query)) {
    // Xoá token sau khi đổi mật khẩu thành công
    $delete_query = "DELETE FROM resetmk WHERE email = '$email'";
    mysqli_query($conn, $delete_query);

    echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Lỗi khi đổi mật khẩu: ' . mysqli_error($conn)]);
}
$conn->close();
?>