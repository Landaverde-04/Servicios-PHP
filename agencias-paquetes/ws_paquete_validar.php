<?php
// ===========================================================================
// SERVICIO: VALIDACION OPERATIVA DE PAQUETE TURISTICO
// ----------------------------------------------------------------------------
// Flujo: Android -> PHP -> MySQL -> Android.
// Valida existencia, estado, fechas, cupos, agencia, destinos y requisitos.
//
// Parametros:
//   idPaquete : obligatorio. Ej. PQ01
//   idTurista : opcional. Si se envia, valida visa y vacunas del turista.
//
// Pruebas en navegador:
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_paquete_validar.php?idPaquete=PQ01
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_paquete_validar.php?idPaquete=PQ01&idTurista=T01
// ===========================================================================

header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$idPaquete = isset($_REQUEST['idPaquete']) ? trim($_REQUEST['idPaquete']) : "";
$idTurista = isset($_REQUEST['idTurista']) ? trim($_REQUEST['idTurista']) : "";

if ($idPaquete === "") {
    echo json_encode(["resultado" => "0", "mensaje" => "Falta parametro obligatorio: idPaquete"]);
    $conn->close();
    exit;
}

$sql = "SELECT P.IDPAQUETE,
               P.IDAGENCIA,
               A.NOMBREAGENCIA,
               P.NOMBREPAQUETE,
               P.DESPAQUETE,
               P.FECHAINICIO,
               P.FECHAFIN,
               P.CUPOMAXIMO,
               P.ESTADO,
               COUNT(DISTINCT CASE
                   WHEN I.IDINSCRIPCION IS NOT NULL AND LOWER(I.ESTADO) <> 'cancelado'
                   THEN I.IDINSCRIPCION
               END) AS CUPOSOCUPADOS,
               (P.CUPOMAXIMO - COUNT(DISTINCT CASE
                   WHEN I.IDINSCRIPCION IS NOT NULL AND LOWER(I.ESTADO) <> 'cancelado'
                   THEN I.IDINSCRIPCION
               END)) AS CUPOSDISPONIBLES,
               COUNT(DISTINCT PD.IDDESTINO) AS CANTIDADDESTINOS,
               MAX(IFNULL(D.REQUIEREVISAESP, 0)) AS REQUIEREVISA,
               COUNT(DISTINCT RVD.IDVACUNA) AS CANTIDADVACUNASREQUERIDAS,
               GROUP_CONCAT(DISTINCT CONCAT(D.IDDESTINO, ':', D.NOMBREDESTINO)
                            ORDER BY D.NOMBREDESTINO SEPARATOR '|') AS DESTINOS,
               GROUP_CONCAT(DISTINCT CONCAT(V.IDVACUNA, ':', V.NOMBREVACUNA)
                            ORDER BY V.NOMBREVACUNA SEPARATOR '|') AS VACUNASREQUERIDAS
        FROM PAQUETE_TURISTICO P
        LEFT JOIN AGENCIA_VIAJE A            ON P.IDAGENCIA = A.IDAGENCIA
        LEFT JOIN INSCRIPCION I              ON P.IDPAQUETE = I.IDPAQUETE
        LEFT JOIN PAQUETE_DESTINO PD         ON P.IDPAQUETE = PD.IDPAQUETE
        LEFT JOIN DESTINO D                  ON PD.IDDESTINO = D.IDDESTINO
        LEFT JOIN REQUISITO_VACUNA_DESTINO RVD ON D.IDDESTINO = RVD.IDDESTINO
        LEFT JOIN VACUNA V                   ON RVD.IDVACUNA = V.IDVACUNA
        WHERE P.IDPAQUETE = ?
        GROUP BY P.IDPAQUETE, P.IDAGENCIA, A.NOMBREAGENCIA, P.NOMBREPAQUETE,
                 P.DESPAQUETE, P.FECHAINICIO, P.FECHAFIN, P.CUPOMAXIMO, P.ESTADO";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $idPaquete);
$stmt->execute();
$resultado = $stmt->get_result();
$paquete = $resultado->fetch_assoc();
$stmt->close();

if (!$paquete) {
    echo json_encode([
        "resultado" => "0",
        "valido" => "0",
        "mensaje" => "El paquete turistico no existe en el servidor"
    ]);
    $conn->close();
    exit;
}

$valido = true;
$mensajes = array();
$advertencias = array();

if (strtolower($paquete["ESTADO"]) !== "activo") {
    $valido = false;
    $mensajes[] = "El paquete no esta activo";
}
if ($paquete["FECHAINICIO"] < date("Y-m-d")) {
    $valido = false;
    $mensajes[] = "El paquete ya inicio";
}
if ($paquete["FECHAFIN"] < date("Y-m-d")) {
    $valido = false;
    $mensajes[] = "El paquete ya finalizo";
}
if ((int)$paquete["CUPOSDISPONIBLES"] <= 0) {
    $valido = false;
    $mensajes[] = "El paquete no tiene cupos disponibles";
}
if ($paquete["IDAGENCIA"] === null || $paquete["NOMBREAGENCIA"] === null) {
    $valido = false;
    $mensajes[] = "El paquete no tiene agencia asociada";
}
if ((int)$paquete["CANTIDADDESTINOS"] <= 0) {
    $valido = false;
    $mensajes[] = "El paquete no tiene destinos asociados";
}

if ((int)$paquete["REQUIEREVISA"] === 1) {
    $advertencias[] = "El paquete incluye destinos que requieren visa";
}
if ((int)$paquete["CANTIDADVACUNASREQUERIDAS"] > 0) {
    $advertencias[] = "El paquete tiene vacunas requeridas";
}

if ($idTurista !== "") {
    $stmtTurista = $conn->prepare("SELECT IDTURISTA, TIENEVISA FROM TURISTA WHERE IDTURISTA = ?");
    $stmtTurista->bind_param("s", $idTurista);
    $stmtTurista->execute();
    $turista = $stmtTurista->get_result()->fetch_assoc();
    $stmtTurista->close();

    if (!$turista) {
        $valido = false;
        $mensajes[] = "El turista no existe en el servidor";
    } else {
        if ((int)$paquete["REQUIEREVISA"] === 1 && (int)$turista["TIENEVISA"] === 0) {
            $valido = false;
            $mensajes[] = "El turista no tiene visa y el paquete la requiere";
        }

        $sqlVacunas = "SELECT DISTINCT V.IDVACUNA, V.NOMBREVACUNA
                       FROM PAQUETE_DESTINO PD
                       INNER JOIN REQUISITO_VACUNA_DESTINO RVD ON PD.IDDESTINO = RVD.IDDESTINO
                       INNER JOIN VACUNA V ON RVD.IDVACUNA = V.IDVACUNA
                       WHERE PD.IDPAQUETE = ?
                         AND NOT EXISTS (
                             SELECT 1
                             FROM VACUNA_TURISTA VT
                             WHERE VT.IDTURISTA = ?
                               AND VT.IDVACUNA = RVD.IDVACUNA
                               AND VT.FECHAVIGENCIA >= ?
                         )
                       ORDER BY V.NOMBREVACUNA";
        $stmtVacunas = $conn->prepare($sqlVacunas);
        $stmtVacunas->bind_param("sss", $idPaquete, $idTurista, $paquete["FECHAINICIO"]);
        $stmtVacunas->execute();
        $resVacunas = $stmtVacunas->get_result();

        $vacunasFaltantes = array();
        while ($vacuna = $resVacunas->fetch_assoc()) {
            $vacunasFaltantes[] = $vacuna;
        }
        $stmtVacunas->close();

        if (count($vacunasFaltantes) > 0) {
            $valido = false;
            $mensajes[] = "El turista no tiene todas las vacunas requeridas vigentes";
            $paquete["VACUNASFALTANTES"] = $vacunasFaltantes;
        }
    }
}

echo json_encode([
    "resultado" => "1",
    "valido" => $valido ? "1" : "0",
    "mensaje" => $valido ? "Paquete disponible para el cliente" : implode(". ", $mensajes),
    "advertencias" => $advertencias,
    "paquete" => $paquete
]);

$conn->close();
?>
