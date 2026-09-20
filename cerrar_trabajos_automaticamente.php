<?php
//=====================================================
// CERRAR TRABAJOS AUTOMÁTICAMENTE
//
// Cambia a "cerrado" los trabajos que:
// - siguen en estado "en_proceso"
// - y cuya fecha límite ya fue alcanzada.
//
// La prórroga NO modifica el estado del trabajo.
// Un trabajo puede permanecer "cerrado" aunque tenga
// una prórroga vigente.
//=====================================================

declare(strict_types=1);

require_once __DIR__ . '/config/database.php';

//=====================================================
// ACTUALIZAR TRABAJOS VENCIDOS
//=====================================================

$sql = "
    UPDATE trabajos
    SET estado = 'cerrado'
    WHERE estado = 'en_proceso'
      AND fecha_limite <= NOW()
";

$stmt = $conexion->prepare($sql);

if (!$stmt) {
    die(
        'Error al preparar el cierre automático de trabajos: '
        . $conexion->error
    );
}

if (!$stmt->execute()) {
    die(
        'Error al ejecutar el cierre automático de trabajos: '
        . $stmt->error
    );
}

//=====================================================
// RESULTADO
//=====================================================

echo 'Trabajos cerrados automáticamente: '
    . $stmt->affected_rows;

$stmt->close();
