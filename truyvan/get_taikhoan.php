<?php
header('Content-Type: application/json');
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(array('success' => false, 'message' => 'Invalid request method'));
    exit;
}

$username = isset($_POST['nameuser']) ? trim($_POST['nameuser']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if (empty($username) || empty($password)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'));
    exit;
}

// Sử dụng Prepared Statement để tránh SQL Injection
$stmt = $conn->prepare("SELECT id_user, nameuser, password FROM taikhoan WHERE nameuser = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Kiểm tra mật khẩu (nếu dùng hash thì dùng password_verify)
    if ($user['password'] === $password) {  
        echo json_encode(array(
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'token' => $token,
            'userId' => $user['id_user'],
            'username' => $user['nameuser']
        ));
    } else {
        echo json_encode(array('success' => false, 'message' => 'Sai tên đăng nhập hoặc mật khẩu'));
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Sai tên đăng nhập hoặc mật khẩu'));
}

$stmt->close();
$conn->close();
?>