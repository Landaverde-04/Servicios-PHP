<?php
// ===========================================================================
// SERVICIO (Kevin): LISTAR DESTINOS POR PAQUETE
// ----------------------------------------------------------------------------
// Devuelve, en lista plana, cada combinacion paquete-destino desde MySQL.
// Filtro OPCIONAL por paquete:
//   ?idPaquete=PQ01  -> solo los destinos de ese paquete
//   (sin parametro)  -> todos los paquetes con sus destinos
//
// Sirve para: la vista PHP (mostrar) y la sincronizacion (descargar a SQLite).
//
// Prueba en navegador:
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_destino_paquete_listar.php
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_destino_paquete_listar.php?idPaquete=PQ01
// ===========================================================================

header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

// Leer el filtro opcional
$idPaquete = isset($_REQUEST['idPaquete']) ? trim($_REQUEST['idPaquete']) : "";

// Consulta base: JOIN de las 3 tablas (relacion + destino + paquete)
$sql = "SELECT P.IDPAQUETE,
               P.NOMBREPAQUETE,
               D.IDDESTINO,
               D.IDPAIS,
               D.NOMBREDESTINO,
               D.DESDESTINO,
               D.REQUIEREVISAESP
        FROM PAQUETE_TURISTICO P
        LEFT JOIN PAQUETE_DESTINO PD ON P.IDPAQUETE = PD.IDPAQUETE
        LEFT JOIN DESTINO D          ON PD.IDDESTINO = D.IDDESTINO";

if ($idPaquete !== "") {
    $sql .= " WHERE P.IDPAQUETE = ?";
}
$sql .= " ORDER BY P.NOMBREPAQUETE, D.NOMBREDESTINO";

// Ejecutar con sentencia preparada
$stmt = $conn->prepare($sql);
if ($idPaquete !== "") {
    $stmt->bind_param("s", $idPaquete);
}
$stmt->execute();
$resultado = $stmt->get_result();

// Recoger filas y devolver JSON
$filas = array();
while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}
echo json_encode($filas);

$stmt->close();
$conn->close();
?>