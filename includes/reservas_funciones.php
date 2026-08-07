<?php

declare(strict_types=1);

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
// OBTENER RESERVA PARA EDITAR
//=====================================================

function obtenerReservaEditar(
    mysqli $conexion,
    int $id
): ?array
{
    $sql = "
        SELECT

            r.*,

            b.numero_bloque,
            b.hora_inicio,
            b.hora_termino

        FROM reservas r

        INNER JOIN bloques b
            ON b.id = r.bloque_id

        WHERE r.id = ?

        LIMIT 1
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "i",
        $id
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $reserva = $resultado->fetch_assoc();

    $stmt->close();

    return $reserva ?: null;
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
    //-------------------------------------------------
    // OBTENER RESERVAS EXISTENTES
    //-------------------------------------------------

    $sql = "
        SELECT tipo_reserva
        FROM Reservas
        WHERE fecha = ?
          AND bloque_id = ?
          AND estado = 'reservada'
    ";

    $stmt = $conexion->prepare($sql);

    $stmt->bind_param(
        "si",
        $fecha,
        $bloqueId
    );

    $stmt->execute();

    $resultado = $stmt->get_result();

    $conflicto = false;

    //-------------------------------------------------
    // VALIDAR CONFLICTOS
    //-------------------------------------------------

    while ($fila = $resultado->fetch_assoc()) {

        $tipoExistente = $fila['tipo_reserva'];

        //---------------------------------------------
        // QUIERE RESERVAR BLOQUE COMPLETO
        //---------------------------------------------

        if ($tipoReserva === 'completo') {

            $conflicto = true;
            break;
        }

        //---------------------------------------------
        // QUIERE RESERVAR SUBBLOQUE 1
        //---------------------------------------------

        if (
            $tipoReserva === 'sub1'
            &&
            (
                $tipoExistente === 'sub1'
                ||
                $tipoExistente === 'completo'
            )
        ) {

            $conflicto = true;
            break;
        }

        //---------------------------------------------
        // QUIERE RESERVAR SUBBLOQUE 2
        //---------------------------------------------

        if (
            $tipoReserva === 'sub2'
            &&
            (
                $tipoExistente === 'sub2'
                ||
                $tipoExistente === 'completo'
            )
        ) {

            $conflicto = true;
            break;
        }
    }

    $stmt->close();

    return $conflicto;
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


