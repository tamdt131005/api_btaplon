<?php
include 'connect.php';
header('Content-Type: application/json; charset=utf-8');
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc để tạo người bán']);
    exit;
}

$mncc = "NCC".time().rand(10000,99999);
$sql = "INSERT INTO nhacungcap (username,mancc) values ('$username','$mncc')";
if (mysqli_query($conn, $sql)) {
    $arr = [
        'success' => true,
        'message' => 'Tạo người bán thành công',
        'mancc' => $mncc
    ];
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
} else {
    $arr = [
        'success' => false,
        'message' => 'Lỗi khi tạo người bán',
        'mancc' => null
    ];
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
}
$conn->close();
?>