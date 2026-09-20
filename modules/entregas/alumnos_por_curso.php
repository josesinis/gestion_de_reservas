<?php

// ============================================================
// SEGURIDAD
// ============================================================

require_once '../../includes/auth.php';
requiereLogin();

require_once '../../includes/permisos.php';
require_once '../../config/database.php';

requiereRol(['admin', 'superadmin']);


// ============================================================
// RESPUESTA JSON
// ============================================================

header('Content-Type: application/json; charset=utf-8');


// ============================================================
// OBTENER ID DEL CURSO
// ============================================================

$cursoId = isset($_GET['curso_id'])
    ? (int) $_GET['curso_id']
    : 0;


if ($cursoId <= 0) {

    http_response_code(400);

    echo json_encode([
        'error' => 'Curso no válido'
    ], JSON_UNESCAPED_UNICODE);

    exit();
}


// ============================================================
// BUSCAR ESTUDIANTES ACTIVOS DEL CURSO
// ============================================================

$sql = "
    SELECT
        id,
        nombres,
        apellidos
    FROM alumnos
    WHERE curso_id = ?
      AND activo = 1
    ORDER BY apellidos, nombres
";


$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $cursoId);

$stmt->execute();

$resultado = $stmt->get_result();


// ============================================================
// CONSTRUIR RESPUESTA
// ============================================================

$alumnos = [];

while ($alumno = $resultado->fetch_assoc()) {

    $alumnos[] = [
        'id' => (int) $alumno['id'],
        'nombres' => $alumno['nombres'],
        'apellidos' => $alumno['apellidos']
    ];

}


$stmt->close();


// ============================================================
// DEVOLVER RESULTADO
// ============================================================

echo json_encode(
    $alumnos,
    JSON_UNESCAPED_UNICODE
);
