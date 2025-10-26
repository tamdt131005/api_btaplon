<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$tennguoidung = isset($_POST['tennguoidung']) ? trim($_POST['tennguoidung']) : '';
$ngaysinh = isset($_POST['ngaysinh']) ? trim($_POST['ngaysinh']) : '';
$gioitinh = isset($_POST['gioitinh']) ? trim($_POST['gioitinh']) : '';

// Thư mục lưu ảnh
$uploadDir = __DIR__ . '/../anhuser/';
$uploadUrlPath = 'anhuser/';

$maxFileSize = 10 * 1024 * 1024; // 10 MB
$allowedMime = array('image/jpeg', 'image/png', 'image/gif');

// Sửa lỗi 1: Đổi && thành || (chỉ cần 1 trường trống là báo lỗi)
if (empty($username) || empty($tennguoidung) || empty($ngaysinh) || empty($gioitinh)) {
    echo json_encode(array('success' => false, 'message' => 'Thiếu thông tin'), JSON_UNESCAPED_UNICODE);
    exit;
}

// Lấy thông tin ảnh cũ
$usernameEsc = mysqli_real_escape_string($conn, $username);
$checkQuery = "SELECT hinhanhnguoidung FROM nguoidung WHERE username = '$usernameEsc'";
$checkResult = mysqli_query($conn, $checkQuery);
$oldImagePath = null;

if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    $oldData = mysqli_fetch_assoc($checkResult);
    $oldImagePath = $oldData['hinhanhnguoidung'];
}

$hinhanhnguoidung = $oldImagePath; // Giữ ảnh cũ nếu không upload ảnh mới

// Xử lý upload ảnh mới nếu có
if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['file'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(array('success' => false, 'message' => 'Lỗi khi upload file: ' . $file['error']), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    if ($file['size'] > $maxFileSize) {
        echo json_encode(array('success' => false, 'message' => 'Kích thước file vượt quá giới hạn 10MB'), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Kiểm tra thật sự là ảnh
    $imgInfo = @getimagesize($file['tmp_name']);
    if ($imgInfo === false || !in_array($imgInfo['mime'], $allowedMime)) {
        echo json_encode(array('success' => false, 'message' => 'Chỉ cho phép upload ảnh (jpg, png, gif)'), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Tạo thư mục nếu chưa có
    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            echo json_encode(array('success' => false, 'message' => 'Không thể tạo thư mục lưu ảnh'), JSON_UNESCAPED_UNICODE);
            exit;
        }
    }
    
    // Sinh tên file mới an toàn
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $ext = strtolower($ext);
    if ($ext === '') {
        $ext = image_type_to_extension($imgInfo[2], false);
    }
    $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $destination = $uploadDir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        echo json_encode(array('success' => false, 'message' => 'Lưu file thất bại'), JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $hinhanhnguoidung = $uploadUrlPath . $filename; // Cập nhật đường dẫn ảnh mới
}

// Escape dữ liệu
$hinhanhnguoidungEsc = mysqli_real_escape_string($conn, $hinhanhnguoidung);
$tennguoidungEsc = mysqli_real_escape_string($conn, $tennguoidung);
$ngaysinhEsc = mysqli_real_escape_string($conn, $ngaysinh);
$gioitinhEsc = mysqli_real_escape_string($conn, $gioitinh);
$query = "
UPDATE nguoidung nd
JOIN taikhoan tk ON nd.username = tk.username
SET 
    nd.tennguoidung = '$tennguoidungEsc',
    nd.ngaysinh = '$ngaysinhEsc',
    nd.gioitinh = '$gioitinhEsc',
    nd.hinhanhnguoidung = '$hinhanhnguoidungEsc'
WHERE 
    nd.username = '$usernameEsc' 
    OR tk.email = '$usernameEsc'
";

$data = mysqli_query($conn, $query);

if ($data && mysqli_affected_rows($conn) >= 0) {
    // Xóa ảnh cũ nếu có ảnh mới và ảnh cũ tồn tại
    if ($hinhanhnguoidung !== $oldImagePath && !empty($oldImagePath)) {
        $oldFilePath = __DIR__ . '/../' . $oldImagePath;
        if (file_exists($oldFilePath)) {
            @unlink($oldFilePath);
        }
    }
    
    $arr = [
        'success' => true,
        'message' => 'Cập nhật thông tin người dùng thành công',
        'path' => $hinhanhnguoidung
    ];
} else {
    // Xóa ảnh mới nếu upload thành công nhưng update database thất bại
    if ($hinhanhnguoidung !== $oldImagePath && !empty($hinhanhnguoidung)) {
        $newFilePath = __DIR__ . '/../' . $hinhanhnguoidung;
        if (file_exists($newFilePath)) {
            @unlink($newFilePath);
        }
    }
    
    echo json_encode(array('success' => false, 'message' => 'Cập nhật thông tin người dùng thất bại: ' . mysqli_error($conn)), JSON_UNESCAPED_UNICODE);
    $conn->close();
    exit;
}

print_r(json_encode($arr, JSON_UNESCAPED_UNICODE));
$conn->close();