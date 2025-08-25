<?php
$host = 'localhost';
$db   = 'qlvt';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
header('Content-Type: application/json');

// Kiểm tra kết nối
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "data" => [],
        "message" => "Kết nối thất bại: " . $conn->connect_error
    ]);
    exit();
}

// Lấy từ khóa tìm kiếm
$keyword = isset($_POST['keyword']) ? trim($_POST['keyword']) : "";

if ($keyword != "") {
    $stmt = $conn->prepare("
        SELECT * FROM caytrong 
        WHERE MaC LIKE ? 
           OR TenC LIKE ? 
           OR CachCanhTac LIKE ? 
           OR PhanBon LIKE ? 
           OR NguonGoc LIKE ?
    ");
    $kw = "%" . $keyword . "%";
    $stmt->bind_param("sssss", $kw, $kw, $kw, $kw, $kw);
} else {
    $stmt = $conn->prepare("SELECT * FROM caytrong");
}

if ($stmt->execute()) {
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $data,
        "message" => $keyword ? "Kết quả tìm kiếm cho '$keyword'" : "Danh sách cây trồng"
    ]);
} else {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "data" => [],
        "message" => "Lỗi truy vấn: " . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
