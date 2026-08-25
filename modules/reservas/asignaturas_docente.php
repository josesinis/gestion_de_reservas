<?php

declare(strict_types=1);

//=====================================================
// ASIGNATURAS POR DOCENTE
// Devuelve las asignaturas que imparte un docente.
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// VALIDAR DOCENTE
//=====================================================

$docenteId = isset($_GET['docente_id'])
    ? (int) $_GET['docente_id']
    : 0;

header('Content-Type: application/json; charset=utf-8');

if ($docenteId <= 0) {

    echo json_encode([]);

    exit();
}

//=====================================================
// OBTENER ASIGNATURAS
//=====================================================

$asignaturas = obtenerAsignaturasPorDocente(
    $conexion,
    $docenteId
);

//=====================================================
// RESPUESTA
//=====================================================

echo json_encode(
    $asignaturas,
    JSON_UNESCAPED_UNICODE
);
