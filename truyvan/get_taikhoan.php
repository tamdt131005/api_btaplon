<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$login_input = isset($_POST['username']) ? trim($_POST['username']) : ''; // Có thể là username, email hoặc mobile
$password = isset($_POST['matkhau']) ? trim($_POST['matkhau']) : '';

// Kiểm tra nếu login_input hoặc password rỗng
if (empty($login_input) || empty($password)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Cho phép đăng nhập bằng username, email hoặc mobile
$query = "SELECT username, matkhau FROM taikhoan WHERE username = '$login_input' OR email = '$login_input' OR mobile = '$login_input';";
$data = mysqli_query($conn, $query);
$result = array();
while ($row = mysqli_fetch_assoc($data)) {
    $result[] = $row;
}

// Nếu có 1 user thì kiểm tra mật khẩu, và lấy user đầu tiên
if (count($result) > 0) {
    $user = $result[0];
    
    // Kiểm tra mật khẩu
    if ($user['matkhau'] === $password) {  
        echo json_encode(array(
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'username' => $user['username']
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array('success' => false, 'message' => 'Sai tên đăng nhập hoặc mật khẩu'), JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Sai tên đăng nhập hoặc mật khẩu'), JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>