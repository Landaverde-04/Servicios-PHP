<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);
include '../conexion.php';

$idMotivacion = isset($_REQUEST['idMotivacion']) ? trim($_REQUEST['idMotivacion']) : "";
$descripcion = isset($_REQUEST['descripcion']) ? trim($_REQUEST['descripcion']) : "";

if ($idMotivacion === "" || $descripcion === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Todos los campos son obligatorios"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "UPDATE MOTIVACIONES SET DESMOTIVACION = ? WHERE IDMOTIVACION = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("ss", $descripcion, $idMotivacion);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "Motivación actualizada correctamente"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "No hubo cambios o la motivación no existe"
        ], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>