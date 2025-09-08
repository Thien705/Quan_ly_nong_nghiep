<?php
// Cấu hình kết nối DB qlvt
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

$MaND = $_POST['MaND'] ?? '';
$HoTen = $_POST['HoTen'] ?? '';
$NgaySinh = $_POST['NgaySinh'] ?? '';

if ($MaND == '' || $HoTen == '' || $NgaySinh == '') {
    echo json_encode(["success" => false, "message" => "Thiếu thông tin: Mã nông dân, họ tên, ngày sinh"]);
    exit;
}

// Kiểm tra mã nông dân có tồn tại không
$sql_check_nd = "SELECT * FROM nongdan WHERE MaND = ?";
$stmt_check = $conn->prepare($sql_check_nd);
$stmt_check->bind_param("s", $MaND);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    echo json_encode(["success" => false, "message" => "Mã nông dân không tồn tại"]);
    exit;
}

// Tạo username từ họ tên (loại bỏ dấu, khoảng trắng, chuyển thường)
function createUsername($hoTen) {
    $username = strtolower($hoTen);
    $username = str_replace(' ', '', $username);
    
    // Loại bỏ dấu tiếng Việt
    $vietnamese = array(
        'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
        'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
        'ì','í','ị','ỉ','ĩ',
        'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
        'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
        'ỳ','ý','ỵ','ỷ','ỹ',
        'đ'
    );
    $replace = array(
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y',
        'd'
    );
    
    return str_replace($vietnamese, $replace, $username);
}

// Tạo mật khẩu từ ngày sinh (định dạng ddmmyyyy)
function createPassword($ngaySinh) {
    $date = DateTime::createFromFormat('Y-m-d', $ngaySinh);
    if ($date) {
        return $date->format('dmY'); // ddmmyyyy
    }
    return str_replace('-', '', $ngaySinh); // Fallback
}

$username = createUsername($HoTen);
$password = createPassword($NgaySinh);
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Kiểm tra username đã tồn tại chưa
$sql_check_user = "SELECT * FROM users WHERE username = ?";
$stmt_user = $conn->prepare($sql_check_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Tài khoản với username '$username' đã tồn tại"]);
    exit;
}

// Thêm tài khoản mới theo cấu trúc bảng của bạn
$sql_insert = "INSERT INTO users (username, email, hashpass, role, MaND) VALUES (?, ?, ?, 'user', ?)";
$stmt_insert = $conn->prepare($sql_insert);

// Tạo email từ username + @gmail.com
$email = $username . "@gmail.com";

$stmt_insert->bind_param("ssss", $username, $email, $hashedPassword, $MaND);

if ($stmt_insert->execute()) {
    echo json_encode([
        "success" => true, 
        "message" => "Tạo tài khoản thành công",
        "username" => $username,
        "email" => $email,
        "password_default" => $password,
        "ma_nong_dan" => $MaND
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Lỗi khi tạo tài khoản: " . $stmt_insert->error]);
}

$stmt_check->close();
$stmt_user->close();
$stmt_insert->close();
$conn->close();
?>