<?php
//=====================================================
// GUARDAR_CONFIRMACION.PHP
// Confirma el uso de una reserva o de una ocurrencia
// de horario fijo y genera una única bitácora.
//=====================================================

//=====================================================
// 1. VALIDAR SESIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// 3. RECIBIR DATOS
//=====================================================

$reservaId = (int) ($_POST['reserva_id'] ?? 0);

$ocurrenciaId = (int) (
    $_POST['horario_fijo_ocurrencia_id'] ?? 0
);

$actividad = trim(
    $_POST['actividad'] ?? ''
);

$objetivoClase = trim(
    $_POST['objetivo_clase'] ?? ''
);

$observaciones = trim(
    $_POST['observaciones'] ?? ''
);

$recursos = $_POST['recursos'] ?? [];


//=====================================================
// 4. VALIDAR IDENTIFICADOR
//=====================================================

if ($reservaId <= 0 && $ocurrenciaId <= 0) {

    $_SESSION['error'] =
        'La reserva o la ocurrencia no es válida.';

    header('Location: agenda.php');
    exit();
}

if ($reservaId > 0 && $ocurrenciaId > 0) {

    $_SESSION['error'] =
        'La confirmación no es válida.';

    header('Location: agenda.php');
    exit();
}


//=====================================================
// 5. VALIDAR ACTIVIDAD Y OBJETIVO
//=====================================================

if ($actividad === '') {

    $_SESSION['error'] =
        'Debe ingresar la actividad realizada.';

    if ($reservaId > 0) {
        header('Location: confirmar.php?id=' . $reservaId);
    } else {
        header(
            'Location: confirmar.php?horario_fijo_ocurrencia_id='
                . $ocurrenciaId
        );
    }

    exit();
}

if (mb_strlen($actividad) > 150) {

    $_SESSION['error'] =
        'La actividad no puede superar los 150 caracteres.';

    if ($reservaId > 0) {
        header('Location: confirmar.php?id=' . $reservaId);
    } else {
        header(
            'Location: confirmar.php?horario_fijo_ocurrencia_id='
                . $ocurrenciaId
        );
    }

    exit();
}

if ($objetivoClase === '') {

    $_SESSION['error'] =
        'Debe ingresar el objetivo de la clase.';

    if ($reservaId > 0) {
        header('Location: confirmar.php?id=' . $reservaId);
    } else {
        header(
            'Location: confirmar.php?horario_fijo_ocurrencia_id='
                . $ocurrenciaId
        );
    }

    exit();
}

if (mb_strlen($objetivoClase) > 150) {

    $_SESSION['error'] =
        'El objetivo de la clase no puede superar los 150 caracteres.';

    if ($reservaId > 0) {
        header('Location: confirmar.php?id=' . $reservaId);
    } else {
        header(
            'Location: confirmar.php?horario_fijo_ocurrencia_id='
                . $ocurrenciaId
        );
    }

    exit();
}


//=====================================================
// 6. VALIDAR RECURSOS
//=====================================================

if (!is_array($recursos)) {
    $recursos = [];
}

$recursos = array_values(
    array_unique(
        array_filter(
            array_map('intval', $recursos),
            fn($id) => $id > 0
        )
    )
);


//=====================================================
// 7. OBTENER Y VALIDAR OBJETO
//=====================================================

$reserva = null;
$ocurrencia = null;

if ($reservaId > 0) {

    $reserva = obtenerReservaPorId(
        $conexion,
        $reservaId
    );

    if (!$reserva) {

        $_SESSION['error'] =
            'La reserva no existe.';

        header('Location: agenda.php');
        exit();
    }

    if ($reserva['estado'] !== 'reservada') {

        $_SESSION['error'] =
            'Esta reserva ya no se encuentra disponible para confirmar.';

        header('Location: ver.php?id=' . $reservaId);
        exit();
    }

    if (!reservaPuedeConfirmarse($reserva)) {

        $_SESSION['error'] =
            'El uso de la sala todavía no puede ser confirmado porque el horario aún no comienza.';

        header('Location: agenda.php');
        exit();
    }
} else {

    $ocurrencia = obtenerOcurrenciaHorarioFijo(
        $conexion,
        $ocurrenciaId
    );

    if (!$ocurrencia) {

        $_SESSION['error'] =
            'La ocurrencia del horario fijo no existe.';

        header('Location: agenda.php');
        exit();
    }

    if ($ocurrencia['estado'] !== 'pendiente') {

        $_SESSION['error'] =
            'Esta ocurrencia de horario fijo ya no está disponible para confirmar.';

        header('Location: agenda.php');
        exit();
    }

    if (!ocurrenciaHorarioFijoPuedeConfirmarse($ocurrencia)) {

        $_SESSION['error'] =
            'El uso de la sala todavía no puede ser confirmado porque el horario aún no comienza.';

        header(
            'Location: confirmar.php?horario_fijo_ocurrencia_id='
                . $ocurrenciaId
        );

        exit();
    }
}


//=====================================================
// 8. INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();

try {

    //=================================================
    // CONFIRMAR RESERVA O OCURRENCIA
    //=================================================

    if ($reservaId > 0) {

        //-------------------------------------------------
        // RESERVA NORMAL
        //-------------------------------------------------

        $sqlReserva = "
            UPDATE reservas
            SET
                actividad = ?,
                objetivo_clase = ?,
                estado = 'utilizada'
            WHERE id = ?
              AND estado = 'reservada'
        ";

        $stmtReserva =
            $conexion->prepare($sqlReserva);

        if (!$stmtReserva) {
            throw new Exception(
                'No se pudo preparar la actualización de la reserva.'
            );
        }

        $stmtReserva->bind_param(
            "ssi",
            $actividad,
            $objetivoClase,
            $reservaId
        );

        if (!$stmtReserva->execute()) {

            $stmtReserva->close();

            throw new Exception(
                'No se pudo actualizar la reserva.'
            );
        }

        if ($stmtReserva->affected_rows !== 1) {

            $stmtReserva->close();

            throw new Exception(
                'La reserva ya no está disponible para confirmar.'
            );
        }

        $stmtReserva->close();
    } else {

        //-------------------------------------------------
        // HORARIO FIJO DIRECTO
        //-------------------------------------------------

        $usuarioId = (int) $_SESSION['usuario_id'];

        $sqlOcurrencia = "
            UPDATE horarios_fijos_ocurrencias
            SET
                estado = 'utilizada',
                usuario_id = ?,
                observaciones = ?,
                fecha_confirmacion = NOW()
            WHERE id = ?
              AND estado = 'pendiente'
        ";

        $stmtOcurrencia =
            $conexion->prepare($sqlOcurrencia);

        if (!$stmtOcurrencia) {
            throw new Exception(
                'No se pudo preparar la actualización de la ocurrencia.'
            );
        }

        $stmtOcurrencia->bind_param(
            "isi",
            $usuarioId,
            $observaciones,
            $ocurrenciaId
        );

        if (!$stmtOcurrencia->execute()) {

            $stmtOcurrencia->close();

            throw new Exception(
                'No se pudo actualizar la ocurrencia del horario fijo.'
            );
        }

        if ($stmtOcurrencia->affected_rows !== 1) {

            $stmtOcurrencia->close();

            throw new Exception(
                'La ocurrencia ya no está disponible para confirmar.'
            );
        }

        $stmtOcurrencia->close();
    }


    //=================================================
    // 9. DETERMINAR VÍNCULO DE LA BITÁCORA
    //=================================================
    //
    // Reserva normal:
    //   reserva_id = reserva
    //   horario_fijo_ocurrencia_id = NULL
    //
    // Horario fijo utilizado directamente:
    //   reserva_id = NULL
    //   horario_fijo_ocurrencia_id = ocurrencia
    //
    // Reasignación:
    //   reserva_id = nueva reserva
    //   horario_fijo_ocurrencia_id = ocurrencia original
    //=================================================

    $bitacoraReservaId = null;
    $bitacoraOcurrenciaId = null;

    if ($reservaId > 0) {

        $bitacoraReservaId = $reservaId;

        $sqlOcurrencia = "
            SELECT id
            FROM horarios_fijos_ocurrencias
            WHERE reserva_id = ?
            LIMIT 1
        ";

        $stmtOcurrencia =
            $conexion->prepare($sqlOcurrencia);

        if (!$stmtOcurrencia) {
            throw new Exception(
                'No se pudo comprobar si la reserva proviene de una reasignación.'
            );
        }

        $stmtOcurrencia->bind_param(
            "i",
            $reservaId
        );

        if (!$stmtOcurrencia->execute()) {

            $stmtOcurrencia->close();

            throw new Exception(
                'No se pudo comprobar el origen de la reserva.'
            );
        }

        $resultado =
            $stmtOcurrencia->get_result();

        $fila =
            $resultado->fetch_assoc();

        $stmtOcurrencia->close();

        if ($fila) {

            $bitacoraOcurrenciaId =
                (int) $fila['id'];
        }
    } else {

        $bitacoraOcurrenciaId =
            $ocurrenciaId;
    }


    //=================================================
    // 10. CREAR UNA ÚNICA BITÁCORA
    //=================================================

    $sqlBitacora = "
        INSERT INTO bitacoras (
            reserva_id,
            objetivo_clase,
            actividad,
            horario_fijo_ocurrencia_id,
            observaciones
        )
        VALUES (
            NULLIF(?, 0),
            ?,
            ?,
            NULLIF(?, 0),
            ?
        )
    ";

    $stmtBitacora =
        $conexion->prepare($sqlBitacora);

    if (!$stmtBitacora) {
        throw new Exception(
            'No se pudo preparar la creación de la bitácora.'
        );
    }

    // Las columnas de vínculo son opcionales.
    // Se utiliza 0 como valor temporal y NULLIF
    // lo convierte explícitamente en NULL.

    $bitacoraReservaId =
        $bitacoraReservaId ?? 0;

    $bitacoraOcurrenciaId =
        $bitacoraOcurrenciaId ?? 0;

    $stmtBitacora->bind_param(
        "issis",
        $bitacoraReservaId,
        $objetivoClase,
        $actividad,
        $bitacoraOcurrenciaId,
        $observaciones
    );

    if (!$stmtBitacora->execute()) {

        $stmtBitacora->close();

        throw new Exception(
            'No se pudo crear la bitácora.'
        );
    }

    $bitacoraId =
        $conexion->insert_id;

    $stmtBitacora->close();


    //=================================================
    // 11. GUARDAR RECURSOS UTILIZADOS
    //=================================================

    if (!empty($recursos)) {

        $sqlRecurso = "
            INSERT INTO bitacora_recursos (
                bitacora_id,
                recurso_id
            )
            VALUES (?, ?)
        ";

        $stmtRecurso =
            $conexion->prepare($sqlRecurso);

        if (!$stmtRecurso) {
            throw new Exception(
                'No se pudo preparar el registro de recursos.'
            );
        }

        foreach ($recursos as $recursoId) {

            $stmtRecurso->bind_param(
                "ii",
                $bitacoraId,
                $recursoId
            );

            if (!$stmtRecurso->execute()) {

                $stmtRecurso->close();

                throw new Exception(
                    'No se pudo registrar uno de los recursos utilizados.'
                );
            }
        }

        $stmtRecurso->close();
    }


    //=================================================
    // 12. CONFIRMAR TRANSACCIÓN
    //=================================================

    $conexion->commit();

    $_SESSION['exito'] =
        'El uso de la sala fue confirmado correctamente.';
} catch (Throwable $e) {

    $conexion->rollback();

    $_SESSION['error'] =
        'No se pudo confirmar el uso de la sala.';
}


//=====================================================
// 13. REDIRECCIÓN
//=====================================================

header('Location: agenda.php');
exit();
