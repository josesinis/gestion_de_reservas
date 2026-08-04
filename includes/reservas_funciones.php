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
): array
{
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
