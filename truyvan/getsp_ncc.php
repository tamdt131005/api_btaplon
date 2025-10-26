<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

$mancc = isset($_POST['mancc']) ? $_POST['mancc'] : '';
if (empty($mancc)) {
    echo json_encode(array('success' => false, 'message' => 'Mã nhà cung cấp không được để trống'), JSON_UNESCAPED_UNICODE);
    exit();
}
$query = "SELECT masp, tensanpham, hinhanh, mota, giagoc, giaban, soluongtonkho, donvitinh, trangthai, luotban 
FROM sanpham 
WHERE masp in (SELECT masp FROM spban WHERE mancc = '$mancc')";
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
        'message' => 'Lấy thông tin sản phẩm nhà cung cấp thành công',
        'result' => $result

   ]  ;
        
} else {
    $arr = array('success' => false, 'message' => 'Không tìm thấy sản phẩm cho nhà cung cấp này'

);
}
print_r(json_encode($arr, JSON_UNESCAPED_UNICODE));
$conn->close();;
?>