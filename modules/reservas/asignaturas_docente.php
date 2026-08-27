<?php

declare(strict_types=1);

//=====================================================
// ASIGNATURAS POR DOCENTE
// Devuelve las asignaturas que imparte un docente,
// filtradas según la modalidad.
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// RECIBIR DATOS
//=====================================================

$docenteId = isset($_GET['docente_id'])
    ? (int) $_GET['docente_id']
    : 0;

$modalidad = trim(
    $_GET['modalidad'] ?? 'asignatura'
);

header(
    'Content-Type: application/json; charset=utf-8'
);

//=====================================================
// VALIDAR DOCENTE
//=====================================================

if ($docenteId <= 0) {

    echo json_encode([]);

    exit();
}

//=====================================================
// VALIDAR MODALIDAD
//=====================================================

$modalidadesPermitidas = [
    'asignatura',
    'taller'
];

if (!in_array(
    $modalidad,
    $modalidadesPermitidas,
    true
)) {

    echo json_encode([]);

    exit();
}

//=====================================================
// OBTENER ASIGNATURAS
//=====================================================

$asignaturas = obtenerAsignaturasPorDocente(
    $conexion,
    $docenteId,
    $modalidad
);

//=====================================================
// RESPUESTA
//=====================================================

echo json_encode(
    $asignaturas,
    JSON_UNESCAPED_UNICODE
);
