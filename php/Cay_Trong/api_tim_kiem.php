<?php
$host = 'localhost';
$db   = 'qlvt';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode([]);
    exit();
}

$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : "";

if ($keyword != "") {
    $stmt = $conn->prepare("SELECT * FROM caytrong WHERE MaC LIKE ? OR TenC LIKE ?");
    $kw = "%" . $keyword . "%";
    $stmt->bind_param("ss", $kw, $kw);
} else {
    $stmt = $conn->prepare("SELECT * FROM caytrong");
}

$stmt->execute();
$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
