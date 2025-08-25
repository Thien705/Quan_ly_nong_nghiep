<?php
include 'config.php';
header('Content-Type: application/json');

// Lấy dữ liệu từ POST
$MaC = $_POST['MaC'] ?? '';
$TenC = $_POST['TenC'] ?? '';
$CachCanhTac = $_POST['CachCanhTac'] ?? '';
$PhanBon = $_POST['PhanBon'] ?? '';
$NguonGoc = $_POST['NguonGoc'] ?? '';

// Kiểm tra dữ liệu không được rỗng
if ($MaC && $TenC && $CachCanhTac && $PhanBon && $NguonGoc) {
    $stmt = $conn->prepare("INSERT INTO caytrong (MaC, TenC, CachCanhTac, PhanBon, NguonGoc) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $MaC, $TenC, $CachCanhTac, $PhanBon, $NguonGoc);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Thêm cây trồng thành công"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Lỗi khi thêm dữ liệu: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "error", "message" => "Thiếu dữ liệu"]);
}

$conn->close();
?>
