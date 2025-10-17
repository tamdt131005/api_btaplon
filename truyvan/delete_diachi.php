<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$madiachi = isset($_POST['madiachi']) ? trim($_POST['madiachi']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : ''; // để kiểm tra quyền xóa

// Kiểm tra mã địa chỉ có được gửi lên
if (empty($madiachi)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng cung cấp mã địa chỉ'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra username có được gửi lên
if (empty($username)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng cung cấp username'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra địa chỉ tồn tại và thuộc về user
$check_query = "SELECT username FROM diachigiao WHERE madiachi = '$madiachi'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) === 0) {
    echo json_encode(array('success' => false, 'message' => 'Địa chỉ không tồn tại'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Kiểm tra quyền xóa
$row = mysqli_fetch_assoc($check_result);
if ($row['username'] !== $username) {
    echo json_encode(array('success' => false, 'message' => 'Bạn không có quyền xóa địa chỉ này'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Thực hiện xóa địa chỉ
$delete_query = "DELETE FROM diachigiao WHERE madiachi = '$madiachi' AND username = '$username';";
if (mysqli_query($conn, $delete_query)) {
    echo json_encode(array(
        'success' => true,
        'message' => 'Xóa địa chỉ thành công'
    ), JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(array(
        'success' => false, 
        'message' => 'Lỗi khi xóa địa chỉ: ' . mysqli_error($conn)
    ), JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
