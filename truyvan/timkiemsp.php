<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$tensanpham = isset($_POST['tensanpham']) ? trim($_POST['tensanpham']) : '';

// Sử dụng LIKE để tìm kiếm sản phẩm có tên chứa từ khóa
$query = "SELECT masp, tensanpham, hinhanh, mota, giagoc, giaban, soluongtonkho, donvitinh, trangthai, luotban 
          FROM sanpham 
          WHERE tensanpham LIKE '%$tensanpham%' 
          ORDER BY masp DESC";

$data = mysqli_query($conn, $query);
$result = array();

while ($row = mysqli_fetch_assoc($data)) {
    $row['hinhanh'] = isset($row['hinhanh']) && $row['hinhanh'] !== null ? $row['hinhanh'] : '';
    $row['mota'] = isset($row['mota']) && $row['mota'] !== null ? $row['mota'] : '';
    $result[] = $row;
}

if (!empty($result)) {
    $arr = [
        'success' => true,
        'message' => 'Tìm kiếm sản phẩm thành công',
        'result' => $result
    ];
} else {
    $arr = [
        'success' => false,
        'message' => 'Không tìm thấy sản phẩm phù hợp',
        'result' => []
    ];
}

print_r(json_encode($arr, JSON_UNESCAPED_UNICODE));
$conn->close();
?>