<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

// Thư mục lưu ảnh (thư mục `anhuser` nằm cùng cấp với thư mục `truyvan`)
$uploadDir = __DIR__ . '/../anhuser/';
$uploadUrlPath = 'anhuser/'; // đường dẫn tương đối lưu vào database (vd: anhuser/filename.jpg)

// Giới hạn
$maxFileSize = 5 * 1024 * 1024; // 5 MB
$allowedMime = array('image/jpeg', 'image/png', 'image/gif');

$username = isset($_POST['username']) ? trim($_POST['username']) : '';

// Yêu cầu bắt buộc: username và file upload (field name: file)
if (empty($username)) {
	echo json_encode(array('success' => false, 'message' => 'Thiếu username'), JSON_UNESCAPED_UNICODE);
	exit;
}

if (!isset($_FILES['file'])) {
	echo json_encode(array('success' => false, 'message' => 'Không có file được gửi'), JSON_UNESCAPED_UNICODE);
	exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
	echo json_encode(array('success' => false, 'message' => 'Lỗi khi upload file: ' . $file['error']), JSON_UNESCAPED_UNICODE);
	exit;
}

if ($file['size'] > $maxFileSize) {
	echo json_encode(array('success' => false, 'message' => 'Kích thước file vượt quá giới hạn 5MB'), JSON_UNESCAPED_UNICODE);
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
	// fallback từ mime
	$ext = image_type_to_extension($imgInfo[2], false);
}
$filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$destination = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
	echo json_encode(array('success' => false, 'message' => 'Lưu file thất bại'), JSON_UNESCAPED_UNICODE);
	exit;
}

$dbPath = $uploadUrlPath . $filename; // đường dẫn lưu vào database
// Sử dụng SQL trực tiếp (escape giá trị) theo yêu cầu
$oldPath = null;
$usernameEsc = mysqli_real_escape_string($conn, $username);
$dbPathEsc = mysqli_real_escape_string($conn, $dbPath);

// Lấy đường dẫn ảnh cũ (nếu có)
$sqlOld = "SELECT hinhanhnguoidung FROM nguoidung WHERE username = '$usernameEsc' LIMIT 1";
$resOld = mysqli_query($conn, $sqlOld);
if ($resOld && mysqli_num_rows($resOld) > 0) {
	$rowOld = mysqli_fetch_assoc($resOld);
	$oldPath = $rowOld['hinhanhnguoidung'];
}

// Cập nhật đường dẫn ảnh vào bảng `nguoidung`
$sqlUpdate = "UPDATE nguoidung SET hinhanhnguoidung = '$dbPathEsc' WHERE username = '$usernameEsc'";
if (mysqli_query($conn, $sqlUpdate)) {
	$affected = mysqli_affected_rows($conn);
	if ($affected > 0) {
		// Nếu cập nhật thành công, xóa file cũ (nếu có và khác file mới)
		if (!empty($oldPath) && $oldPath !== $dbPath) {
			$oldFile = __DIR__ . '/../' . ltrim($oldPath, '/\\');
			if (file_exists($oldFile) && is_file($oldFile)) {
				@unlink($oldFile);
			}
		}
		echo json_encode(array('success' => true, 'message' => 'Upload và cập nhật ảnh thành công', 'username' => $username, 'path' => $dbPath), JSON_UNESCAPED_UNICODE);
		$conn->close();
		exit;
	}
	// nếu không có hàng bị ảnh hưởng, có thể không tồn tại bản ghi trong nguoidung
} else {
	// Nếu câu lệnh UPDATE lỗi, xóa file mới để tránh rác
	if (file_exists($destination)) {@unlink($destination);} 
	echo json_encode(array('success' => false, 'message' => 'Lỗi khi cập nhật bảng nguoidung: ' . mysqli_error($conn)), JSON_UNESCAPED_UNICODE);
	$conn->close();
	exit;
}
if (file_exists($destination)) {@unlink($destination);} 
echo json_encode(array('success' => false, 'message' => 'Không tìm thấy username trong bảng nguoidung'), JSON_UNESCAPED_UNICODE);
$conn->close();
exit;

?>
