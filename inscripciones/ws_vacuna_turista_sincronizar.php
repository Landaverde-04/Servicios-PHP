<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$sql = "SELECT IDTURISTA, IDVACUNA, FECHAAPLICACION, FECHAVIGENCIA FROM VACUNA_TURISTA";
$resultado = $conn->query($sql);

$respuesta = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $respuesta[] = [
            "IDTURISTA"       => $fila["IDTURISTA"],
            "IDVACUNA"        => $fila["IDVACUNA"],
            "FECHAAPLICACION" => $fila["FECHAAPLICACION"],
            "FECHAVIGENCIA"   => $fila["FECHAVIGENCIA"]
        ];
    }
}

echo json_encode($respuesta);

$conn->close();
exit();
?>