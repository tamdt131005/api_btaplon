<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
date_default_timezone_set('Asia/Ho_Chi_Minh');
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$token = isset($_POST['token']) ? trim($_POST['token']) : '';
if (empty($email) || empty($token)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp email và token!']);
    exit;
}
// Kiểm tra token hợp lệ
$escaped_email = mysqli_real_escape_string($conn, $email);
$escaped_token = mysqli_real_escape_string($conn, $token);
$current_time = date('Y-m-d H:i:s');

$query = "SELECT * FROM resetmk WHERE email = '$escaped_email' AND token = '$escaped_token' AND timetoken >= '$current_time'";
$result = mysqli_query($conn, $query);
if (mysqli_num_rows($result) > 0) {
    echo json_encode(['success' => true, 'message' => 'Token hợp lệ.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Token không hợp lệ hoặc đã hết hạn.']);
}
$conn->close();
?>
