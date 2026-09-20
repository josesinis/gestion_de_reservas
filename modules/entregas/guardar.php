<?php
// Recibe una entrega inicial o una nueva versión. No requiere cuenta administrativa.
require_once __DIR__ . '/../../config/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Cache-Control: no-store');

function volverEntrega(string $mensaje, bool $exito = false): void
{
    $_SESSION[$exito ? 'entregas_exito' : 'entregas_error'] = $mensaje;
    session_write_close();
    header('Location: registrar.php#resultado-entrega', true, 303);
    exit;
}

function idEntregaPublica($valor): int
{
    return is_string($valor)
        ? (int) (filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 0)
        : 0;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Location: registrar.php', true, 303);
    exit;
}

// PHP vacía POST y FILES si se supera post_max_size.
if (empty($_POST) && empty($_FILES)) {
    volverEntrega('No se recibió el archivo. Puede superar el tamaño permitido por el servidor.');
}

$token = $_POST['csrf_token'] ?? null;
if (!is_string($token) || !isset($_SESSION['entregas_csrf'])
    || !hash_equals($_SESSION['entregas_csrf'], $token)) {
    volverEntrega('La sesión del formulario venció. Seleccione nuevamente curso y estudiante.');
}

$trabajoId = idEntregaPublica($_POST['trabajo_id'] ?? null);
$cursoId = idEntregaPublica($_POST['curso_id'] ?? null);
$alumnoId = idEntregaPublica($_POST['alumno_id'] ?? null);
$_SESSION['entregas_seleccion'] = [
    'curso_id' => (string) $cursoId,
    'alumno_id' => (string) $alumnoId,
];
if (!$trabajoId || !$cursoId || !$alumnoId) {
    volverEntrega('Seleccione un trabajo, curso y estudiante válidos.');
}

$archivo = $_FILES['archivo'] ?? null;
if (!is_array($archivo) || !isset($archivo['error']) || !is_int($archivo['error'])) {
    volverEntrega('Seleccione un archivo para entregar.');
}
if ($archivo['error'] !== UPLOAD_ERR_OK) {
    $mensajes = [
        UPLOAD_ERR_INI_SIZE => 'El archivo supera el tamaño permitido por el servidor.',
        UPLOAD_ERR_FORM_SIZE => 'El formulario enviado impuso un límite de tamaño. Recargue la página actualizada y vuelva a seleccionar el archivo.',
        UPLOAD_ERR_PARTIAL => 'El archivo llegó incompleto. Intente nuevamente.',
        UPLOAD_ERR_NO_FILE => 'Seleccione un archivo para entregar.',
    ];
    volverEntrega($mensajes[$archivo['error']] ?? 'No fue posible recibir el archivo. Intente nuevamente.');
}
if (!isset($archivo['tmp_name'], $archivo['name'])
    || !is_string($archivo['tmp_name']) || !is_string($archivo['name'])
    || !is_uploaded_file($archivo['tmp_name'])) {
    volverEntrega('El archivo recibido no es válido.');
}
$tamano = filesize($archivo['tmp_name']);
if ($tamano === false || $tamano <= 0 || $tamano > 20 * 1024 * 1024) {
    volverEntrega('El archivo debe contener datos y no superar 20 MB.');
}
$nombreOriginal = trim(basename(str_replace('\\', '/', $archivo['name'])));
if ($nombreOriginal === '' || $nombreOriginal === '.' || $nombreOriginal === '..'
    || strlen($nombreOriginal) > 240 || preg_match('/[\x00-\x1F\x7F]/', $nombreOriginal)
    || !preg_match('//u', $nombreOriginal)) {
    volverEntrega('El nombre del archivo no es válido o es demasiado largo. Renómbrelo e intente nuevamente.');
}

$rutaFisica = null;
$guardado = false;
try {
    require_once __DIR__ . '/../../config/database.php';
    // La actividad y el alumno deben pertenecer al mismo curso. No se exige una entrega previa.
    $stmt = $conexion->prepare('
        SELECT t.id, r.asignatura_id
        FROM trabajos t
        INNER JOIN reservas r ON r.id = t.reserva_id
        INNER JOIN alumnos a ON a.id = ? AND a.curso_id = r.curso_id AND a.activo = 1
        WHERE t.id = ? AND r.curso_id = ?
        LIMIT 1
    ');
    $stmt->bind_param('iii', $alumnoId, $trabajoId, $cursoId);
    $stmt->execute();
    $trabajo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$trabajo) {
        volverEntrega('La actividad no corresponde al curso o el estudiante no está activo en ese curso.');
    }

    $directorio = dirname(__DIR__, 2) . '/uploads/entregas';
    if (!is_dir($directorio) && !mkdir($directorio, 0755, true) && !is_dir($directorio)) {
        throw new RuntimeException('No se pudo crear el directorio de entregas.');
    }
    // El nombre original se conserva en la BD. La extensión física fija evita ejecutar
    // código subido (por ejemplo, un ejercicio PHP) desde el servidor web.
    $nombreFisico = bin2hex(random_bytes(24)) . '.bin';
    $rutaFisica = $directorio . '/' . $nombreFisico;
    $rutaArchivo = 'uploads/entregas/' . $nombreFisico;
    if (!move_uploaded_file($archivo['tmp_name'], $rutaFisica)) {
        throw new RuntimeException('No se pudo almacenar el archivo recibido.');
    }

    // Validación final al insertar: evita aceptar una entrega si cambió el curso,
    // el estado del alumno o el plazo durante la recepción del archivo.
    $ahora = (new DateTimeImmutable())->format('Y-m-d H:i:s');
    $stmt = $conexion->prepare('
        INSERT INTO entregas
            (trabajo_id, curso_id, asignatura_id, alumno_id, nombre_archivo, ruta_archivo, fecha_hora_entrega)
        SELECT t.id, r.curso_id, r.asignatura_id, a.id, ?, ?, ?
        FROM trabajos t
        INNER JOIN reservas r ON r.id = t.reserva_id
        INNER JOIN alumnos a ON a.id = ? AND a.curso_id = r.curso_id AND a.activo = 1
        WHERE t.id = ? AND r.curso_id = ?
          AND (
              (t.fecha_inicio <= ? AND t.fecha_limite >= ?)
              OR EXISTS (
                  SELECT 1 FROM trabajo_prorrogas tp
                  WHERE tp.trabajo_id = t.id AND tp.alumno_id = a.id
                    AND tp.fecha_inicio <= ? AND tp.fecha_fin >= ?
              )
          )
    ');
    $stmt->bind_param('sssiiissss', $nombreOriginal, $rutaArchivo, $ahora,
        $alumnoId, $trabajoId, $cursoId, $ahora, $ahora, $ahora, $ahora);
    $stmt->execute();
    $guardado = $stmt->affected_rows === 1;
    $stmt->close();
    if (!$guardado) {
        if (!unlink($rutaFisica)) {
            error_log('No se pudo retirar una entrega rechazada: ' . $rutaFisica);
        }
        volverEntrega('No se pudo aceptar la entrega: el plazo no está vigente o cambió la asignación del estudiante.');
    }
} catch (Throwable $e) {
    if (!$guardado && $rutaFisica !== null && is_file($rutaFisica)) {
        if (!unlink($rutaFisica)) {
            error_log('No se pudo retirar una entrega fallida: ' . $rutaFisica);
        }
    }
    error_log('Guardar entrega del estudiante: ' . $e->getMessage());
    volverEntrega('No fue posible guardar la entrega. Intente nuevamente más tarde.');
}

volverEntrega('Se guardó el archivo «' . $nombreOriginal . '». Puede consultar sus trabajos o subir otra entrega.', true);
