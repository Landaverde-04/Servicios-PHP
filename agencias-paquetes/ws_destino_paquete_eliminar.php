<?php
// ===========================================================================
// SERVICIO (Kevin): QUITAR UN DESTINO DE UN PAQUETE
// ----------------------------------------------------------------------------
// Elimina una fila de PAQUETE_DESTINO (la asociacion paquete-destino) en MySQL.
// NO elimina el destino en si, solo lo quita de ese paquete.
//
// Parametros (GET o POST):
//   idPaquete : ej "PQ04"
//   idDestino : ej "D02"
//
// Respuesta (objeto JSON):
//   {"resultado":"1","mensaje":"Destino quitado del paquete"}
//   {"resultado":"0","mensaje":"..."}   si no existia o hubo error
//
// Prueba en navegador:
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_destino_paquete_eliminar.php?idPaquete=PQ04&idDestino=D02
// ===========================================================================

header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

// Leer parametros
$idPaquete = isset($_REQUEST['idPaquete']) ? trim($_REQUEST['idPaquete']) : "";
$idDestino = isset($_REQUEST['idDestino']) ? trim($_REQUEST['idDestino']) : "";

// Validar que vengan los dos
if ($idPaquete === "" || $idDestino === "") {
    echo json_encode(["resultado" => "0", "mensaje" => "Faltan parametros: idPaquete e idDestino"]);
    $conn->close();
    exit;
}

// Intentar eliminar la asociacion
try {
    $stmt = $conn->prepare("DELETE FROM PAQUETE_DESTINO WHERE IDPAQUETE = ? AND IDDESTINO = ?");
    $stmt->bind_param("ss", $idPaquete, $idDestino);
    $stmt->execute();

    // affected_rows dice cuantas filas se borraron realmente
    if ($stmt->affected_rows > 0) {
        echo json_encode(["resultado" => "1", "mensaje" => "Destino quitado del paquete correctamente"]);
    } else {
        echo json_encode(["resultado" => "0", "mensaje" => "Esa asociacion no existe (nada que eliminar)"]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    echo json_encode(["resultado" => "0", "mensaje" => $e->getMessage()]);
}

$conn->close();
?>