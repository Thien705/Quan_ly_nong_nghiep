<?php
// Thêm vào đầu file api_change_password.php để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cấu hình kết nối DB
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "qlvt";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Kết nối database thất bại: " . $conn->connect_error]);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

// Lấy dữ liệu từ POST
$input_username = $_POST['username'] ?? '';
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if (empty($input_username) || empty($current_password) || empty($new_password)) {
    echo json_encode(["success" => false, "message" => "Vui lòng nhập đầy đủ thông tin!"]);
    exit;
}

// Kiểm tra mật khẩu mới có đủ điều kiện
if (strlen($new_password) < 6) {
    echo json_encode(["success" => false, "message" => "Mật khẩu mới phải có ít nhất 6 ký tự!"]);
    exit;
}

// Tìm user trong database - kiểm tra tất cả cột
$sql_check = "SELECT * FROM users WHERE username = ?";
$stmt_check = $conn->prepare($sql_check);

if (!$stmt_check) {
    echo json_encode(["success" => false, "message" => "Lỗi prepare statement: " . $conn->error]);
    exit;
}

$stmt_check->bind_param("s", $input_username);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Tên đăng nhập không tồn tại!"]);
    $stmt_check->close();
    $conn->close();
    exit;
}

$user = $result->fetch_assoc();
$stmt_check->close();

// Debug: Kiểm tra dữ liệu user
error_log("User data: " . print_r($user, true));

// Kiểm tra cột password có tồn tại không
$stored_password = '';
if (isset($user['hashpass']) && !empty($user['hashpass'])) {
    $stored_password = $user['hashpass'];
} elseif (isset($user['password']) && !empty($user['password'])) {
    $stored_password = $user['password'];
} else {
    // Nếu chưa có mật khẩu được hash, hash mật khẩu hiện tại
    $hashed_current = password_hash($current_password, PASSWORD_DEFAULT);
    
    // Cập nhật vào database
    $sql_init = "UPDATE users SET hashpass = ? WHERE username = ?";
    $stmt_init = $conn->prepare($sql_init);
    $stmt_init->bind_param("ss", $hashed_current, $input_username);
    $stmt_init->execute();
    $stmt_init->close();
    
    $stored_password = $hashed_current;
}

// Kiểm tra mật khẩu hiện tại
if (!password_verify($current_password, $stored_password)) {
    // Thử kiểm tra trực tiếp nếu mật khẩu chưa được hash
    if ($current_password !== $stored_password) {
        echo json_encode(["success" => false, "message" => "Mật khẩu hiện tại không đúng!"]);
        $conn->close();
        exit;
    }
}

// Kiểm tra mật khẩu mới không trùng với mật khẩu cũ
if ($current_password === $new_password) {
    echo json_encode(["success" => false, "message" => "Mật khẩu mới không được trùng với mật khẩu hiện tại!"]);
    $conn->close();
    exit;
}

// Hash mật khẩu mới
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Cập nhật mật khẩu trong database
$sql_update = "UPDATE users SET hashpass = ? WHERE username = ?";
$stmt_update = $conn->prepare($sql_update);

if (!$stmt_update) {
    echo json_encode(["success" => false, "message" => "Lỗi prepare statement: " . $conn->error]);
    $conn->close();
    exit;
}

$stmt_update->bind_param("ss", $new_hashed_password, $input_username);

if ($stmt_update->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Đổi mật khẩu thành công!",
        "username" => $input_username
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi cập nhật mật khẩu: " . $stmt_update->error]);
}

$stmt_update->close();
$conn->close();
?>