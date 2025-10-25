<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : ''; // Lấy dữ liệu từ POST và loại bỏ khoảng trắng thừa , nếu không có thì gắn giá thị bằng rỗng
$password = isset($_POST['matkhau']) ? trim($_POST['matkhau']) : '';

if (empty($username) || empty($password)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'), JSON_UNESCAPED_UNICODE);
    exit;
}
$query = "SELECT username, matkhau FROM taikhoan WHERE username = '$username' or email='$username' ;";
$data = mysqli_query($conn, $query);
$result = array();
while ($row = mysqli_fetch_assoc($data)) {
    $result[] = $row;
}


// nếu có 1 user thì kiểm tra mật khẩu , và lấy user đầu tiên
if (count($result) > 0) {
    $user = $result[0];
    
    // Kiểm tra mật khẩu (nếu dùng hash thì dùng password_verify)
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