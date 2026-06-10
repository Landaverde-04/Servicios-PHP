<?php
header('Content-Type: application/json; charset=utf-8');
include '../conexion.php'; // Sube un nivel: la conexión está en la raíz

// 1) Leer parámetros opcionales (filtro por nombre de enfermedad)
$enfermedad = isset($_REQUEST['enfermedad']) ? trim($_REQUEST['enfermedad']) : "";

// 2) Armar la consulta (Hacemos un JOIN para buscar por el NOMBRE de la enfermedad)
$sql = "SELECT v.IDVACUNA, v.IDENFERMEDAD, e.NOMBREENFERMEDAD, v.NOMBREVACUNA 
        FROM VACUNA v
        INNER JOIN ENFERMEDAD e ON v.IDENFERMEDAD = e.IDENFERMEDAD";

if ($enfermedad !== "") {
    $sql .= " WHERE e.NOMBREENFERMEDAD LIKE ?";
}

$sql .= " ORDER BY v.NOMBREVACUNA ASC";

// 3) Ejecutar con sentencia preparada (SEGURO, evita inyección SQL)
$stmt = $conn->prepare($sql);

if ($enfermedad !== "") {
    // Usamos "%" para que busque coincidencias parciales (ej. "Gripe" encontrará "Gripe A")
    $buscarEnfermedad = "%" . $enfermedad . "%";
    $stmt->bind_param("s", $buscarEnfermedad);
}

$stmt->execute();
$resultado = $stmt->get_result();

// 4) Recoger filas y devolver JSON (Formato Arreglo)
$filas = array();
while ($reg = $resultado->fetch_assoc()) {
    $filas[] = $reg;
}

// 5) Validación: ¿Se encontraron registros?
if (count($filas) > 0) {
    // Si hay datos, devuelve el arreglo JSON [ {...}, {...} ]
    echo json_encode($filas);
} else {
    // Si está vacío, devuelve el objeto JSON de error {"resultado":"0","mensaje":"..."}
    $respuestaError = array(
        "resultado" => "0",
        "mensaje" => "No se encontraron vacunas registradas para la enfermedad especificada."
    );
    echo json_encode($respuestaError);
}

//----------------------------------------------
//http://localhost/Servicios-PHP/turistas/ws_vacuna_listar.php
//http://localhost/Servicios-PHP/turistas/ws_vacuna_listar.php?enfermedad=Hepatitis
//----------------------------------------------
$stmt->close();
$conn->close();
?>