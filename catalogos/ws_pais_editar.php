<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);
include '../conexion.php';

$idPais = isset($_REQUEST['idPais']) ? trim($_REQUEST['idPais']) : "";
$nombrePais = isset($_REQUEST['nombrePais']) ? trim($_REQUEST['nombrePais']) : "";

if ($idPais === "" || $nombrePais === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Todos los campos son obligatorios"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "UPDATE PAIS SET NOMBREPAIS = ? WHERE IDPAIS = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("ss", $nombrePais, $idPais);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "País actualizado correctamente"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "No hubo cambios o el país no existe"
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