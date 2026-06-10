<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$idTurista       = isset($_POST['idTurista'])       ? trim($_POST['idTurista'])       : '';
$idVacuna        = isset($_POST['idVacuna'])        ? trim($_POST['idVacuna'])        : '';
$fechaAplicacion = isset($_POST['fechaAplicacion']) ? trim($_POST['fechaAplicacion']) : '';
$fechaVigencia   = isset($_POST['fechaVigencia'])   ? trim($_POST['fechaVigencia'])   : '';

if (empty($idTurista) || empty($idVacuna) || empty($fechaAplicacion) || empty($fechaVigencia)) {
    echo json_encode(["resultado" => "0", "mensaje" => "Campos incompletos en el servidor."]);
    exit();
}

$respuesta = [];

try {
    $sql = "INSERT INTO VACUNA_TURISTA (IDTURISTA, IDVACUNA, FECHAAPLICACION, FECHAVIGENCIA) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $idTurista, $idVacuna, $fechaAplicacion, $fechaVigencia);

    if ($stmt->execute()) {
        $respuesta["resultado"] = "1";
        $respuesta["mensaje"] = "Registro guardado exitosamente.";
    } else {
        $respuesta["resultado"] = "0";
        $respuesta["mensaje"] = "No se pudo registrar el elemento.";
    }
    $stmt->close();

} catch (mysqli_sql_exception $e) {
    $respuesta["resultado"] = "0";
    
    if ($e->getCode() == 1062 || str_contains($e->getMessage(), 'ya tiene registrada')) {
        $respuesta["mensaje"] = "Este turista ya tiene registrada esta vacuna.";
    } else {
        $respuesta["mensaje"] = "Restricción: " . $e->getMessage();
    }
}

echo json_encode($respuesta);
$conn->close();
exit();
?>