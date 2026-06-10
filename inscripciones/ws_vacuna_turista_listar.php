<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$idTurista = isset($_REQUEST['idTurista']) ? trim($_REQUEST['idTurista']) : "";

if ($idTurista === "") {
    echo json_encode([]);
    exit();
}

$sql = "SELECT 
            T.NOMBRES, 
            T.APELLIDOS,
            V.NOMBREVACUNA, 
            E.NOMBREENFERMEDAD, 
            VT.FECHAAPLICACION, 
            VT.FECHAVIGENCIA,
            T.IDTURISTA,
            V.IDVACUNA
        FROM VACUNA_TURISTA VT
        INNER JOIN TURISTA T ON VT.IDTURISTA = T.IDTURISTA
        INNER JOIN VACUNA V  ON VT.IDVACUNA = V.IDVACUNA
        INNER JOIN ENFERMEDAD E ON V.IDENFERMEDAD = E.IDENFERMEDAD
        WHERE VT.IDTURISTA = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idTurista);
$stmt->execute();
$resultado = $stmt->get_result();

$filas = array();
while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}

echo json_encode($filas);

$stmt->close();
$conn->close();
?>