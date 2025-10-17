<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$madiachi = isset($_POST['madiachi']) ? trim($_POST['madiachi']) : '';
$tennguoihinhan = isset($_POST['tennguoinhan']) ? trim($_POST['tennguoinhan']) : '';
$sdtnhan = isset($_POST['sdtnhan']) ? trim($_POST['sdtnhan']) : '';
$tinhthanhpho = isset($_POST['tinhthanhpho']) ? trim($_POST['tinhthanhpho']) : '';
$tennhasonha = isset($_POST['tennhasonha']) ? trim($_POST['tennhasonha']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : ''; // để kiểm tra quyền
$macdinh = isset($_POST['macdinh']) ? trim($_POST['macdinh']) : '0';
// Kiểm tra mã địa chỉ có được gửi lên
if (empty($madiachi)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng cung cấp mã địa chỉ'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra có ít nhất một trường được cập nhật
if (empty($tennguoihinhan) && empty($sdtnhan) && empty($tinhthanhpho) && empty($tennhasonha)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng cung cấp ít nhất một thông tin cần cập nhật'), JSON_UNESCAPED_UNICODE);
    exit;
}
// Kiểm tra địa chỉ tồn tại và thuộc về user
$check_query = "SELECT username FROM diachigiao WHERE madiachi = '$madiachi';";
$check_result = mysqli_query($conn, $check_query);
if (mysqli_num_rows($check_result) === 0) {
    echo json_encode(array('success' => false, 'message' => 'Địa chỉ không tồn tại'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra quyền cập nhật (nếu có username gửi lên)
if (!empty($username)) {
    $row = mysqli_fetch_assoc($check_result);
    if ($row['username'] !== $username) {
        echo json_encode(array('success' => false, 'message' => 'Bạn không có quyền cập nhật địa chỉ này'), JSON_UNESCAPED_UNICODE);
        exit;
    }
}
// Nếu đặt làm mặc định, đặt tất cả địa chỉ khác của user thành không mặc định
if ($macdinh=='1') {
    $querymacdinh = "UPDATE diachigiao SET macdinh = '0' WHERE username = '$username';";
    mysqli_query($conn, $querymacdinh);
}
// Cập nhật tất cả thông tin địa chỉ
$update_query = "UPDATE diachigiao SET 
    tennguoinhan = '$tennguoihinhan',
    sdtnhan = '$sdtnhan',  
    tinhthanhpho = '$tinhthanhpho',
    tennhasonha = '$tennhasonha',
    macdinh = '$macdinh'
WHERE madiachi = '$madiachi';";
if (mysqli_query($conn, $update_query)) {
    echo json_encode(array(
        'success' => true,
        'message' => 'Cập nhật địa chỉ thành công',
        'madiachi' => $madiachi
    ), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array('success' => false, 'message' => 'Lỗi khi cập nhật địa chỉ: ' . mysqli_error($conn)), JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
