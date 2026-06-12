<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);
include '../conexion.php';

$idMotivacion = isset($_REQUEST['idMotivacion']) ? trim($_REQUEST['idMotivacion']) : "";

if ($idMotivacion === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Debe enviar el ID de la motivación"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "DELETE FROM MOTIVACIONES WHERE IDMOTIVACION = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("s", $idMotivacion);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "Motivación eliminada correctamente"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "resultado" => "0",
            "mensaje" => "No existe una motivación con ese ID"
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