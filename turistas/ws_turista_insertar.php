<?php
header('Content-Type: application/json; charset=utf-8');

// CONFIGURACIÓN CRÍTICA: Desactiva el lanzamiento automático de excepciones en MySQLi
mysqli_report(MYSQLI_REPORT_OFF);

include '../conexion.php'; 

// 1) Leer parámetros
$idturista       = isset($_REQUEST['idTurista']) ? trim($_REQUEST['idTurista']) : "";
$idpais          = isset($_REQUEST['codPais']) ? trim($_REQUEST['codPais']) : null;
$nombres         = isset($_REQUEST['nombres']) ? trim($_REQUEST['nombres']) : "";
$apellidos       = isset($_REQUEST['apellidos']) ? trim($_REQUEST['apellidos']) : "";
$fechanacimiento = isset($_REQUEST['natalicio(AAAA-MM-DD)']) ? trim($_REQUEST['natalicio(AAAA-MM-DD)']) : "";
$telefono        = isset($_REQUEST['telefono']) ? trim($_REQUEST['telefono']) : null;
$correo          = isset($_REQUEST['correo']) ? trim($_REQUEST['correo']) : null;

// --- PROCESAMIENTO DE LA VISA ---
$visa_input      = isset($_REQUEST['tieneVisa']) ? strtolower(trim($_REQUEST['tieneVisa'])) : "no";
$tienevisa       = ($visa_input === "si") ? 1 : 0; 

// Validar campos obligatorios
if ($idturista === "" || $nombres === "" || $apellidos === "" || $fechanacimiento === "") {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Faltan campos obligatorios (idTurista, nombres, apellidos o natalicio)."
    ]);
    $conn->close();
    exit;
}

// 2) Armar la consulta
$sql = "INSERT INTO TURISTA (IDTURISTA, IDPAIS, NOMBRES, APELLIDOS, FECHANACIMIENTO, TELEFONO, CORREO, TIENEVISA) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

// 3) Preparar la sentencia
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("sssssssi", $idturista, $idpais, $nombres, $apellidos, $fechanacimiento, $telefono, $correo, $tienevisa);
    
    // 4) Ejecutar la inserción
    if ($stmt->execute()) {
        echo json_encode([
            "resultado" => "1",
            "mensaje" => "Turista registrado exitosamente."
        ]);
    } else {
        // Al quitar el if/else, mandamos el error tal cual lo genera MySQL crudo
        echo json_encode([
            "resultado" => "0",
            "mensaje" => "Error al insertar el registro: " . $stmt->error
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode([
        "resultado" => "0",
        "mensaje" => "Error al preparar la consulta: " . $conn->error
    ]);
}
//-----------------------------------------------------
//http://localhost/Servicios-PHP/turistas/ws_turista_insertar.php?idTurista=T06&codPais=SLV&nombres=Elena&apellidos=Rivas&natalicio(AAAA-MM-DD)=1997-11-25&telefono=7706-1122&correo=elena@mail.com&tieneVisa=si
//-------------------------------------------------------
$conn->close();
?>