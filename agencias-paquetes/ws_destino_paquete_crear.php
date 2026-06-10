<?php
// ===========================================================================
// SERVICIO (Kevin): AGREGAR DESTINO A UN PAQUETE
// ----------------------------------------------------------------------------
// Inserta una fila en PAQUETE_DESTINO (relacion paquete-destino) en MySQL.
//
// Parametros (por GET o POST, $_REQUEST sirve para ambos):
//   idPaquete : ej "PQ01"
//   idDestino : ej "D03"
//
// Respuesta (objeto JSON):
//   {"resultado":"1","mensaje":"Destino agregado al paquete"}
//   {"resultado":"0","mensaje":"..."}   en caso de error o validacion
//
// Considera:
//   - PK compuesta (IDPAQUETE, IDDESTINO): no se puede duplicar la asociacion.
//   - Trigger trg_bloquear_destino_paquete_con_inscripciones: impide agregar
//     destinos a paquetes con inscripciones activas. Capturamos ese error.
//
// Prueba en navegador:
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_destino_paquete_crear.php?idPaquete=PQ05&idDestino=D01
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

// Verificar que no exista ya la asociacion (PK compuesta)
$check = $conn->prepare("SELECT 1 FROM PAQUETE_DESTINO WHERE IDPAQUETE = ? AND IDDESTINO = ?");
$check->bind_param("ss", $idPaquete, $idDestino);
$check->execute();
if ($check->get_result()->fetch_assoc()) {
    echo json_encode(["resultado" => "0", "mensaje" => "Ese destino ya esta asignado a ese paquete"]);
    $check->close();
    $conn->close();
    exit;
}
$check->close();

// Intentar insertar. El trigger podria abortar con SIGNAL (inscripciones activas).
try {
    $stmt = $conn->prepare("INSERT INTO PAQUETE_DESTINO (IDPAQUETE, IDDESTINO) VALUES (?, ?)");
    $stmt->bind_param("ss", $idPaquete, $idDestino);

    if ($stmt->execute()) {
        echo json_encode(["resultado" => "1", "mensaje" => "Destino agregado al paquete correctamente"]);
    } else {
        echo json_encode(["resultado" => "0", "mensaje" => $stmt->error]);
    }
    $stmt->close();
} catch (mysqli_sql_exception $e) {
    // Aqui cae el mensaje del trigger (ej. "el paquete ya tiene inscripciones activas")
    echo json_encode(["resultado" => "0", "mensaje" => $e->getMessage()]);
}

$conn->close();
?>