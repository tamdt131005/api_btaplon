<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';

if (empty($username)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập tên đăng nhập'), JSON_UNESCAPED_UNICODE);
    exit;
}
$query = "SELECT 
    tk.email,
    nd.tennguoidung,
    nd.ngaysinh,
    nd.gioitinh
    ,nd.hinhanhnguoidung
FROM taikhoan tk
JOIN nguoidung nd ON tk.username = nd.username
WHERE tk.username = '$username' or tk.email='$username' ;";
$data = mysqli_query($conn, $query);
$result = array();
while ($row = mysqli_fetch_assoc($data)) {
    $result[] = $row;
}
if (!empty($result)) {
    $arr = [
        'success' => true,
        'message' => 'Lấy thông tin người dùng thành công',
        'result' => $result[0]
   ]  ;
        
} else {
    echo json_encode(array('success' => false, 'message' => 'Không tìm thấy người dùng'), JSON_UNESCAPED_UNICODE);
}
print_r(json_encode($arr, JSON_UNESCAPED_UNICODE));
$conn->close();