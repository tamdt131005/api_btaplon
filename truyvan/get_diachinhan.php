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

// Lấy danh sách địa chỉ
$query = "SELECT madiachi, tennguoinhan, sdtnhan, tinhthanhpho, tennhasonha, macdinh FROM diachigiao WHERE username = '$username' ORDER BY macdinh DESC;";
$data = mysqli_query($conn, $query);
$result = array();

while ($row = mysqli_fetch_assoc($data)) {
    $result[] = $row;
}

// Luôn trả về cấu trúc JSON đồng nhất
if (!empty($result)) {
    $arr = array(
        'success' => true,
        'message' => 'Lấy thông tin địa chỉ thành công',
        'result' => $result  // Đổi từ 'result' thành 'item'
    );
} else {
    $arr = array(
        'success' => true,  // Đổi thành true
        'message' => 'Chưa có địa chỉ nào',
        'result' => array()  
    );
}

echo json_encode($arr, JSON_UNESCAPED_UNICODE);

$conn->close();
?>