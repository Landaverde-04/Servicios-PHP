<?php
header('Content-Type: application/json; charset=utf-8');
mysqli_report(MYSQLI_REPORT_OFF);
include '../conexion.php';

// 1) Leer parámetro opcional de búsqueda
$buscar = isset($_REQUEST['buscar']) ? trim($_REQUEST['buscar']) : "";

// 2) Consulta con JOIN a PAIS
//    El WHERE ahora incluye p.NOMBREPAIS para que buscar=Honduras funcione
$sql = "SELECT t.IDTURISTA, 
               t.IDPAIS, 
               p.NOMBREPAIS,
               t.NOMBRES, 
               t.APELLIDOS, 
               t.FECHANACIMIENTO, 
               t.TELEFONO, 
               t.CORREO, 
               t.TIENEVISA
        FROM TURISTA t
        LEFT JOIN PAIS p ON t.IDPAIS = p.IDPAIS";

if ($buscar !== "") {
    // Se agrega p.NOMBREPAIS como tercer criterio de búsqueda
    $sql .= " WHERE t.NOMBRES    LIKE ?
               OR   t.APELLIDOS  LIKE ?
               OR   p.NOMBREPAIS LIKE ?";
}

$sql .= " ORDER BY t.APELLIDOS ASC";

// 3) Sentencia preparada — ahora bind_param recibe 3 parámetros ("sss")
$stmt = $conn->prepare($sql);

if ($buscar !== "") {
    $like = "%" . $buscar . "%";
    $stmt->bind_param("sss", $like, $like, $like);
}

$stmt->execute();
$resultado = $stmt->get_result();

// 4) Recoger filas
$filas = [];
while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}

// 5) Responder
if (count($filas) > 0) {
    echo json_encode($filas);
} else {
    echo json_encode([
        "resultado" => "0",
        "mensaje"   => "No se encontraron turistas registrados."
    ]);
}

// ------------------------------------------------------
// PRUEBAS EN NAVEGADOR:
// http://localhost/Servicios-PHP/turistas/ws_turista_listar.php
// http://localhost/Servicios-PHP/turistas/ws_turista_listar.php?buscar=Pérez
// http://localhost/Servicios-PHP/turistas/ws_turista_listar.php?buscar=Honduras
// http://localhost/Servicios-PHP/turistas/ws_turista_listar.php?buscar=El Salvador
// ------------------------------------------------------

$stmt->close();
$conn->close();
?>