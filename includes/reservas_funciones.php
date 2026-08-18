<?php

declare(strict_types=1);

//=====================================================
// CONFIGURACIÓN DE RESERVAS
//=====================================================

// Tiempo permitido para realizar una reserva
// después del inicio del bloque o subbloque.
const MINUTOS_GRACIA_RESERVA = 10;

/**
 * =====================================================
 * MÓDULO RESERVAS
 * Funciones del módulo de reservas.
 * =====================================================
 */

/**
 * Obtiene los días de la semana de trabajo
 * (lunes a viernes).
 *
 * @param string|null $fechaReferencia Fecha en formato Y-m-d.
 * @return array
 */
function obtenerDiasSemana(?string $fechaReferencia = null): array
{
    if ($fechaReferencia === null) {
        $fechaReferencia = date('Y-m-d');
    }

    $fecha = new DateTime($fechaReferencia);

    // Si es sábado o domingo, comenzar desde el lunes siguiente.
    if ($fecha->format('N') >= 6) {
        $fecha->modify('next monday');
    } else {
        $fecha->modify('monday this week');
    }

    $diasSemana = [];

    $nombresDias = [
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes'
    ];

    for ($i = 0; $i < 5; $i++) {

        $diasSemana[] = [

            'nombre' => $nombresDias[$i],

            'fecha' => $fecha->format('Y-m-d'),

            'fecha_corta' => $fecha->format('d/m'),

            'fecha_larga' => $fecha->format('d/m/Y')

        ];

        $fecha->modify('+1 day');
    }

    return $diasSemana;
}


/**
 * Obtiene todos los bloques configurados.
 *
 * @param mysqli $conexion
 * @return array
 */
function obtenerBloques(mysqli $conexion): array
{
    $sql = "
        SELECT
            id,
            numero_bloque,
            hora_inicio,
            hora_termino
        FROM bloques
        ORDER BY numero_bloque
    ";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        return [];
    }

    return $resultado->fetch_all(MYSQLI_ASSOC);
}

//=====================================================
// OBTENER DOCENTES
//=====================================================

function obtenerDocentes(mysqli $conexion): array
{
    $sql = "
        SELECT
            id,
            CONCAT(nombres, ' ', apellidos) AS nombre
        FROM docentes
        ORDER BY apellidos, nombres
    ";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        return [];
    }

    return $resultado->fetch_all(MYSQLI_ASSOC);
}


//=====================================================
// OBTENER CURSOS
//=====================================================

function obtenerCursos(mysqli $conexion): array
{
    $sql = "
        SELECT
            id,
            nombre_curso
        FROM cursos
        ORDER BY nombre_curso
    ";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        return [];
    }

    return $resultado->fetch_all(MYSQLI_ASSOC);
}


//=====================================================
// OBTENER ASIGNATURAS
//=====================================================

function obtenerAsignaturas(mysqli $conexion): array
{
    $sql = "
        SELECT
            id,
            asignatura_nombre
        FROM asignaturas
        ORDER BY asignatura_nombre
    ";

    $resultado = $conexion->query($sql);

    if (!$resultado) {
        return [];
    }

    return $resultado->fetch_all(MYSQLI_ASSOC);
}

//=====================================================
// OBTENER RESERVA POR ID
//=====================================================

function obtenerReservaPorId(
    mysqli $conexion,
    int $id
): ?array {
    $sql = "

        SELECT

            r.*,

            c.nombre_curso,

            a.asignatura_nombre,

            b.numero_bloque,
            b.hora_inicio,
            b.hora_termino,

            d.nombres,
            d.apellidos

        FROM reservas r

        INNER JOIN cursos c
            ON c.id = r.curso_id

        INNER JOIN asignaturas a
            ON a.id = r.asignatura_id

        INNER JOIN bloques b
            ON b.id = r.bloque_id

        INNER JOIN docentes d
            ON d.id = r.docente_id

        WHERE r.id = ?

        LIMIT 1

    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $resultado = $stmt->get_result();

    $reserva = $resultado->fetch_assoc();

    $stmt->close();

    return $reserva ?: null;
}


/**
 * Obtiene las reservas comprendidas entre dos fechas.
 *
 * @param mysqli $conexion
 * @param string $fechaInicio
 * @param string $fechaFin
 * @return array
 */
function obtenerReservas(
    mysqli $conexion,
    string $fechaInicio,
    string $fechaFin
): array {
    $reservas = [];

    $sql = "
        SELECT

            r.id,
            r.fecha,
            r.bloque_id,
            r.tipo_reserva,
            r.actividad,
            r.permite_entrega,
            r.fecha_entrega_oficial,
            r.cierre_manual,
            r.estado,

        CONCAT(SUBSTRING_INDEX(d.nombres,' ',1), ' ', SUBSTRING_INDEX(d.apellidos,' ',1) ) AS docente,

            c.nombre_curso AS curso,

            a.asignatura_nombre AS asignatura

        FROM reservas r

        INNER JOIN docentes d
            ON d.id = r.docente_id

        INNER JOIN cursos c
            ON c.id = r.curso_id

        INNER JOIN asignaturas a
            ON a.id = r.asignatura_id

        WHERE

            r.fecha BETWEEN ? AND ?



        ORDER BY

            r.fecha,
            r.bloque_id,
            r.tipo_reserva
    ";


    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        'ss',
        $fechaInicio,
        $fechaFin
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {

        $reservas[$fila['fecha']][(int)$fila['bloque_id']][$fila['tipo_reserva']] = $fila;
    }

    return $reservas;
}

/**
 * =====================================================
 * Obtiene las reservas de una celda de la agenda.
 *
 * @param array  $reservas
 * @param string $fecha
 * @param int    $bloqueId
 *
 * @return array
 * =====================================================
 */
function obtenerReservaCelda(
    array $reservas,
    string $fecha,
    int $bloqueId
): array {
    return $reservas[$fecha][$bloqueId] ?? [];
}


/**
 * Formatea una fecha YYYY-MM-DD a:
 * Lunes 08 de junio de 2026
 */
function formatearFechaLarga(string $fecha): string
{
    $dias = [
        'Sunday'    => 'Domingo',
        'Monday'    => 'Lunes',
        'Tuesday'   => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday'  => 'Jueves',
        'Friday'    => 'Viernes',
        'Saturday'  => 'Sábado'
    ];

    $meses = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    $timestamp = strtotime($fecha);

    return sprintf(
        '%s %02d de %s de %d',
        $dias[date('l', $timestamp)],
        date('d', $timestamp),
        $meses[(int)date('n', $timestamp)],
        date('Y', $timestamp)
    );
}


//=====================================================
// FORMATEAR RANGO DE FECHAS DE SEMANA
//=====================================================

function formatearRangoSemana(
    string $fechaInicio,
    string $fechaFin
): string {

    $meses = [
        1  => 'enero',
        2  => 'febrero',
        3  => 'marzo',
        4  => 'abril',
        5  => 'mayo',
        6  => 'junio',
        7  => 'julio',
        8  => 'agosto',
        9  => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    $inicio = new DateTime($fechaInicio);
    $fin = new DateTime($fechaFin);

    $diaInicio = $inicio->format('d');
    $diaFin = $fin->format('d');

    $mesInicio = $meses[(int)$inicio->format('n')];
    $mesFin = $meses[(int)$fin->format('n')];

    $anioInicio = $inicio->format('Y');
    $anioFin = $fin->format('Y');

    // Misma fecha
    if ($fechaInicio === $fechaFin) {
        return "{$diaInicio} de {$mesInicio} de {$anioInicio}";
    }

    // Mismo mes y mismo año
    if (
        $inicio->format('n') === $fin->format('n') &&
        $anioInicio === $anioFin
    ) {
        return "{$diaInicio} al {$diaFin} de {$mesInicio} de {$anioInicio}";
    }

    // Mes diferente, mismo año
    if ($anioInicio === $anioFin) {
        return "{$diaInicio} de {$mesInicio} al {$diaFin} de {$mesFin} de {$anioInicio}";
    }

    // Año diferente
    return "{$diaInicio} de {$mesInicio} de {$anioInicio} al {$diaFin} de {$mesFin} de {$anioFin}";
}

//=====================================================
// OBTENER BLOQUE
//=====================================================

function obtenerBloque(
    mysqli $conexion,
    int $bloqueId
): ?array {
    $sql = "
        SELECT
            id,
            numero_bloque,
            hora_inicio,
            hora_termino
        FROM bloques
        WHERE id = ?
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "i",
        $bloqueId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $bloque = $resultado->fetch_assoc();

    $stmt->close();

    return $bloque ?: null;
}


//=====================================================
// OBTENER HORARIO RESERVA
//=====================================================

function obtenerHorarioReserva(
    array $bloque,
    string $tipoReserva
): string {
    $horaInicio = substr($bloque['hora_inicio'], 0, 5);

    $horaTermino = substr($bloque['hora_termino'], 0, 5);

    $inicio = strtotime($bloque['hora_inicio']);

    $termino = strtotime($bloque['hora_termino']);

    $horaMedia = date(
        'H:i',
        $inicio + (($termino - $inicio) / 2)
    );

    switch ($tipoReserva) {

        case 'sub1':
            return $horaInicio . ' - ' . $horaMedia;

        case 'sub2':
            return $horaMedia . ' - ' . $horaTermino;

        default:
            return $horaInicio . ' - ' . $horaTermino;
    }
}


//=====================================================
// OBTENER OPCIONES DE RESERVA
//=====================================================

function obtenerOpcionesTipoReserva(
    mysqli $conexion,
    string $fecha,
    int $bloqueId,
    array $bloque
): array {
    $tiposReservados = obtenerTiposReservados(
        $conexion,
        $fecha,
        $bloqueId
    );

    $opciones = [];

    // Si existe una reserva completa,
    // no debería llamarse al formulario.
    if ($tiposReservados['completo']) {
        return $opciones;
    }

    // Si el bloque está completamente libre
    if (
        !$tiposReservados['sub1'] &&
        !$tiposReservados['sub2']
    ) {

        $opciones[] = [
            'tipo' => 'completo',
            'texto' => 'Bloque completo',
            'horario' => obtenerHorarioReserva($bloque, 'completo')
        ];

        $opciones[] = [
            'tipo' => 'sub1',
            'texto' => 'Primer bloque (45 min)',
            'horario' => obtenerHorarioReserva($bloque, 'sub1')
        ];

        $opciones[] = [
            'tipo' => 'sub2',
            'texto' => 'Segundo bloque (45 min)',
            'horario' => obtenerHorarioReserva($bloque, 'sub2')
        ];

        return $opciones;
    }

    // Solo Sub2 disponible
    if (
        $tiposReservados['sub1'] &&
        !$tiposReservados['sub2']
    ) {

        $opciones[] = [
            'tipo' => 'sub2',
            'texto' => 'Segundo bloque (45 min)',
            'horario' => obtenerHorarioReserva($bloque, 'sub2')
        ];
    }

    // Solo Sub1 disponible
    if (
        !$tiposReservados['sub1'] &&
        $tiposReservados['sub2']
    ) {

        $opciones[] = [
            'tipo' => 'sub1',
            'texto' => 'Primer bloque (45 min)',
            'horario' => obtenerHorarioReserva($bloque, 'sub1')
        ];
    }

    return $opciones;
}


//=====================================================
// VALIDAR SI UNA RESERVA PUEDE SER MODIFICADA
//=====================================================

function reservaEsModificable(array $reserva): bool
{
    $ahora = new DateTime();

    $finReserva = new DateTime(
        $reserva['fecha'] . ' ' . $reserva['hora_termino']
    );

    // La reserva ya terminó
    if ($ahora >= $finReserva) {
        return false;
    }

    return true;
}


//=====================================================
// VALIDAR SI UNA RESERVA PUEDE SER CREADA
//=====================================================

function reservaPuedeCrearse(
    string $fecha,
    array $bloque,
    string $tipoReserva
): bool {

    $hoy = new DateTime('today');
    $fechaReserva = new DateTime($fecha);

    // No se permiten fechas anteriores a hoy.
    if ($fechaReserva < $hoy) {
        return false;
    }

    // Obtener inicio del bloque.
    $inicio = new DateTime(
        $fecha . ' ' . $bloque['hora_inicio']
    );

    // Para sub2, el inicio efectivo es la mitad del bloque.
    if ($tipoReserva === 'sub2') {

        $inicioBloque = strtotime($bloque['hora_inicio']);
        $terminoBloque = strtotime($bloque['hora_termino']);

        $horaMedia = date(
            'H:i:s',
            $inicioBloque + (($terminoBloque - $inicioBloque) / 2)
        );

        $inicio = new DateTime(
            $fecha . ' ' . $horaMedia
        );
    }

    // Si el horario ya comenzó, no se puede crear.
    if (new DateTime() >= $inicio) {
        return false;
    }

    return true;
}

//=====================================================
// VALIDAR SI UN HORARIO PUEDE SER RESERVADO
//=====================================================

function horarioPuedeReservarse(
    string $fecha,
    array $bloque,
    string $tipoReserva
): bool {

    $ahora = new DateTime();

    $inicioBloque = new DateTime(
        $fecha . ' ' . $bloque['hora_inicio']
    );

    $terminoBloque = new DateTime(
        $fecha . ' ' . $bloque['hora_termino']
    );

    // Calcular duración del bloque
    $duracion = $terminoBloque->getTimestamp()
        - $inicioBloque->getTimestamp();

    // Calcular mitad del bloque
    $horaMedia = clone $inicioBloque;

    $horaMedia->modify(
        '+' . ($duracion / 2) . ' seconds'
    );

    // Determinar inicio efectivo de la reserva
    switch ($tipoReserva) {

        case 'sub2':
            $inicioReserva = $horaMedia;
            break;

        case 'sub1':
        case 'completo':
        default:
            $inicioReserva = $inicioBloque;
            break;
    }

    // Agregar el período de gracia
    $limiteReserva = clone $inicioReserva;

    $limiteReserva->modify(
        '+' . MINUTOS_GRACIA_RESERVA . ' minutes'
    );

    // Se puede reservar hasta el límite de gracia.
    return $ahora <= $limiteReserva;
}

//=====================================================
// FORMATEAR TIPO DE RESERVA
//=====================================================

function formatearTipoReserva(string $tipo): string
{
    return match ($tipo) {

        'completo' => 'Bloque completo',

        'sub1' => 'Primer bloque (45 min)',

        'sub2' => 'Segundo bloque (45 min)',

        default => $tipo
    };
}


//=====================================================
// VERIFICAR SI EXISTE UNA RESERVA
//=====================================================

function existeReserva(
    mysqli $conexion,
    int $id
): bool {
    $sql = "
        SELECT 1
        FROM reservas
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $stmt->store_result();

    $existe = $stmt->num_rows > 0;

    $stmt->close();

    return $existe;
}

//=====================================================
// VALIDAR DATOS DE UNA RESERVA
//=====================================================

function validarReserva(
    int $docenteId,
    int $cursoId,
    int $asignaturaId,
    string $actividad
): array {
    $errores = [];

    if ($docenteId <= 0) {
        $errores[] = 'Debe seleccionar un docente.';
    }

    if ($cursoId <= 0) {
        $errores[] = 'Debe seleccionar un curso.';
    }

    if ($asignaturaId <= 0) {
        $errores[] = 'Debe seleccionar una asignatura.';
    }

    if (mb_strlen($actividad) > 150) {
        $errores[] = 'La actividad no puede superar los 150 caracteres.';
    }

    return $errores;
}


//=====================================================
// PUEDE MODIFICAR UNA RESERVA
//=====================================================

function puedeModificarReserva(string $fecha): bool
{
    return $fecha >= date('Y-m-d');
}


//=====================================================
// OBTENER TIPOS DE RESERVA OCUPADOS
//=====================================================

function obtenerTiposReservados(
    mysqli $conexion,
    string $fecha,
    int $bloqueId
): array {
    $tipos = [
        'completo' => null,
        'sub1'     => null,
        'sub2'     => null
    ];

    $sql = "

        SELECT

    r.*,

    c.nombre_curso AS curso,

    a.asignatura_nombre AS asignatura,

    CONCAT(d.apellidos, ', ', d.nombres) AS docente,

    b.numero_bloque,
    b.hora_inicio,
    b.hora_termino

FROM reservas r

INNER JOIN cursos c
    ON c.id = r.curso_id

INNER JOIN asignaturas a
    ON a.id = r.asignatura_id

INNER JOIN bloques b
    ON b.id = r.bloque_id

INNER JOIN docentes d
    ON d.id = r.docente_id

WHERE r.fecha = ?
  AND r.bloque_id = ?
  AND r.estado = 'reservada'

    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "si",
        $fecha,
        $bloqueId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {

        $tipos[$fila['tipo_reserva']] = $fila;
    }

    $stmt->close();

    return $tipos;
}

//=====================================================
// VALIDAR CONFLICTO DE RESERVA
//=====================================================

function hayConflictoReserva(
    mysqli $conexion,
    string $fecha,
    int $bloqueId,
    string $tipoReserva
): bool {

    $sql = "
        SELECT tipo_reserva
        FROM reservas
        WHERE fecha = ?
          AND bloque_id = ?
          AND estado = 'reservada'
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return true;
    }

    $stmt->bind_param(
        "si",
        $fecha,
        $bloqueId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {

        $tipoExistente = $fila['tipo_reserva'];

        // Una reserva completa ocupa todo el bloque.
        if ($tipoReserva === 'completo') {
            $stmt->close();
            return true;
        }

        // Una reserva de sub1 entra en conflicto
        // con otra sub1 o con una reserva completa.
        if (
            $tipoReserva === 'sub1' &&
            in_array($tipoExistente, ['sub1', 'completo'], true)
        ) {
            $stmt->close();
            return true;
        }

        // Una reserva de sub2 entra en conflicto
        // con otra sub2 o con una reserva completa.
        if (
            $tipoReserva === 'sub2' &&
            in_array($tipoExistente, ['sub2', 'completo'], true)
        ) {
            $stmt->close();
            return true;
        }
    }

    $stmt->close();

    return false;
}

//=====================================================
// GENERAR TARJETA DE RESERVA
//=====================================================

function renderizarTarjetaReserva(array $reserva): string
{
    ob_start();
?>

    <a
        href="ver.php?id=<?= (int)$reserva['id']; ?>"
        class="agenda-reserva">

        <div class="agenda-reserva-asignatura">
            <?= htmlspecialchars($reserva['asignatura']); ?>
        </div>

        <div class="agenda-reserva-info">

            <?= htmlspecialchars($reserva['curso']); ?>

            <span>•</span>

            <?= htmlspecialchars($reserva['docente']); ?>

        </div>

    </a>

<?php

    return ob_get_clean();
}
