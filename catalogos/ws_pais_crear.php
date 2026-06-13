<?php
header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_OFF);

include '../conexion.php';

$idPais = isset($_REQUEST['idPais'])
    ? trim($_REQUEST['idPais'])
    : "";

$nombrePais = isset($_REQUEST['nombrePais'])
    ? trim($_REQUEST['nombrePais'])
    : "";

if ($idPais === "" || $nombrePais === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Todos los campos son obligatorios"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "INSERT INTO PAIS (IDPAIS, NOMBREPAIS)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param(
    "ss",
    $idPais,
    $nombrePais
);

if ($stmt->execute()) {
    echo json_encode([
        "resultado" => "1",
        "mensaje" => "País registrado correctamente"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al guardar país: " . $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?><?php
header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_OFF);

include '../conexion.php';

$idPais = isset($_REQUEST['idPais'])
    ? trim($_REQUEST['idPais'])
    : "";

$nombrePais = isset($_REQUEST['nombrePais'])
    ? trim($_REQUEST['nombrePais'])
    : "";

if ($idPais === "" || $nombrePais === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Todos los campos son obligatorios"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$sql = "INSERT INTO PAIS (IDPAIS, NOMBREPAIS)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar consulta: " . $conn->error
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt->bind_param(
    "ss",
    $idPais,
    $nombrePais
);

if ($stmt->execute()) {
    echo json_encode([
        "resultado" => "1",
        "mensaje" => "País registrado correctamente"
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al guardar país: " . $stmt->error
    ], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
?>