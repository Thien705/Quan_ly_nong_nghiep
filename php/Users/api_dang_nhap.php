<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Kết nối database
$host = "localhost";
$user = "root";   // đổi theo user của MySQL
$pass = "";       // đổi theo mật khẩu MySQL
$db   = "qlvt";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Kết nối DB thất bại"]);
    exit();
}

// Hỗ trợ cả JSON và form-data
$data = json_decode(file_get_contents("php://input"), true);

if ($data) {
    $username = isset($data["username"]) ? trim($data["username"]) : "";
    $password = isset($data["password"]) ? trim($data["password"]) : "";
} else {
    $username = isset($_POST["username"]) ? trim($_POST["username"]) : "";
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
}

if (empty($username) || empty($password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Thiếu username hoặc password"]);
    exit();
}

// Truy vấn lấy user
$sql = "SELECT id, username, email, hashpass, role FROM users WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row["hashpass"])) {
        echo json_encode([
            "status" => "success",
            "message" => "Đăng nhập thành công",
            "user" => [
                "id" => $row["id"],
                "username" => $row["username"],
                "email" => $row["email"],
                "role" => $row["role"]
            ]
        ]);
    } else {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Sai mật khẩu"]);
    }
} else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Không tìm thấy user"]);
}

$stmt->close();
$conn->close();
?>
