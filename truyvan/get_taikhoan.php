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

// Escape input để tránh injection
$li = mysqli_real_escape_string($conn, $login_input);

// Cho phép đăng nhập bằng username, email hoặc mobile
$query = "SELECT tk.username, tk.email, tk.matkhau, nd.hinhanhnguoidung FROM taikhoan tk LEFT JOIN nguoidung nd ON tk.username = nd.username WHERE tk.username = '$li' OR tk.email = '$li' OR tk.mobile = '$li' LIMIT 1";
$data = mysqli_query($conn, $query);

if ($data && mysqli_num_rows($data) > 0) {
    $user = mysqli_fetch_assoc($data);

    // Kiểm tra mật khẩu (hiện đang so sánh plain text như cũ)
    if ($user['matkhau'] === $password) {
        // Nếu hinhanhnguoidung NULL thì trả về chuỗi rỗng
        $avatar = '';
        if (isset($user['hinhanhnguoidung']) && $user['hinhanhnguoidung'] !== null) {
            $avatar = $user['hinhanhnguoidung'];
        }

        echo json_encode(array(
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'username' => $user['username'],
            'email' => $user['email'],
            'avatar' => $avatar
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array('success' => false, 'message' => 'Sai tên đăng nhập hoặc mật khẩu'), JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode(array('success' => false, 'message' => 'Sai tên đăng nhập hoặc mật khẩu'), JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>