<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);
include '../conexion.php';

$idPais = isset($_REQUEST['idPais']) ? trim($_REQUEST['idPais']) : "";

if ($idPais === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Debe enviar el ID del país"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "DELETE FROM PAIS WHERE IDPAIS = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("s", $idPais);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "País eliminado correctamente"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "resultado" => "0",
            "mensaje" => "No existe un país con ese ID"
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