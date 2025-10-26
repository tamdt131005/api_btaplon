<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';



if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Không phải phương thức POST']);
    exit;
}

if (empty($_POST)) {
    echo json_encode(['success' => false, 'message' => 'Không nhận được dữ liệu POST']);
    exit;
}
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
    exit;
}
$query = "
SELECT COUNT(*) 
FROM nhacungcap 
WHERE username COLLATE utf8mb4_unicode_ci = '$username' 
   OR username COLLATE utf8mb4_unicode_ci IN (
       SELECT username COLLATE utf8mb4_unicode_ci FROM taikhoan WHERE email COLLATE utf8mb4_unicode_ci = '$username'
   )
";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_row($result);
if ($row[0] > 0) {
    $query2 = "SELECT mancc FROM nhacungcap WHERE username = '$username'";
    $result2 = mysqli_query($conn, $query2);
    $row2 = mysqli_fetch_row($result2);
    $arr = [
        'success' => false,
        'message' => 'Đã là người bán',
        'mancc' => $row2[0]
    ];
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}
else {
    $arr = [
        'success' => true,
        'message' => 'Chưa là người bán',
        'mancc' => null
    ];
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
}
$conn->close();
?>
