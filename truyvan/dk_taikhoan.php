<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$mobile = isset($_POST['mobile']) ? trim($_POST['mobile']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['matkhau']) ? trim($_POST['matkhau']) : '';

// Kiểm tra nếu có trường nào rỗng
if (empty($username) || empty($mobile) || empty($email) || empty($password)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra username đã tồn tại chưa
$check_username = "SELECT username FROM taikhoan WHERE username = '$username';";
$result_username = mysqli_query($conn, $check_username);
if (mysqli_num_rows($result_username) > 0) {
    echo json_encode(array('success' => false, 'message' => 'Tên đăng nhập đã tồn tại'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra mobile đã tồn tại chưa
$check_mobile = "SELECT mobile FROM taikhoan WHERE mobile = '$mobile';";
$result_mobile = mysqli_query($conn, $check_mobile);
if (mysqli_num_rows($result_mobile) > 0) {
    echo json_encode(array('success' => false, 'message' => 'Số điện thoại đã được đăng ký'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra email đã tồn tại chưa
$check_email = "SELECT email FROM taikhoan WHERE email = '$email';";
$result_email = mysqli_query($conn, $check_email);
if (mysqli_num_rows($result_email) > 0) {
    echo json_encode(array('success' => false, 'message' => 'Email đã được đăng ký'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Thêm tài khoản mới
$query = "INSERT INTO taikhoan (username, mobile, email, matkhau) VALUES ('$username', '$mobile', '$email', '$password');";
if (mysqli_query($conn, $query)) {
    echo json_encode(array(
        'success' => true,
        'message' => 'Đăng ký tài khoản thành công',
        'username' => $username
    ), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array('success' => false, 'message' => 'Đăng ký thất bại: ' . mysqli_error($conn)), JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>