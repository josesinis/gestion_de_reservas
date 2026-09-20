<?php
// Descarga solo la última versión del alumno seleccionado; el historial administrativo no cambia.
require_once __DIR__ . '/../../config/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (session_status() === PHP_SESSION_NONE) session_start();
header('Cache-Control: no-store');

function errorDescargaEstudiante(string $mensaje): void
{
    $_SESSION['entregas_error'] = $mensaje;
    $contexto = $_SESSION['entregas_contexto'] ?? [];
    $_SESSION['entregas_seleccion'] = [
        'curso_id' => (string) ($contexto['curso_id'] ?? 0),
        'alumno_id' => (string) ($contexto['alumno_id'] ?? 0),
    ];
    session_write_close();
    header('Location: registrar.php#resultado-entrega', true, 303);
    exit;
}

$token = $_POST['csrf_token'] ?? null;
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' || !is_string($token)
    || !isset($_SESSION['entregas_csrf']) || !hash_equals($_SESSION['entregas_csrf'], $token)) {
    errorDescargaEstudiante('Vuelva a seleccionar curso y estudiante para descargar.');
}
$valor = $_POST['trabajo_id'] ?? null;
$trabajoId = is_string($valor) ? (int) (filter_var($valor, FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]) ?: 0) : 0;
$cursoId = (int) ($_SESSION['entregas_contexto']['curso_id'] ?? 0);
$alumnoId = (int) ($_SESSION['entregas_contexto']['alumno_id'] ?? 0);
if (!$trabajoId || !$cursoId || !$alumnoId) errorDescargaEstudiante('Seleccione un trabajo y un estudiante válidos.');

try {
    require_once __DIR__ . '/../../config/database.php';
    $ahora = (new DateTimeImmutable())->format('Y-m-d H:i:s');
    $stmt = $conexion->prepare('
        SELECT e.nombre_archivo, e.ruta_archivo
        FROM entregas e
        INNER JOIN trabajos t ON t.id = e.trabajo_id
        INNER JOIN reservas r ON r.id = t.reserva_id
        INNER JOIN alumnos a ON a.id = e.alumno_id AND a.curso_id = r.curso_id AND a.activo = 1
        WHERE t.id = ? AND r.curso_id = ? AND e.alumno_id = ? AND e.curso_id = r.curso_id
          AND ((t.fecha_inicio <= ? AND t.fecha_limite >= ?)
            OR EXISTS (SELECT 1 FROM trabajo_prorrogas tp
                WHERE tp.trabajo_id = t.id AND tp.alumno_id = a.id
                  AND tp.fecha_inicio <= ? AND tp.fecha_fin >= ?))
        ORDER BY e.fecha_hora_entrega DESC, e.id DESC
        LIMIT 1
    ');
    $stmt->bind_param('iiissss', $trabajoId, $cursoId, $alumnoId, $ahora, $ahora, $ahora, $ahora);
    $stmt->execute();
    $entrega = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$entrega) errorDescargaEstudiante('No hay una entrega disponible para este estudiante dentro del plazo vigente.');

    $raiz = dirname(__DIR__, 2);
    $base = realpath($raiz . '/uploads/entregas');
    $relativa = str_replace('\\', '/', $entrega['ruta_archivo']);
    if (strpos($relativa, 'uploads/entregas/') !== 0 || strpos($relativa, '..') !== false
        || strpos($relativa, "\0") !== false) errorDescargaEstudiante('La ubicación del archivo no es válida.');
    $ruta = realpath($raiz . '/' . $relativa);
    if ($base === false || $ruta === false || !is_file($ruta)
        || strpos(str_replace('\\', '/', $ruta), rtrim(str_replace('\\', '/', $base), '/') . '/') !== 0) {
        errorDescargaEstudiante('El archivo de la última entrega no está disponible en el servidor.');
    }
    $archivo = fopen($ruta, 'rb');
    if ($archivo === false) errorDescargaEstudiante('No fue posible abrir el archivo de la entrega.');
    $datos = fstat($archivo);
    $nombre = basename(str_replace('\\', '/', $entrega['nombre_archivo']));
    $nombre = preg_replace('/[\x00-\x1F\x7F]/', '', $nombre);
    session_write_close();
    header('Content-Type: application/octet-stream');
    header('X-Content-Type-Options: nosniff');
    header("Content-Disposition: attachment; filename=\"entrega\"; filename*=UTF-8''" . rawurlencode($nombre));
    if ($datos !== false) header('Content-Length: ' . $datos['size']);
    fpassthru($archivo);
    fclose($archivo);
    exit;
} catch (Throwable $e) {
    error_log('Descarga del estudiante: ' . $e->getMessage());
    errorDescargaEstudiante('No fue posible descargar el archivo. Intente nuevamente.');
}
