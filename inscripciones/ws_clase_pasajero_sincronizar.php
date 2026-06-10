<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$idClase    = $_POST['idClase']    ?? '';
$nombreClase = $_POST['nombreClase'] ?? '';

if (empty($idClase) || empty($nombreClase)) {
    echo json_encode(['ok' => false, 'error' => 'Datos incompletos']);
    exit;
}

$sql  = "INSERT INTO CLASE_PASAJERO (IDCLASE, NOMBRECLASE) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $idClase, $nombreClase);

if ($stmt->execute()) {
    echo json_encode(['ok' => true]);
} else {
    echo json_encode(['ok' => false, 'error' => $conn->error]);
}

$conn->close();
?>