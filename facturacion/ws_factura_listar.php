<?php
// ===========================================================================
// SERVICIO (Kevin - Facturacion): LISTAR FACTURAS
// Devuelve todas las facturas en JSON. Filtros OPCIONALES por GET:
//   estado     -> "Pagada" | "Pendiente" | "Anulada"
//   fechaDesde -> "YYYY-MM-DD"  (FECHAEMISION >= fechaDesde)
//   fechaHasta -> "YYYY-MM-DD"  (FECHAEMISION <= fechaHasta)
//
// Pruebas en navegador:
//   http://localhost/Servicios-PHP/facturacion/ws_factura_listar.php
//   http://localhost/Servicios-PHP/facturacion/ws_factura_listar.php?estado=Pagada
// ===========================================================================

header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

// ---- 1) Leer parametros opcionales ----
$estado     = isset($_REQUEST['estado'])     ? trim($_REQUEST['estado'])     : "";
$fechaDesde = isset($_REQUEST['fechaDesde']) ? trim($_REQUEST['fechaDesde']) : "";
$fechaHasta = isset($_REQUEST['fechaHasta']) ? trim($_REQUEST['fechaHasta']) : "";

// ---- 2) Armar el WHERE solo con los filtros que llegaron ----
$condiciones = array();
$tipos       = "";
$valores     = array();

if ($estado !== "") {
    $condiciones[] = "ESTADO = ?";
    $tipos        .= "s";
    $valores[]     = $estado;
}
if ($fechaDesde !== "") {
    $condiciones[] = "FECHAEMISION >= ?";
    $tipos        .= "s";
    $valores[]     = $fechaDesde;
}
if ($fechaHasta !== "") {
    $condiciones[] = "FECHAEMISION <= ?";
    $tipos        .= "s";
    $valores[]     = $fechaHasta;
}

$where = "";
if (count($condiciones) > 0) {
    $where = "WHERE " . implode(" AND ", $condiciones);
}

// ---- 3) Construir y ejecutar la consulta (sentencia preparada) ----
$sql = "SELECT IDFACTURA, IDTIPOAGRUP, IDFORMAPAGO,
               FECHAEMISION, NOMBREGRUPO, TOTAL, ESTADO
        FROM FACTURA
        $where
        ORDER BY FECHAEMISION DESC";

$stmt = $conn->prepare($sql);
if (count($valores) > 0) {
    $stmt->bind_param($tipos, ...$valores);
}
$stmt->execute();
$resultado = $stmt->get_result();

// ---- 4) Recoger filas y devolver JSON ----
$filas = array();
while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}
echo json_encode($filas);

// ---- 5) Cerrar ----
$stmt->close();
$conn->close();
?>