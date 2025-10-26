<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$mancc = isset($_POST['mancc']) ? trim($_POST['mancc']) : '';
$ghichu = isset($_POST['ghichu']) ? trim($_POST['ghichu']) : '';
$masp = isset($_POST['masp']) ? trim($_POST['masp']) : '';
$soluongnhap = isset($_POST['soluongnhap']) ? trim($_POST['soluongnhap']) : 0;

$maph = "PN".time().rand(1000,9999);
if (empty($mancc) || empty($masp) || $soluongnhap <= 0) {
    echo json_encode(array('success' => false, 'message' => 'Thiếu thông tin bắt buộc để thêm phiếu nhập'), JSON_UNESCAPED_UNICODE);
    exit;
}
$query = "INSERT INTO phieunhap (maph, mancc, masp, soluongnhap, ghichu) VALUES ('$maph', '$mancc', '$masp', $soluongnhap, '$ghichu')";;
if (mysqli_query($conn, $query)) {
    $qr = "UPDATE sanpham SET soluongtonkho = soluongtonkho + $soluongnhap WHERE masp = '$masp'";
    mysqli_query($conn, $qr);
    $arr = [
        'success' => true,
        'message' => 'Thêm phiếu nhập thành công'
   ]  ;
        
} else {
    $arr = [
        'success' => false,
        'message' => 'Lỗi khi thêm phiếu nhập'
   ]  ;
}
print_r(json_encode($arr, JSON_UNESCAPED_UNICODE));
$conn->close();   
?>