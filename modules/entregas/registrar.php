<?php
// Interfaz pública del estudiante: consulta y subida de entregas.
// Descarga de la última entrega mediante un endpoint separado del administrativo.
require_once __DIR__ . '/../../config/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['entregas_csrf'])) {
    $_SESSION['entregas_csrf'] = bin2hex(random_bytes(32));
}

header('Cache-Control: no-store');

function entregaHtml($valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function entregaId($valor): int
{
    if (!is_string($valor)) {
        return 0;
    }
    return (int) (filter_var($valor, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1]
    ]) ?: 0);
}

function entregaFecha($valor): ?DateTimeImmutable
{
    if (!is_string($valor) || $valor === '') {
        return null;
    }
    $fecha = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $valor);
    return $fecha && $fecha->format('Y-m-d H:i:s') === $valor ? $fecha : null;
}

function entregaSituacion(array $trabajo, DateTimeImmutable $ahora): array
{
    $inicio = entregaFecha($trabajo['fecha_inicio']);
    $limite = entregaFecha($trabajo['fecha_limite']);
    $finProrroga = entregaFecha($trabajo['prorroga_fin']);

    if ($inicio && $limite && $inicio <= $ahora && $ahora <= $limite) {
        return ['estado-activo', 'Disponible por plazo normal'];
    }
    // La consulta solo devuelve prórrogas vigentes de este estudiante.
    if ($finProrroga && $ahora <= $finProrroga) {
        return ['estado-activo', 'Disponible por prórroga vigente hasta ' . $finProrroga->format('d/m/Y H:i')];
    }
    if (!$inicio || !$limite || $inicio > $limite) {
        return ['estado-inactivo', 'No disponible: fechas pendientes de revisión'];
    }
    if ($ahora < $inicio) {
        return ['estado-inactivo', 'Plazo aún no iniciado. Disponible desde ' . $inicio->format('d/m/Y H:i')];
    }
    return ['estado-inactivo', 'Plazo finalizado'];
}

// POST mantiene la selección fuera de la URL. El antiguo parámetro ?id se ignora.
$seleccion = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST'
    ? $_POST : ($_SESSION['entregas_seleccion'] ?? []);
$cursoId = entregaId($seleccion['curso_id'] ?? null);
$alumnoId = entregaId($seleccion['alumno_id'] ?? null);
$cursos = [];
$alumnos = [];
$trabajos = [];
$cursoSeleccionado = null;
$alumnoSeleccionado = null;
$error = $_SESSION['entregas_error'] ?? '';
$consultaFallida = false;
$exito = $_SESSION['entregas_exito'] ?? '';
unset($_SESSION['entregas_error'], $_SESSION['entregas_exito'], $_SESSION['entregas_seleccion']);
$ahora = new DateTimeImmutable();
$ahoraSql = $ahora->format('Y-m-d H:i:s');

try {
    require_once __DIR__ . '/../../config/database.php';

    $cursos = $conexion->query('SELECT id, nombre_curso FROM cursos ORDER BY nombre_curso')->fetch_all(MYSQLI_ASSOC);
    foreach ($cursos as $curso) {
        if ((int) $curso['id'] === $cursoId) {
            $cursoSeleccionado = $curso;
            break;
        }
    }

    if ($cursoId && !$cursoSeleccionado) {
        $error = 'Seleccione un curso válido.';
    }

    if ($cursoSeleccionado) {
        $stmt = $conexion->prepare('
            SELECT id, nombres, apellidos
            FROM alumnos
            WHERE curso_id = ? AND activo = 1
            ORDER BY apellidos, nombres, id
        ');
        $stmt->bind_param('i', $cursoId);
        $stmt->execute();
        $alumnos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        // Validación del lado servidor: alumno activo y perteneciente al curso.
        foreach ($alumnos as $alumno) {
            if ((int) $alumno['id'] === $alumnoId) {
                $alumnoSeleccionado = $alumno;
                break;
            }
        }
        if ($alumnoId && !$alumnoSeleccionado) {
            $error = 'Seleccione un estudiante activo del curso indicado.';
        }
    }

    if ($cursoSeleccionado && $alumnoSeleccionado) {
        $_SESSION['entregas_contexto'] = ['curso_id' => $cursoId, 'alumno_id' => $alumnoId];
        // Se usa el mismo instante y zona horaria para plazo normal y prórrogas.
        // MAX evita duplicar trabajos si existen varias prórrogas vigentes.
        $stmt = $conexion->prepare('
            SELECT t.id, t.titulo, t.fecha_inicio, t.fecha_limite,
                   a.asignatura_nombre,
                   (SELECT COUNT(*) FROM entregas e
                    WHERE e.trabajo_id = t.id AND e.alumno_id = ?) AS cantidad_entregas,
                   (
                       SELECT MAX(tp.fecha_fin)
                       FROM trabajo_prorrogas tp
                       WHERE tp.trabajo_id = t.id AND tp.alumno_id = ?
                         AND tp.fecha_inicio <= ? AND tp.fecha_fin >= ?
                   ) AS prorroga_fin
            FROM trabajos t
            INNER JOIN reservas r ON r.id = t.reserva_id
            LEFT JOIN asignaturas a ON a.id = r.asignatura_id
            WHERE r.curso_id = ?
            ORDER BY t.fecha_limite ASC, t.titulo ASC, t.id ASC
        ');
        $stmt->bind_param('iissi', $alumnoId, $alumnoId, $ahoraSql, $ahoraSql, $cursoId);
        $stmt->execute();
        $trabajos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
} catch (mysqli_sql_exception $e) {
    $consultaFallida = true;
    error_log('Consulta de entregas del estudiante: ' . $e->getMessage());
    http_response_code(503);
    $error = 'No fue posible cargar la información. Intente nuevamente más tarde.';
    $trabajos = [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrega de trabajos</title>
    <link rel="stylesheet" href="../../assets/css/estilos.css">
    <link rel="stylesheet" href="../../assets/css/botones.css">
    <link rel="stylesheet" href="../../assets/css/formularios.css">
    <link rel="stylesheet" href="../../assets/css/tablas.css">
    <style>
        /* Ajustes exclusivos de esta pantalla; conserva los estilos del proyecto. */
        .entregas-pagina { width: 96%; max-width: 1600px; padding-bottom: 32px; }
        .entregas-pagina .contenedor-formulario { width: 100%; max-width: none; margin: 24px 0; }
        .entregas-seleccion { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 24px; }
        .entregas-seleccion form { min-width: 0; display: grid; grid-template-rows: 1fr auto; gap: 16px; }
        .entregas-seleccion .grupo-formulario { justify-content: flex-start; }
        .entregas-seleccion small { margin-top: 6px; }
        .entregas-seleccion .botones { margin: 0; }
        .entregas-pagina .tabla { min-width: 0; width: 100%; table-layout: fixed; }
        .entregas-pagina .tabla th, .entregas-pagina .tabla td {
            white-space: normal; overflow-wrap: anywhere; vertical-align: top; padding: 18px 16px;
        }
        .entregas-pagina .tabla th:nth-child(1) { width: 16%; }
        .entregas-pagina .tabla th:nth-child(2) { width: 16%; }
        .entregas-pagina .tabla th:nth-child(3) { width: 16%; }
        .entregas-pagina .tabla th:nth-child(4) { width: 22%; }
        .entregas-pagina .tabla th:nth-child(5) { width: 30%; }
        .entregas-pagina .tabla td p { margin: 0 0 14px; }
        .entregas-pagina .tabla .btn { width: 100%; white-space: normal; min-height: 42px; }
        .entregas-pagina .tabla .btn-secundario { margin-top: 12px; }
        .entregas-subida { display: grid; gap: 14px; min-width: 0; margin: 0; }
        .entregas-subida input[type="file"] { min-width: 0; max-width: 100%; }
        .entregas-aviso { padding: 18px 22px; margin: 24px 0; border: 1px solid;
            border-radius: var(--radio-borde); scroll-margin-top: 24px; overflow-wrap: anywhere; }
        .entregas-aviso p { margin: 6px 0 0; }
        .entregas-aviso-exito { color: #175c30; background: #eaf7ee; border-color: #8ac69c; }
        .entregas-aviso-error { color: #842029; background: #fff0f1; border-color: #e0a1a7; }
        @media (max-width: 900px) {
            .entregas-seleccion { grid-template-columns: 1fr; }
            .entregas-pagina .tabla thead { position: absolute; width: 1px; height: 1px;
                overflow: hidden; clip-path: inset(50%); }
            .entregas-pagina .tabla, .entregas-pagina .tabla tbody,
            .entregas-pagina .tabla tr, .entregas-pagina .tabla td { display: block; width: 100%; box-sizing: border-box; }
            .entregas-pagina .tabla tr + tr { border-top: 3px solid var(--color-primario); }
            .entregas-pagina .tabla td { border-top: 0; padding: 12px 16px; }
            .entregas-pagina .tabla td::before { content: attr(data-label); display: block;
                font-weight: 700; margin-bottom: 8px; color: var(--color-primario); }
        }
    </style>
</head>
<body>
    <header class="menu-principal">
        <div class="menu-contenedor">
            <div class="menu-identidad">
                <span class="menu-logo"><span class="menu-logo-texto">Gestión Institucional</span></span>
            </div>
        </div>
    </header>

    <main class="contenedor entregas-pagina">
        <h1>Entrega de trabajos</h1>
        <p>Seleccione su curso y su nombre para consultar los trabajos y sus plazos.</p>

        <?php if ($error !== ''): ?>
            <div id="resultado-entrega" class="entregas-aviso entregas-aviso-error" role="alert" tabindex="-1">
                <strong>No se pudo completar la operación</strong>
                <p><?= entregaHtml($error) ?></p>
            </div>
        <?php endif; ?>
        <?php if ($exito !== ''): ?>
            <div id="<?= $error !== '' ? 'confirmacion-entrega' : 'resultado-entrega' ?>" class="entregas-aviso entregas-aviso-exito" role="status" tabindex="-1">
                <strong>¡Archivo subido correctamente!</strong>
                <p><?= entregaHtml($exito) ?></p>
            </div>
        <?php endif; ?>

        <section class="contenedor-formulario" aria-labelledby="titulo-seleccion">
            <h2 id="titulo-seleccion">Curso y estudiante</h2>

            <!-- Un formulario independiente descarta siempre el alumno al cambiar de curso. -->
            <div class="entregas-seleccion">
            <form id="formCurso" action="registrar.php" method="post">
                <div class="grupo-formulario">
                    <label for="curso_id">Curso</label>
                    <select id="curso_id" name="curso_id" required aria-describedby="ayuda-curso">
                        <option value="">Seleccione su curso</option>
                        <?php foreach ($cursos as $curso): ?>
                            <option value="<?= (int) $curso['id'] ?>" <?= (int) $curso['id'] === $cursoId ? 'selected' : '' ?>><?= entregaHtml($curso['nombre_curso']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small id="ayuda-curso">Al cambiar de curso se actualiza la lista de estudiantes.</small>
                </div>
                <div class="botones">
                    <button type="submit" class="btn btn-secundario">Cargar estudiantes</button>
                </div>
            </form>

            <form id="formEstudiante" action="registrar.php" method="post">
                <input type="hidden" name="curso_id" value="<?= $cursoSeleccionado ? (int) $cursoSeleccionado['id'] : 0 ?>">
                <div class="grupo-formulario">
                    <label for="alumno_id">Estudiante</label>
                    <select id="alumno_id" name="alumno_id" required <?= !$alumnos ? 'disabled' : '' ?>>
                        <option value=""><?= !$cursoSeleccionado ? 'Primero seleccione un curso' : (!$alumnos ? 'No hay estudiantes activos en este curso' : 'Seleccione su nombre') ?></option>
                        <?php foreach ($alumnos as $alumno): ?>
                            <option value="<?= (int) $alumno['id'] ?>" <?= (int) $alumno['id'] === $alumnoId ? 'selected' : '' ?>><?= entregaHtml($alumno['apellidos'] . ', ' . $alumno['nombres']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="botones">
                    <button type="submit" id="btnConsultar" class="btn btn-primario" <?= !$alumnos ? 'disabled' : '' ?>>Ver trabajos</button>
                </div>
            </form>
            </div>
        </section>

        <?php if ($cursoSeleccionado && $alumnoSeleccionado && !$consultaFallida): ?>
            <section id="trabajos" aria-labelledby="titulo-trabajos">
                <h2 id="titulo-trabajos">Trabajos del curso <?= entregaHtml($cursoSeleccionado['nombre_curso']) ?></h2>
                <p>Estudiante: <strong><?= entregaHtml($alumnoSeleccionado['apellidos'] . ', ' . $alumnoSeleccionado['nombres']) ?></strong></p>
                <p id="aviso-acciones">Puede subir archivos de hasta 20 MB, sujeto al límite del servidor, y descargar su última entrega durante el plazo vigente. Las versiones anteriores se conservan para administración.</p>
                <?php if (!$trabajos): ?>
                    <p>No hay trabajos asignados a este curso. El docente debe registrar una actividad antes de que pueda realizar su primera entrega.</p>
                <?php else: ?>
                    <div class="tabla-contenedor">
                        <table class="tabla">
                            <thead>
                                <tr>
                                    <th scope="col">Trabajo</th>
                                    <th scope="col">Asignatura</th>
                                    <th scope="col">Fecha límite original</th>
                                    <th scope="col">Situación para el estudiante</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($trabajos as $trabajo): ?>
                                    <?php
                                    [$clase, $situacion] = entregaSituacion($trabajo, $ahora);
                                    $limite = entregaFecha($trabajo['fecha_limite']);
                                    $puedeSubir = $clase === 'estado-activo';
                                    $tieneEntregas = (int) $trabajo['cantidad_entregas'] > 0;
                                    ?>
                                    <tr>
                                        <td data-label="Trabajo"><?= entregaHtml($trabajo['titulo']) ?></td>
                                        <td data-label="Asignatura"><?= entregaHtml($trabajo['asignatura_nombre'] ?? 'Sin asignatura') ?></td>
                                        <td data-label="Fecha límite original"><?= $limite ? entregaHtml($limite->format('d/m/Y H:i')) : 'Sin fecha válida' ?></td>
                                        <td data-label="Situación para el estudiante"><span class="<?= entregaHtml($clase) ?>"><?= entregaHtml($situacion) ?></span></td>
                                        <td data-label="Acciones">
                                            <p><?= $tieneEntregas ? 'Ya tiene entregas registradas.' : 'Sin entregas: esta será su primera subida.' ?></p>
                                            <?php if ($puedeSubir): ?>
                                                <form class="entregas-subida" action="guardar.php" method="post" enctype="multipart/form-data">
                                                    <input type="hidden" name="csrf_token" value="<?= entregaHtml($_SESSION['entregas_csrf']) ?>">
                                                    <input type="hidden" name="trabajo_id" value="<?= (int) $trabajo['id'] ?>">
                                                    <input type="hidden" name="curso_id" value="<?= $cursoId ?>">
                                                    <input type="hidden" name="alumno_id" value="<?= $alumnoId ?>">
                                                    <div class="grupo-formulario">
                                                        <label for="archivo_<?= (int) $trabajo['id'] ?>">Archivo para <?= entregaHtml($trabajo['titulo']) ?></label>
                                                        <input type="file" id="archivo_<?= (int) $trabajo['id'] ?>" name="archivo" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primario"><?= $tieneEntregas ? 'Subir nueva entrega' : 'Subir primera entrega' ?></button>
                                                </form>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-primario" disabled>Subir archivo</button>
                                            <?php endif; ?>
                                            <?php if ($tieneEntregas && $puedeSubir): ?>
                                                <form action="descargar_estudiante.php" method="post">
                                                    <input type="hidden" name="csrf_token" value="<?= entregaHtml($_SESSION['entregas_csrf']) ?>">
                                                    <input type="hidden" name="trabajo_id" value="<?= (int) $trabajo['id'] ?>">
                                                    <button type="submit" class="btn btn-secundario">Descargar última entrega</button>
                                                </form>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-secundario" disabled><?= $tieneEntregas ? 'Descarga fuera de plazo' : 'Sin archivo para descargar' ?></button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>
    <script>
        const resultadoEntrega = document.getElementById('resultado-entrega');
        if (resultadoEntrega) {
            resultadoEntrega.focus({ preventScroll: true });
            resultadoEntrega.scrollIntoView({ block: 'start' });
        }
        document.querySelectorAll('.entregas-subida').forEach(function (form) {
            form.addEventListener('submit', function () {
                const boton = form.querySelector('button[type="submit"]');
                boton.disabled = true;
                boton.textContent = 'Subiendo archivo…';
                form.setAttribute('aria-busy', 'true');
            });
        });
        window.addEventListener('pageshow', function () {
            document.querySelectorAll('.entregas-subida button[type="submit"]').forEach(function (boton) {
                if (boton.dataset.textoOriginal) boton.textContent = boton.dataset.textoOriginal;
                else boton.dataset.textoOriginal = boton.textContent;
                boton.disabled = false;
                boton.form.removeAttribute('aria-busy');
            });
        });
        const curso = document.getElementById('curso_id');
        const estudiante = document.getElementById('alumno_id');

        curso.addEventListener('change', function () {
            estudiante.disabled = true;
            document.getElementById('btnConsultar').disabled = true;
            const trabajos = document.getElementById('trabajos');
            if (trabajos) trabajos.hidden = true;
            document.getElementById('formCurso').submit();
        });

        estudiante.addEventListener('change', function () {
            const trabajos = document.getElementById('trabajos');
            if (trabajos) trabajos.hidden = true;
            if (estudiante.value !== '') {
                document.getElementById('formEstudiante').requestSubmit();
            }
        });
    </script>
</body>
</html>
