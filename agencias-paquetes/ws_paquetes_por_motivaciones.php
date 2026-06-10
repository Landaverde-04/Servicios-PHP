<?php
// ===========================================================================
// SERVICIO: BUSQUEDA DE PAQUETES POR MOTIVACIONES
// ----------------------------------------------------------------------------
// Flujo: Android selecciona intereses -> PHP -> MySQL -> Android.
// Recibe una o varias motivaciones y devuelve paquetes activos/vigentes
// ordenados por cantidad de coincidencias.
//
// Parametros:
//   motivaciones : obligatorio. IDs o textos separados por coma.
//                  Ej. M01,M05 o playa,cultura
//
// Pruebas en navegador:
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_paquetes_por_motivaciones.php?motivaciones=M01,M05
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_paquetes_por_motivaciones.php?motivaciones=playa,cultura
// ===========================================================================

header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

function bind_parametros($stmt, $tipos, &$valores) {
    $refs = array();
    $refs[] = $tipos;
    foreach ($valores as $key => $value) {
        $refs[] = &$valores[$key];
    }
    call_user_func_array(array($stmt, 'bind_param'), $refs);
}

$entrada = isset($_REQUEST['motivaciones']) ? trim($_REQUEST['motivaciones']) : "";

if ($entrada === "") {
    echo json_encode(["resultado" => "0", "mensaje" => "Falta parametro obligatorio: motivaciones"]);
    $conn->close();
    exit;
}

$tokens = array();
foreach (explode(",", $entrada) as $token) {
    $token = trim($token);
    if ($token !== "") {
        $tokens[] = $token;
    }
}

if (count($tokens) === 0) {
    echo json_encode(["resultado" => "0", "mensaje" => "Debe enviar al menos una motivacion valida"]);
    $conn->close();
    exit;
}

$condiciones = array();
$tipos = "";
$valores = array();

foreach ($tokens as $token) {
    $condiciones[] = "(MM.IDMOTIVACION = ? OR MM.DESMOTIVACION LIKE ?)";
    $tipos .= "ss";
    $valores[] = $token;
    $valores[] = "%" . $token . "%";
}

$whereMotivaciones = implode(" OR ", $condiciones);

$sql = "SELECT P.IDPAQUETE,
               P.IDAGENCIA,
               A.NOMBREAGENCIA,
               P.NOMBREPAQUETE,
               P.DESPAQUETE,
               P.FECHAINICIO,
               P.FECHAFIN,
               P.CUPOMAXIMO,
               P.ESTADO,
               COUNT(DISTINCT MM.IDMOTIVACION) AS COINCIDENCIAS,
               COUNT(DISTINCT CASE
                   WHEN I.IDINSCRIPCION IS NOT NULL AND LOWER(I.ESTADO) <> 'cancelado'
                   THEN I.IDINSCRIPCION
               END) AS CUPOSOCUPADOS,
               (P.CUPOMAXIMO - COUNT(DISTINCT CASE
                   WHEN I.IDINSCRIPCION IS NOT NULL AND LOWER(I.ESTADO) <> 'cancelado'
                   THEN I.IDINSCRIPCION
               END)) AS CUPOSDISPONIBLES,
               GROUP_CONCAT(DISTINCT CONCAT(MALL.IDMOTIVACION, ':', MALL.DESMOTIVACION)
                            ORDER BY MALL.DESMOTIVACION SEPARATOR '|') AS MOTIVACIONES,
               GROUP_CONCAT(DISTINCT CONCAT(D.IDDESTINO, ':', D.NOMBREDESTINO)
                            ORDER BY D.NOMBREDESTINO SEPARATOR '|') AS DESTINOS
        FROM PAQUETE_TURISTICO P
        INNER JOIN PAQUETE_MOTIVACION PMM ON P.IDPAQUETE = PMM.IDPAQUETE
        INNER JOIN MOTIVACIONES MM        ON PMM.IDMOTIVACION = MM.IDMOTIVACION
        LEFT JOIN AGENCIA_VIAJE A         ON P.IDAGENCIA = A.IDAGENCIA
        LEFT JOIN INSCRIPCION I           ON P.IDPAQUETE = I.IDPAQUETE
        LEFT JOIN PAQUETE_MOTIVACION PMALL ON P.IDPAQUETE = PMALL.IDPAQUETE
        LEFT JOIN MOTIVACIONES MALL       ON PMALL.IDMOTIVACION = MALL.IDMOTIVACION
        LEFT JOIN PAQUETE_DESTINO PD      ON P.IDPAQUETE = PD.IDPAQUETE
        LEFT JOIN DESTINO D               ON PD.IDDESTINO = D.IDDESTINO
        WHERE LOWER(P.ESTADO) = 'activo'
          AND P.FECHAFIN >= CURDATE()
          AND ($whereMotivaciones)
        GROUP BY P.IDPAQUETE, P.IDAGENCIA, A.NOMBREAGENCIA, P.NOMBREPAQUETE,
                 P.DESPAQUETE, P.FECHAINICIO, P.FECHAFIN, P.CUPOMAXIMO, P.ESTADO
        HAVING CUPOSDISPONIBLES > 0
        ORDER BY COINCIDENCIAS DESC, P.FECHAINICIO ASC, P.NOMBREPAQUETE ASC";

$stmt = $conn->prepare($sql);
bind_parametros($stmt, $tipos, $valores);
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
