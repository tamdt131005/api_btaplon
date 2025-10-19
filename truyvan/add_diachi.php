<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$tennguoinhan = isset($_POST['tennguoinhan']) ? trim($_POST['tennguoinhan']) : '';
$sdtnhan = isset($_POST['sdtnhan']) ? trim($_POST['sdtnhan']) : '';
$tinhthanhpho = isset($_POST['tinhthanhpho']) ? trim($_POST['tinhthanhpho']) : '';
$tennhasonha = isset($_POST['tennhasonha']) ? trim($_POST['tennhasonha']) : '';
$macdinh = isset($_POST['macdinh']) ? trim($_POST['macdinh']) : '0';
// Kiểm tra nếu có trường nào rỗng
if (empty($username) || empty($tennguoinhan) || empty($sdtnhan) || empty($tinhthanhpho) || empty($tennhasonha)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin'), JSON_UNESCAPED_UNICODE);
    exit;
}
// tạo mã địa chỉ tự động với time() là số giây từ 1970 đến nay và rand(1000, 9999)số ngẫu nhiên từ 1000 đến 9999
$madiachi = 'DC' . time() . rand(1000, 9999);
// Kiểm tra username tồn tại
$check_username = "SELECT username FROM taikhoan WHERE username = '$username';";
$result_username = mysqli_query($conn, $check_username);
if (mysqli_num_rows($result_username) === 0) {
    echo json_encode(array('success' => false, 'message' => 'Tài khoản không tồn tại'), JSON_UNESCAPED_UNICODE);
    exit;
}

if ($macdinh=='1') {
    $querymacdinh = "UPDATE diachigiao SET macdinh = '0' WHERE username = '$username';";
    mysqli_query($conn, $querymacdinh);
}
// Thêm địa chỉ mới
$query = "INSERT INTO diachigiao (madiachi, username, tennguoinhan, sdtnhan, tinhthanhpho, tennhasonha, macdinh) 
          VALUES ('$madiachi', '$username', '$tennguoinhan', '$sdtnhan', '$tinhthanhpho', '$tennhasonha','$macdinh');";
if (mysqli_query($conn, $query)) {
    echo json_encode(array(
        'success' => true,
        'message' => 'Thêm địa chỉ thành công'
    ), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array('success' => false, 'message' => 'Thêm địa chỉ thất bại: ' . mysqli_error($conn)), JSON_UNESCAPED_UNICODE);
}
$conn->close();
?>
