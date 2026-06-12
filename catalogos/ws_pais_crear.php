<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);
include '../conexion.php';

$idTipoDoc = isset($_REQUEST['idTipoDoc']) ? trim($_REQUEST['idTipoDoc']) : "";
$desTipoDoc = isset($_REQUEST['desTipoDoc']) ? trim($_REQUEST['desTipoDoc']) : "";
$longitudMin = isset($_REQUEST['longitudMin']) ? trim($_REQUEST['longitudMin']) : "";
$longitudMax = isset($_REQUEST['longitudMax']) ? trim($_REQUEST['longitudMax']) : "";
$regexValidacion = isset($_REQUEST['regexValidacion']) ? trim($_REQUEST['regexValidacion']) : "";
$formatoEjemplo = isset($_REQUEST['formatoEjemplo']) ? trim($_REQUEST['formatoEjemplo']) : "";
$mensajeError = isset($_REQUEST['mensajeError']) ? trim($_REQUEST['mensajeError']) : "";
$activo = isset($_REQUEST['activo']) ? trim($_REQUEST['activo']) : "1";

if (
    $idTipoDoc === "" ||
    $desTipoDoc === "" ||
    $longitudMin === "" ||
    $longitudMax === "" ||
    $regexValidacion === "" ||
    $mensajeError === ""
) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Todos los campos obligatorios deben completarse"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if (!is_numeric($longitudMin) || !is_numeric($longitudMax) || !is_numeric($activo)) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Longitudes y activo deben ser valores numéricos"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$longitudMin = intval($longitudMin);
$longitudMax = intval($longitudMax);
$activo = intval($activo);

if ($longitudMin <= 0 || $longitudMax <= 0) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Las longitudes deben ser mayores a cero"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($longitudMin > $longitudMax) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "La longitud mínima no puede ser mayor que la máxima"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($activo !== 0 && $activo !== 1) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Activo debe ser 1 o 0"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "INSERT INTO TIPO_DOCUMENTO
        (IDTIPODOC, DESTIPODOC, LONGITUDMIN, LONGITUDMAX,
         REGEXVALIDACION, FORMATOEJEMPLO, MENSAJEERROR, ACTIVO)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param(
    "ssiisssi",
    $idTipoDoc,
    $desTipoDoc,
    $longitudMin,
    $longitudMax,
    $regexValidacion,
    $formatoEjemplo,
    $mensajeError,
    $activo
);

if ($stmt->execute()) {
    echo json_encode([
        "resultado" => "1",
        "mensaje" => "Tipo de documento registrado correctamente"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>