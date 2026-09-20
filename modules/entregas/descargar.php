<?php

// ============================================================
// DESCARGAR ENTREGA
// ============================================================

require_once '../../includes/auth.php';
requiereLogin();

require_once '../../includes/permisos.php';
require_once '../../config/database.php';

requiereRol(['admin', 'superadmin']);


// ============================================================
// 1. OBTENER ID DE LA ENTREGA
// ============================================================

$entregaId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($entregaId <= 0) {
    $_SESSION['error'] = 'Entrega no válida.';
    header('Location: index.php');
    exit();
}


// ============================================================
// 2. OBTENER DATOS DE LA ENTREGA Y DEL TRABAJO
// ============================================================

$sql = "
    SELECT
        e.id,
        e.trabajo_id,
        e.alumno_id,
        e.nombre_archivo,
        e.ruta_archivo,

        t.titulo,
        t.estado,
        t.fecha_inicio,
        t.fecha_limite

    FROM entregas e

    INNER JOIN trabajos t
        ON t.id = e.trabajo_id

    WHERE e.id = ?
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $entregaId);
$stmt->execute();

$resultado = $stmt->get_result();
$entrega = $resultado->fetch_assoc();

$stmt->close();

if (!$entrega) {
    $_SESSION['error'] = 'La entrega no existe.';
    header('Location: index.php');
    exit();
}


// ============================================================
// 3. COMPROBAR SI ESTÁ DENTRO DEL PLAZO ORIGINAL
// ============================================================

$ahora = date('Y-m-d H:i:s');

$accesoPermitido = (
    $ahora >= $entrega['fecha_inicio']
    &&
    $ahora <= $entrega['fecha_limite']
);


// ============================================================
// 4. SI TERMINÓ EL PLAZO ORIGINAL, REVISAR PRÓRROGA
// ============================================================

if (!$accesoPermitido) {

    $sqlProrroga = "
        SELECT id
        FROM trabajo_prorrogas
        WHERE trabajo_id = ?
          AND alumno_id = ?
          AND fecha_inicio <= ?
          AND fecha_fin >= ?
        LIMIT 1
    ";

    $stmtProrroga = $conexion->prepare($sqlProrroga);

    $stmtProrroga->bind_param(
        "iiss",
        $entrega['trabajo_id'],
        $entrega['alumno_id'],
        $ahora,
        $ahora
    );

    $stmtProrroga->execute();

    $resultadoProrroga = $stmtProrroga->get_result();

    if ($resultadoProrroga->fetch_assoc()) {
        $accesoPermitido = true;
    }

    $stmtProrroga->close();
}


// ============================================================
// 5. DENEGAR DESCARGA SI NO HAY PLAZO VIGENTE
// ============================================================

if (!$accesoPermitido) {
    $_SESSION['error'] = 'La descarga no está disponible. El plazo de este trabajo ha finalizado y no existe una prórroga vigente para este estudiante.';

    header('Location: index.php');
    exit();
}


// ============================================================
// 6. CONSTRUIR RUTA FÍSICA DEL ARCHIVO
// ============================================================

$rutaRelativa = $entrega['ruta_archivo'];

// Normalizar separadores
$rutaRelativa = str_replace('\\', '/', $rutaRelativa);

// Evitar rutas absolutas o intentos de salir del directorio
if (
    str_starts_with($rutaRelativa, '/') ||
    str_contains($rutaRelativa, '..')
) {
    $_SESSION['error'] = 'La ruta del archivo no es válida.';
    header('Location: index.php');
    exit();
}

$rutaBase = realpath('../../');

if ($rutaBase === false) {
    $_SESSION['error'] = 'No se pudo determinar la carpeta del sistema.';
    header('Location: index.php');
    exit();
}

$rutaArchivo = realpath('../../' . $rutaRelativa);


// ============================================================
// 7. COMPROBAR QUE EL ARCHIVO EXISTE
// ============================================================

if (
    $rutaArchivo === false ||
    !is_file($rutaArchivo)
) {
    $_SESSION['error'] = 'El archivo de la entrega no existe en el servidor.';
    header('Location: index.php');
    exit();
}


// ============================================================
// 8. COMPROBAR QUE EL ARCHIVO ESTÁ DENTRO DEL SISTEMA
// ============================================================

$rutaBaseNormalizada = rtrim(str_replace('\\', '/', $rutaBase), '/') . '/';
$rutaArchivoNormalizada = str_replace('\\', '/', $rutaArchivo);

if (!str_starts_with($rutaArchivoNormalizada, $rutaBaseNormalizada)) {
    $_SESSION['error'] = 'La ubicación del archivo no es válida.';
    header('Location: index.php');
    exit();
}


// ============================================================
// 9. DETERMINAR TIPO MIME
// ============================================================

$tipoMime = 'application/octet-stream';

if (function_exists('finfo_open')) {

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if ($finfo !== false) {
        $mimeDetectado = finfo_file($finfo, $rutaArchivo);

        if ($mimeDetectado !== false) {
            $tipoMime = $mimeDetectado;
        }

        finfo_close($finfo);
    }
}


// ============================================================
// 10. ENVIAR ARCHIVO AL NAVEGADOR
// ============================================================

$nombreDescarga = basename($entrega['nombre_archivo']);

header('Content-Description: File Transfer');
header('Content-Type: ' . $tipoMime);
header('Content-Disposition: attachment; filename="' . addslashes($nombreDescarga) . '"');
header('Content-Length: ' . filesize($rutaArchivo));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: public');

readfile($rutaArchivo);

exit();
