<?php
//=====================================================
// CANCELAR AUTOMÁTICAMENTE
//
// Cambia a "cancelada" las reservas que:
// - siguen en estado "reservada"
// - y cuyo bloque ya terminó
//
// No elimina registros.
// No genera bitácoras.
//=====================================================

require_once __DIR__ . '/../../config/database.php';

//=====================================================
// OBTENER FECHA Y HORA ACTUAL
//=====================================================

$ahora = new DateTime();

$fechaActual = $ahora->format('Y-m-d');
$horaActual = $ahora->format('H:i:s');

//=====================================================
// CANCELAR RESERVAS VENCIDAS
//=====================================================

$sql = "
    UPDATE reservas r

    INNER JOIN bloques b
        ON b.id = r.bloque_id

    SET r.estado = 'cancelada'

    WHERE r.estado = 'reservada'

      AND (
            r.fecha < ?

            OR (

                r.fecha = ?

                AND (

                    (
                        r.tipo_reserva IN ('completo', 'sub2')
                        AND b.hora_termino <= ?
                    )

                    OR

                    (
                        r.tipo_reserva = 'sub1'

                        AND ADDTIME(
                            b.hora_inicio,
                            SEC_TO_TIME(
                                TIME_TO_SEC(
                                    TIMEDIFF(
                                        b.hora_termino,
                                        b.hora_inicio
                                    )
                                ) / 2
                            )
                        ) <= ?
                    )

                )
            )
      )
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {

    die('Error al preparar la cancelación automática: '
        . $conexion->error);
}

$stmt->bind_param(
    "ssss",
    $fechaActual,
    $fechaActual,
    $horaActual,
    $horaActual
);

if (!$stmt->execute()) {

    die('Error al ejecutar la cancelación automática: '
        . $stmt->error);
}

//=====================================================
// RESULTADO
//=====================================================

echo 'Reservas canceladas automáticamente: '
    . $stmt->affected_rows;

$stmt->close();
