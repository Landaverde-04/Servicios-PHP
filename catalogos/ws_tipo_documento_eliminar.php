<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);
include '../conexion.php';

$idTipoDoc = isset($_REQUEST['idTipoDoc']) ? trim($_REQUEST['idTipoDoc']) : "";

if ($idTipoDoc === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Debe enviar el ID del tipo de documento"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "DELETE FROM TIPO_DOCUMENTO WHERE IDTIPODOC = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param("s", $idTipoDoc);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "Tipo de documento eliminado correctamente"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "resultado" => "0",
            "mensaje" => "No existe un tipo de documento con ese ID"
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