<?php
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';
$mancc = isset($_POST['mancc']) ? trim($_POST['mancc']) : '';

if (empty($mancc)) {
    echo json_encode(array('success' => false, 'message' => 'Vui lòng nhập mã nhà cung cấp'), JSON_UNESCAPED_UNICODE);
    exit;
}
$query = "SELECT username,tennhacungcap, sodienthoai,email FROM nhacungcap WHERE mancc = '$mancc'";
$result = mysqli_query($conn, $query);
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}
if (!empty($data)) {
    $arr = [
        'success' => true,
        'message' => 'Lấy thông tin nhà cung cấp thành công',
        'result' => $data[0]
   ];
        
} else {
    $arr = [
        'success' => true,
        'message' => 'Không tìm thấy nhà cung cấp',
        'result' => null
   ];


}

echo json_encode($arr, JSON_UNESCAPED_UNICODE);
$conn->close();   
?>