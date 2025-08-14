<?php
include 'config.php';
header('Content-Type: application/json');

// Lấy dữ liệu từ POST
$MaC = $_POST['MaC'] ?? '';
$TenC = $_POST['TenC'] ?? '';
$CachSuDung = $_POST['CachSuDung'] ?? '';

// Kiểm tra dữ liệu không được rỗng
if ($MaC && $TenC && $CachSuDung) {
    $stmt = $conn->prepare("INSERT INTO caytrong (MaC, TenC, CachSuDung) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $MaC, $TenC, $CachSuDung);

    if ($stmt->execute()) {
        echo json_encode(array("status" => "success", "message" => "Thêm thành công"));
    } else {
        echo json_encode(array("status" => "error", "message" => "Lỗi khi thêm dữ liệu"));
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu"]);
}

$conn->close();
?>