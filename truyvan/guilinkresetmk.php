<?php
// Import các file cần thiết
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
header('Content-Type: application/json; charset=utf-8');
include 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Cấu hình
define('SMTP_USER', 'benoobfreefire13102005@gmail.com');
define('SMTP_PASS', 'cgemuacqbvqbgmgn');
define('SMTP_FROM_NAME', 'APP Bán Hàng');

// Lưu hoặc cập nhật token vào database
function saveResetToken($email, $token) {
    global $conn;
    
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $expiryTime = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    $email = mysqli_real_escape_string($conn, $email);
    $token = mysqli_real_escape_string($conn, $token);
    $expiryTime = mysqli_real_escape_string($conn, $expiryTime);
    $checkSql = "SELECT email FROM resetmk WHERE email = '$email'";
    $checkResult = mysqli_query($conn, $checkSql);
    
    if (mysqli_num_rows($checkResult) > 0) {
        $updateSql = "UPDATE resetmk 
                      SET token = '$token', 
                          timetoken = '$expiryTime'
                      WHERE email = '$email'";
        $result = mysqli_query($conn, $updateSql);
    } else {
        // Nếu chưa tồn tại, INSERT mới
        $insertSql = "INSERT INTO resetmk (email, token, timetoken) 
                      VALUES ('$email', '$token', '$expiryTime')";
        $result = mysqli_query($conn, $insertSql);
    }
    
    return $result;
}
function emailExists($email) {
    global $conn;
    
    $email = mysqli_real_escape_string($conn, $email);
    $sql = "SELECT email FROM taikhoan WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    
    return mysqli_num_rows($result) > 0;
}

function sendPasswordResetEmail($email, $resetToken) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';
        
        $mail->setFrom(SMTP_USER, SMTP_FROM_NAME);
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Yêu cầu đặt lại mật khẩu';
        $mail->Body = "
            <h2>Đặt lại mật khẩu</h2>
            <p>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản của mình.</p>
            <p>Mã để đặt lại mật khẩu:</p>
            <h2><b>$resetToken</b></h2>
            <p><small>Mã này sẽ hết hạn sau 1 giờ.</small></p>
            <p><small>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</small></p>
        ";
        $mail->send();
        return ['success' => true, 'message' => 'Email đã được gửi thành công! Vui lòng kiểm tra hộp thư của bạn.'];
        
    } catch (Exception $e) {
        return ['success' => false, 'message' => "Lỗi gửi email: {$mail->ErrorInfo}"];
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if (empty($email)) {
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập email!']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Email không hợp lệ!']);
        exit;
    }
    
    // Kiểm tra email có tồn tại không
    if (!emailExists($email)) {
        echo json_encode(['success' => false, 'message' => 'Email không tồn tại trong hệ thống!']);
        exit;
    }
    $resetToken =rand(10000000, 99999999); // Tạo token ngẫu nhiên
    if (saveResetToken($email, $resetToken)) {
        $result = sendPasswordResetEmail($email, $resetToken);
        echo json_encode($result);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi lưu token vào database!']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
}
?>