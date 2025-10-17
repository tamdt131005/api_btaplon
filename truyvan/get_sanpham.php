<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$query = "SELECT masp, tensanpham, hinhanh, mota, giagoc, giaban, soluongtonkho, donvitinh, trangthai, luotban FROM sanpham order by masp DESC; ";
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
        'message' => 'Lấy thông tin sản phẩm thành công',
        'result' => $result

   ]  ;
        
} else {
    echo json_encode(array('success' => false, 'message' => 'Không tìm thấy sản phẩm'), JSON_UNESCAPED_UNICODE);
}
print_r(json_encode($arr, JSON_UNESCAPED_UNICODE));
$conn->close();
?>
