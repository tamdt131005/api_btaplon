
<?php
// Tạo mã địa chỉ tự động
$madiachi = 'PN' . time() . rand(1000, 9999);
date_default_timezone_set('Asia/Ho_Chi_Minh');
$expiryTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
print_r($madiachi);
print_r($expiryTime);
$current_time = date('Y-m-d H:i:s');
print_r($current_time);
?>