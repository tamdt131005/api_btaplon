<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

// Kiểm tra phương thức
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Không phải phương thức POST']);
    exit;
}

// Kiểm tra dữ liệu POST
if (empty($_POST)) {
    echo json_encode(['success' => false, 'message' => 'Không nhận được dữ liệu POST']);
    exit;
}

// Validate username
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
if (empty($username)) {
    echo json_encode(['success' => false, 'message' => 'Thiếu thông tin bắt buộc']);
    exit;
}

// Escape input để tránh SQL injection
$username_escaped = mysqli_real_escape_string($conn, $username);

// Query tối ưu với JOIN thay vì subquery
$query = "
SELECT ncc.mancc
FROM nhacungcap ncc
WHERE ncc.username COLLATE utf8mb4_unicode_ci = ?
UNION
SELECT ncc.mancc
FROM nhacungcap ncc
INNER JOIN taikhoan tk ON ncc.username COLLATE utf8mb4_unicode_ci = tk.username COLLATE utf8mb4_unicode_ci
WHERE tk.email COLLATE utf8mb4_unicode_ci = ?
LIMIT 1
";

// Sử dụng prepared statement
$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Lỗi chuẩn bị câu truy vấn']);
    exit;
}

mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Kiểm tra kết quả
if ($row = mysqli_fetch_assoc($result)) {
    // Đã là người bán
    $arr = [
        'success' => false,
        'message' => 'Đã là người bán',
        'mancc' => $row['mancc']
    ];
} else {
    // Chưa là người bán
    $arr = [
        'success' => true,
        'message' => 'Chưa là người bán',
        'mancc' => null
    ];
}

echo json_encode($arr, JSON_UNESCAPED_UNICODE);

// Đóng statement và connection
mysqli_stmt_close($stmt);
mysqli_close($conn);
?>