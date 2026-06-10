<?php
// ===========================================================================
// SERVICIO (Kevin): LISTAR TODOS LOS DESTINOS
// ----------------------------------------------------------------------------
// Devuelve todos los destinos del catalogo desde MySQL, en JSON.
// Se usa para poblar el selector de "agregar destino a paquete" en la vista PHP.
//
// Prueba en navegador:
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_destino_listar.php
// ===========================================================================

header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$sql = "SELECT IDDESTINO, IDPAIS, NOMBREDESTINO, DESDESTINO, REQUIEREVISAESP
        FROM DESTINO
        ORDER BY NOMBREDESTINO";

$resultado = $conn->query($sql);

$filas = array();
while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}
echo json_encode($filas);

$conn->close();
?>