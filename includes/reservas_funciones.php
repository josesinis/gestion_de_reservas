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

///=====================================================
// OBTENER ASIGNATURAS DE UN DOCENTE
//=====================================================

function obtenerAsignaturasPorDocente(
    mysqli $conexion,
    int $docenteId,
    string $modalidad = 'asignatura'
): array {

    $sql = "
        SELECT
            a.id,
            a.asignatura_nombre
        FROM docentes_asignaturas da

        INNER JOIN asignaturas a
            ON a.id = da.asignatura_id

        WHERE
            da.docente_id = ?
            AND a.modalidad = ?

        ORDER BY a.asignatura_nombre
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param(
        "is",
        $docenteId,
        $modalidad
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $asignaturas = $resultado->fetch_all(
        MYSQLI_ASSOC
    );

    $stmt->close();

    return $asignaturas;
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

//=====================================================
// VALIDAR SI UNA RESERVA PUEDE SER CONFIRMADA
//=====================================================

function reservaPuedeConfirmarse(array $reserva): bool
{
    $ahora = new DateTime();

    $inicioBloque = new DateTime(
        $reserva['fecha'] . ' ' . $reserva['hora_inicio']
    );

    // Para sub2, el inicio efectivo es la mitad del bloque.
    if ($reserva['tipo_reserva'] === 'sub2') {

        $terminoBloque = new DateTime(
            $reserva['fecha'] . ' ' . $reserva['hora_termino']
        );

        $duracion = $terminoBloque->getTimestamp()
            - $inicioBloque->getTimestamp();

        $inicioReserva = clone $inicioBloque;

        $inicioReserva->modify(
            '+' . ($duracion / 2) . ' seconds'
        );
    } else {

        $inicioReserva = $inicioBloque;
    }

    return $ahora >= $inicioReserva;
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

            AND r.estado <> 'cancelada'

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


//=====================================================
// OBTENER HORARIOS FIJOS
//
// Obtiene la planificación fija vigente entre
// dos fechas.
//=====================================================

function obtenerHorariosFijos(
    mysqli $conexion,
    string $fechaInicio,
    string $fechaFin
): array {

    $horarios = [];

    $sql = "
        SELECT

            hf.id,
            hf.dia_semana,
            hf.bloque_id,
            hf.tipo,
            hf.modalidad,

            hf.docente_id,
            hf.curso_id,
            hf.asignatura_id,

            hf.activo,
            hf.fecha_inicio,
            hf.fecha_fin,

            c.nombre_curso AS curso,

            a.asignatura_nombre AS asignatura,

            CONCAT(
                SUBSTRING_INDEX(d.nombres, ' ', 1),
                ' ',
                SUBSTRING_INDEX(d.apellidos, ' ', 1)
            ) AS docente,

            b.numero_bloque,
            b.hora_inicio,
            b.hora_termino

        FROM horarios_fijos hf

        INNER JOIN docentes d
            ON d.id = hf.docente_id

        INNER JOIN cursos c
            ON c.id = hf.curso_id

        INNER JOIN asignaturas a
            ON a.id = hf.asignatura_id

        INNER JOIN bloques b
            ON b.id = hf.bloque_id

        WHERE hf.activo = 1

          AND hf.fecha_inicio <= ?

          AND (
                hf.fecha_fin IS NULL
                OR hf.fecha_fin >= ?
              )

        ORDER BY
            hf.dia_semana,
            hf.bloque_id,
            hf.tipo
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param(
        "ss",
        $fechaFin,
        $fechaInicio
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {

        $horarios[] = $fila;
    }

    $stmt->close();

    return $horarios;
}


//=====================================================
// OBTENER HORARIO FIJO POR ID
//
// Obtiene un horario fijo específico para su edición
// o consulta.
//=====================================================

function obtenerHorarioFijoPorId(
    mysqli $conexion,
    int $horarioFijoId
): ?array {

    $sql = "
        SELECT

            hf.id,
            hf.dia_semana,
            hf.bloque_id,
            hf.tipo,
            hf.modalidad,

            hf.docente_id,
            hf.curso_id,
            hf.asignatura_id,

            hf.activo,
            hf.fecha_inicio,
            hf.fecha_fin,
            hf.observaciones,

            c.nombre_curso AS curso,

            a.asignatura_nombre AS asignatura,

            CONCAT(
                SUBSTRING_INDEX(d.nombres, ' ', 1),
                ' ',
                SUBSTRING_INDEX(d.apellidos, ' ', 1)
            ) AS docente,

            b.numero_bloque,
            b.hora_inicio,
            b.hora_termino

        FROM horarios_fijos hf

        INNER JOIN docentes d
            ON d.id = hf.docente_id

        INNER JOIN cursos c
            ON c.id = hf.curso_id

        INNER JOIN asignaturas a
            ON a.id = hf.asignatura_id

        INNER JOIN bloques b
            ON b.id = hf.bloque_id

        WHERE hf.id = ?

        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "i",
        $horarioFijoId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $horario = $resultado->fetch_assoc();

    $stmt->close();

    return $horario ?: null;
}

//=====================================================
// CREAR OCURRENCIAS DE HORARIOS FIJOS
//
// Crea una ocurrencia pendiente para cada horario fijo
// que corresponda a una fecha determinada.
//
// No duplica ocurrencias existentes.
//=====================================================

function crearOcurrenciasHorariosFijos(
    mysqli $conexion,
    string $fechaInicio,
    string $fechaFin
): int {

    $horariosFijos = obtenerHorariosFijos(
        $conexion,
        $fechaInicio,
        $fechaFin
    );

    if (empty($horariosFijos)) {
        return 0;
    }

    $creadas = 0;

    $stmt = $conexion->prepare("
    INSERT IGNORE INTO horarios_fijos_ocurrencias (
        horario_fijo_id,
        fecha,
        estado,
        docente_id,
        curso_id,
        asignatura_id
    )
    VALUES (?, ?, 'pendiente', ?, ?, ?)
");

    if (!$stmt) {
        return 0;
    }

    foreach ($horariosFijos as $horario) {

        $fecha = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);

        while ($fecha <= $fin) {

            $diaSemana = (int) $fecha->format('N');

            if (
                $diaSemana === (int) $horario['dia_semana']
            ) {

                $fechaActual = $fecha->format('Y-m-d');

                if (
                    $fechaActual >= $horario['fecha_inicio']
                    &&
                    (
                        $horario['fecha_fin'] === null
                        ||
                        $fechaActual <= $horario['fecha_fin']
                    )
                ) {

                    $horarioFijoId = (int) $horario['id'];

                    $docenteId = (int) $horario['docente_id'];
                    $cursoId = (int) $horario['curso_id'];
                    $asignaturaId = (int) $horario['asignatura_id'];

                    $stmt->bind_param(
                        "isiii",
                        $horarioFijoId,
                        $fechaActual,
                        $docenteId,
                        $cursoId,
                        $asignaturaId
                    );

                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        $creadas++;
                    }
                }
            }

            $fecha->modify('+1 day');
        }
    }

    $stmt->close();

    return $creadas;
}

//=====================================================
// CREAR OCURRENCIAS DE UN HORARIO FIJO
//
// Genera las ocurrencias únicamente del horario fijo
// indicado.
//
// Se utiliza principalmente al editar un horario fijo,
// después de eliminar sus ocurrencias pendientes futuras.
//
// No modifica ocurrencias existentes.
//=====================================================

function crearOcurrenciasHorarioFijo(
    mysqli $conexion,
    int $horarioFijoId,
    string $fechaInicio,
    ?string $fechaFin
): int {

    //=================================================
    // OBTENER HORARIO FIJO
    //=================================================

    $stmt = $conexion->prepare("
        SELECT
            id,
            dia_semana,
            docente_id,
            curso_id,
            asignatura_id,
            fecha_inicio,
            fecha_fin
        FROM horarios_fijos
        WHERE id = ?
        LIMIT 1
    ");

    if (!$stmt) {
        return 0;
    }

    $stmt->bind_param(
        "i",
        $horarioFijoId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $horario = $resultado->fetch_assoc();

    $stmt->close();

    if (!$horario) {
        return 0;
    }


    //=================================================
    // DETERMINAR FECHA FINAL
    //=================================================

    if (
        $fechaFin === null ||
        $fechaFin === ''
    ) {

        // Si no existe fecha de término, utilizamos
        // el 15 de diciembre como término automático.

        $fechaFin = date(
            'Y-12-15',
            strtotime($fechaInicio)
        );
    }


    //=================================================
    // PREPARAR INSERT
    //=================================================

    $stmt = $conexion->prepare("
        INSERT IGNORE INTO horarios_fijos_ocurrencias (
            horario_fijo_id,
            fecha,
            estado,
            docente_id,
            curso_id,
            asignatura_id
        )
        VALUES (?, ?, 'pendiente', ?, ?, ?)
    ");

    if (!$stmt) {
        return 0;
    }


    //=================================================
    // GENERAR FECHAS
    //=================================================

    $fecha = new DateTime($fechaInicio);

    $fin = new DateTime($fechaFin);

    $creadas = 0;

    while ($fecha <= $fin) {

        $diaSemana =
            (int) $fecha->format('N');

        if (
            $diaSemana ===
            (int) $horario['dia_semana']
        ) {

            $fechaActual =
                $fecha->format('Y-m-d');

            $docenteId =
                (int) $horario['docente_id'];

            $cursoId =
                (int) $horario['curso_id'];

            $asignaturaId =
                (int) $horario['asignatura_id'];

            $stmt->bind_param(
                "isiii",
                $horarioFijoId,
                $fechaActual,
                $docenteId,
                $cursoId,
                $asignaturaId
            );

            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $creadas++;
            }
        }

        $fecha->modify('+1 day');
    }

    $stmt->close();

    return $creadas;
}


//=====================================================
// ELIMINAR OCURRENCIAS PENDIENTES FUTURAS
//
// Elimina únicamente las ocurrencias:
//
// - Del horario fijo indicado.
// - Con estado "pendiente".
// - Cuya fecha sea igual o posterior a la fecha indicada.
//
// Las ocurrencias utilizadas, no utilizadas o reasignadas
// permanecen intactas.
//=====================================================

function eliminarOcurrenciasPendientesFuturas(
    mysqli $conexion,
    int $horarioFijoId,
    string $fechaDesde
): bool {

    $stmt = $conexion->prepare("
        DELETE FROM horarios_fijos_ocurrencias
        WHERE
            horario_fijo_id = ?
            AND estado = 'pendiente'
            AND fecha >= ?
    ");

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "is",
        $horarioFijoId,
        $fechaDesde
    );

    $resultado = $stmt->execute();

    $stmt->close();

    return $resultado;
}

//=====================================================
// OBTENER HORARIOS FIJOS POR FECHA
//
// Organiza los horarios fijos de una semana usando:
//
// fecha → bloque → tipo
//
// Ejemplo:
//
// $horarios['2026-08-24'][2]['sub1']
//=====================================================

function obtenerHorariosFijosPorFecha(
    mysqli $conexion,
    string $fechaInicio,
    string $fechaFin
): array {

    $horarios = [];

    $horariosFijos = obtenerHorariosFijos(
        $conexion,
        $fechaInicio,
        $fechaFin
    );

    foreach ($horariosFijos as $horario) {

        $fecha = new DateTime($fechaInicio);
        $fin = new DateTime($fechaFin);

        while ($fecha <= $fin) {

            $diaSemana = (int) $fecha->format('N');

            if (
                $diaSemana === (int) $horario['dia_semana']
            ) {

                $fechaActual = $fecha->format('Y-m-d');

                if (
                    $fechaActual >= $horario['fecha_inicio']
                    &&
                    (
                        $horario['fecha_fin'] === null
                        ||
                        $fechaActual <= $horario['fecha_fin']
                    )
                ) {

                    $bloqueId = (int) $horario['bloque_id'];

                    $tipo = $horario['tipo'];

                    $horarios[$fechaActual][$bloqueId][$tipo]
                        = $horario;
                }
            }

            $fecha->modify('+1 day');
        }
    }

    return $horarios;
}

//=====================================================
// RENDERIZAR TARJETA DE HORARIO FIJO
//
// Muestra la información básica de un horario fijo
// dentro de la agenda.
//=====================================================

function renderizarTarjetaHorarioFijo(
    array $horario,
    ?array $ocurrencia = null
): string {

    $curso = htmlspecialchars(
        $horario['curso'],
        ENT_QUOTES,
        'UTF-8'
    );

    $asignatura = htmlspecialchars(
        $horario['asignatura'],
        ENT_QUOTES,
        'UTF-8'
    );

    $docente = htmlspecialchars(
        $horario['docente'],
        ENT_QUOTES,
        'UTF-8'
    );

    $botonReasignar = '';

    if (
        $ocurrencia !== null &&
        $ocurrencia['estado'] === 'pendiente' &&
        $horario['modalidad'] === 'asignatura'
    ) {

        $botonReasignar = '
        <a
            href="agregar.php?modo=reasignar&horario_fijo_ocurrencia_id='
            . (int) $ocurrencia['id'] . '"
            class="agenda-btn-reasignar">

            Reasignar

        </a>
    ';
    }

    return '
        <div class="agenda-tarjeta agenda-tarjeta-fijo">

            <div class="agenda-tarjeta-curso">
                ' . $curso . '
            </div>

            <div class="agenda-tarjeta-asignatura">
                ' . $asignatura . '
            </div>

            <div class="agenda-tarjeta-docente">
                ' . $docente . '
            </div>

            <div class="agenda-tarjeta-tipo">
                Horario fijo
            </div>

            ' . $botonReasignar . '

        </div>
    ';
}

//=====================================================
// OBTENER OCURRENCIAS DE HORARIOS FIJOS POR FECHA
//
// Organiza las ocurrencias usando:
//
// fecha → bloque → tipo
//
// Permite conocer el ID y estado de la ocurrencia
// correspondiente a cada horario fijo.
//=====================================================

function obtenerOcurrenciasHorariosFijosPorFecha(
    mysqli $conexion,
    string $fechaInicio,
    string $fechaFin
): array {

    $ocurrencias = [];

    $sql = "
        SELECT
            hfo.id,
            hfo.horario_fijo_id,
            hfo.fecha,
            hfo.estado,
            hfo.docente_id,
            hfo.curso_id,
            hfo.asignatura_id,
            hfo.usuario_id,
            hfo.reserva_id,
            hfo.observaciones,
            hfo.fecha_confirmacion,
            hf.bloque_id,
            hf.tipo
        FROM horarios_fijos_ocurrencias hfo
        INNER JOIN horarios_fijos hf
            ON hf.id = hfo.horario_fijo_id
        WHERE hfo.fecha BETWEEN ? AND ?
        ORDER BY hfo.fecha, hf.bloque_id, hf.tipo
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return [];
    }

    $stmt->bind_param(
        "ss",
        $fechaInicio,
        $fechaFin
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    while ($fila = $resultado->fetch_assoc()) {

        $fecha = $fila['fecha'];
        $bloqueId = (int) $fila['bloque_id'];
        $tipo = $fila['tipo'];

        $ocurrencias[$fecha][$bloqueId][$tipo] = $fila;
    }

    $stmt->close();

    return $ocurrencias;
}

//=====================================================
// OBTENER UNA OCURRENCIA DE HORARIO FIJO
//
// Se utiliza para iniciar una reasignación.
//=====================================================

function obtenerOcurrenciaHorarioFijo(
    mysqli $conexion,
    int $ocurrenciaId
): ?array {

    $sql = "
        SELECT
            hfo.id,
            hfo.horario_fijo_id,
            hfo.fecha,
            hfo.estado,
            hfo.docente_id,
            hfo.curso_id,
            hfo.asignatura_id,
            hfo.usuario_id,
            hfo.reserva_id,
            hfo.observaciones,
            hfo.fecha_confirmacion,

            hf.bloque_id,
            hf.tipo,
            hf.modalidad,

            b.numero_bloque,
            b.hora_inicio,
            b.hora_termino,

            CONCAT(d.nombres, ' ', d.apellidos) AS docente,
            c.nombre_curso,
            a.asignatura_nombre

        FROM horarios_fijos_ocurrencias hfo

        INNER JOIN horarios_fijos hf
            ON hf.id = hfo.horario_fijo_id

        INNER JOIN bloques b
            ON b.id = hf.bloque_id

        INNER JOIN docentes d
            ON d.id = hfo.docente_id

        INNER JOIN cursos c
            ON c.id = hfo.curso_id

        INNER JOIN asignaturas a
            ON a.id = hfo.asignatura_id

        WHERE hfo.id = ?

        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    if (!$stmt) {
        return null;
    }

    $stmt->bind_param(
        "i",
        $ocurrenciaId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $ocurrencia = $resultado->fetch_assoc();

    $stmt->close();

    return $ocurrencia ?: null;
}
