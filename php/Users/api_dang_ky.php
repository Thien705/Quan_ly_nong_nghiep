<?php
header("Content-Type: application/json; charset=UTF-8");

// Kết nối MySQL
$host = "localhost";
$user = "root"; 
$pass = "";
$dbname = "qlvt"; // đổi theo tên CSDL của bạn

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Kết nối CSDL thất bại"]));
}

// Lấy dữ liệu POST
$username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
$email    = isset($_POST["email"]) ? trim($_POST["email"]) : "";
$password = isset($_POST["hashpass"]) ? trim($_POST["hashpass"]) : "";

// Kiểm tra thiếu dữ liệu
if ($username === "" || $email === "" || $password === "") {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu"]);
    exit();
}

// Kiểm tra username hoặc email đã tồn tại
$check = $conn->prepare("SELECT id FROM Users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Username hoặc Email đã tồn tại"]);
    $check->close();
    $conn->close();
    exit();
}
$check->close();

// Hash mật khẩu
$hashpass = password_hash($password, PASSWORD_DEFAULT);

// Thêm dữ liệu vào bảng
$stmt = $conn->prepare("INSERT INTO Users (username, email, hashpass, role) VALUES (?, ?, ?, 'user')");
$stmt->bind_param("sss", $username, $email, $hashpass);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Đăng ký thành công"]);
} else {
    echo json_encode(["status" => "error", "message" => "Lỗi khi thêm tài khoản"]);
}

$stmt->close();
$conn->close();
?>
