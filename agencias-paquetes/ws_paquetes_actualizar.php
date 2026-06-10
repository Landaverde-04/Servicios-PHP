<?php
// ===========================================================================
// SERVICIO: ACTUALIZAR PAQUETES TURISTICOS DISPONIBLES
// ----------------------------------------------------------------------------
// Flujo: MySQL servidor -> PHP -> Android -> SQLite.
// Devuelve paquetes activos, vigentes y con cupos, junto con agencia,
// destinos, paises y motivaciones en columnas agregadas.
//
// Prueba en navegador:
//   http://localhost/Servicios-PHP/agencias-paquetes/ws_paquetes_actualizar.php
// ===========================================================================

header('Content-Type: application/json; charset=utf-8');
include '../conexion.php';

$sql = "SELECT P.IDPAQUETE,
               P.IDAGENCIA,
               A.NOMBREAGENCIA,
               A.TELEFONO AS TELEFONOAGENCIA,
               A.CORREO AS CORREOAGENCIA,
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
               GROUP_CONCAT(DISTINCT CONCAT(D.IDDESTINO, ':', D.NOMBREDESTINO, ':', IFNULL(PA.NOMBREPAIS, ''))
                            ORDER BY D.NOMBREDESTINO SEPARATOR '|') AS DESTINOS,
               GROUP_CONCAT(DISTINCT CONCAT(M.IDMOTIVACION, ':', M.DESMOTIVACION)
                            ORDER BY M.DESMOTIVACION SEPARATOR '|') AS MOTIVACIONES
        FROM PAQUETE_TURISTICO P
        LEFT JOIN AGENCIA_VIAJE A      ON P.IDAGENCIA = A.IDAGENCIA
        LEFT JOIN INSCRIPCION I        ON P.IDPAQUETE = I.IDPAQUETE
        LEFT JOIN PAQUETE_DESTINO PD   ON P.IDPAQUETE = PD.IDPAQUETE
        LEFT JOIN DESTINO D            ON PD.IDDESTINO = D.IDDESTINO
        LEFT JOIN PAIS PA              ON D.IDPAIS = PA.IDPAIS
        LEFT JOIN PAQUETE_MOTIVACION PM ON P.IDPAQUETE = PM.IDPAQUETE
        LEFT JOIN MOTIVACIONES M       ON PM.IDMOTIVACION = M.IDMOTIVACION
        WHERE LOWER(P.ESTADO) = 'activo'
          AND P.FECHAFIN >= CURDATE()
        GROUP BY P.IDPAQUETE, P.IDAGENCIA, A.NOMBREAGENCIA, A.TELEFONO, A.CORREO,
                 P.NOMBREPAQUETE, P.DESPAQUETE, P.FECHAINICIO, P.FECHAFIN,
                 P.CUPOMAXIMO, P.ESTADO
        HAVING CUPOSDISPONIBLES > 0
        ORDER BY P.FECHAINICIO ASC, P.NOMBREPAQUETE ASC";

$resultado = $conn->query($sql);

$filas = array();
while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}

echo json_encode($filas);

$conn->close();
?>
