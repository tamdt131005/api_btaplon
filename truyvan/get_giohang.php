<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
// Kiểm tra username có được gửi lên
if (empty($username)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng cung cấp username'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra username tồn tại
$check_username = "SELECT username FROM taikhoan WHERE username = '$username';";
$result_username = mysqli_query($conn, $check_username);
if (mysqli_num_rows($result_username) === 0) {
    echo json_encode(array('success' => false, 'message' => 'Tài khoản không tồn tại'), JSON_UNESCAPED_UNICODE);
    exit;
}

$query = "SELECT magh,username, masp, soluong FROM giohang WHERE username = '$username' order by magh desc;";
$data = mysqli_query($conn, $query);
$result = array();
while ($row = mysqli_fetch_assoc($data)) {
    $result[] = $row;
}
if (!empty($result)) {
    $arr = [
        'success' => true,
        'message' => 'Lấy thông tin giỏ hàng thành công',
        'result' => $result
   ]  ;
        
} else {
    echo json_encode(array('success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'), JSON_UNESCAPED_UNICODE);
}
print_r(json_encode($arr, JSON_UNESCAPED_UNICODE)); 
$conn->close();
?>