<?php
//=====================================================
// CAMBIAR RESERVAS NO UTILIZADAS AUTOMÁTICAMENTE
//
// Cambia a "no_utilizada" las reservas que:
// - siguen en estado "reservada"
// - y cuya fecha ya quedó fuera del plazo de confirmación
//
// El plazo de confirmación corresponde al día de la reserva
// y al día calendario siguiente completo.
//
// Ejemplo:
// Reserva del miércoles 10
// → se puede confirmar durante el miércoles 10
// → se puede confirmar durante el jueves 11
// → desde el viernes 12 pasa a "no_utilizada"
//
// No elimina registros.
// No genera bitácoras.
//=====================================================

require_once __DIR__ . '/../../config/database.php';

//=====================================================
// OBTENER FECHA ACTUAL
//=====================================================

$ahora = new DateTime();

$fechaActual = $ahora->format('Y-m-d');

//=====================================================
// CAMBIAR RESERVAS FUERA DEL PLAZO DE CONFIRMACIÓN
//=====================================================
//
// Una reserva sigue siendo confirmable durante:
// - el día de la reserva
// - el día calendario siguiente
//
// Por lo tanto, recién se cambia a "no_utilizada"
// cuando la fecha de la reserva es anterior al día anterior.
//
// Ejemplo:
// fecha actual = 2026-09-12
// última fecha confirmable = 2026-09-11
// se procesan reservas con fecha <= 2026-09-10
//=====================================================

$sql = "
    UPDATE reservas r

    SET r.estado = 'no_utilizada'

    WHERE r.estado = 'reservada'

      AND r.fecha < DATE_SUB(?, INTERVAL 1 DAY)
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    die('Error al preparar la actualización automática: '
        . $conexion->error);
}

$stmt->bind_param(
    "s",
    $fechaActual
);

if (!$stmt->execute()) {

    die('Error al ejecutar la actualización automática: '
        . $stmt->error);
}

//=====================================================
// RESULTADO
//=====================================================

echo 'Reservas cambiadas automáticamente a no_utilizada: '
    . $stmt->affected_rows;

$stmt->close();
