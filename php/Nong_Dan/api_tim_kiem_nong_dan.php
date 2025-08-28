<?php
// Bật thông báo lỗi chi tiết (debug)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
header('Content-Type: application/json; charset=UTF-8');

// Nhận keyword từ client
$keyword = $_POST['keyword'] ?? '';

// Nếu có từ khóa thì tìm kiếm
if (!empty($keyword)) {
    try {
        // Câu lệnh SQL: tìm trong MaND, HoTen, CuTru
        $sql = "SELECT MaND, HoTen, CuTru 
                FROM nongdan 
                WHERE MaND LIKE ? OR HoTen LIKE ? OR CuTru LIKE ?";

        $stmt = $conn->prepare($sql);
        $like = "%" . $keyword . "%";
        $stmt->bind_param("sss", $like, $like, $like);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        echo json_encode([
            "status" => "success",
            "data" => $data
        ], JSON_UNESCAPED_UNICODE);

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Lỗi: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Thiếu từ khóa tìm kiếm"
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
