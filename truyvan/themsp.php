<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$mancc = isset($_POST['mancc']) ? trim($_POST['mancc']) : '';
$masp = "SP".time().rand(10000,99999);
$tensanpham = isset($_POST['tensanpham']) ? trim($_POST['tensanpham']) : '';
$mota = isset($_POST['mota']) ? trim($_POST['mota']) : '';
$giagoc = isset($_POST['giagoc']) ? (int)$_POST['giagoc'] : 0;
$giaban = isset($_POST['giaban']) ? (int)$_POST['giaban'] : 0;
$soluongtonkho = isset($_POST['soluongtonkho']) ? (int)$_POST['soluongtonkho'] : 0;
// Thư mục lưu ảnh (thư mục `anhsp` nằm cùng cấp với thư mục `truyvan`)
$uploadDir = __DIR__ . '/../anhsp/';
$uploadUrlPath = 'anhsp/'; // đường dẫn tương đối lưu vào database (vd: anhsp/filename.jpg)

$maxFileSize = 10 * 1024 * 1024; // 5 MB
$allowedMime = array('image/jpeg', 'image/png', 'image/gif');


if (empty($mancc)) {
	echo json_encode(array('success' => false, 'message' => 'Thiếu nhà cung cấp'), JSON_UNESCAPED_UNICODE);
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
$oldPath = null;
$manccEsc = mysqli_real_escape_string($conn, $mancc);
$dbPathEsc = mysqli_real_escape_string($conn, $dbPath);
// Bắt đầu transaction
$conn->begin_transaction();
try {
	$query= "INSERT INTO spban VALUES ('$manccEsc','$masp')";
	mysqli_query($conn, $query);
	$sqlInsert = "INSERT INTO sanpham(hinhanh,masp,tensanpham,mota,giagoc,giaban,soluongtonkho) VALUES ('$dbPathEsc','$masp','$tensanpham','$mota','$giagoc','$giaban','$soluongtonkho')";
	if (!mysqli_query($conn, $sqlInsert)) {
		throw new Exception('Lỗi khi thêm sản phẩm: ' . mysqli_error($conn));
	}
	$affected = mysqli_affected_rows($conn);
	if ($affected > 0) {
		$conn->commit();
		echo json_encode(array('success' => true, 'message' => 'Upload và thêm sản phẩm thành công','path' => $dbPath), JSON_UNESCAPED_UNICODE);
		$conn->close();
		exit;
	} else {
		throw new Exception('Không thêm được sản phẩm');
	}
} catch (Exception $e) {
	$conn->rollback();
	if (file_exists($destination)) {@unlink($destination);}
	echo json_encode(array('success' => false, 'message' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
	$conn->close();
	exit;
}

?>
