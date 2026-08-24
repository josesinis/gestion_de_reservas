<?php
//=====================================================
// GUARDAR_CONFIRMACION.PHP
// Confirma el uso de una reserva y genera la bitácora.
//=====================================================


session_start();
/*
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../login.php');
    exit();
}*/


require_once '../../config/database.php';
require_once '../../includes/reservas_funciones.php';

//=====================================================
// 1. RECIBIR DATOS
//=====================================================

$reservaId = (int) ($_POST['reserva_id'] ?? 0);

$actividad = trim($_POST['actividad'] ?? '');

$objetivoClase = trim($_POST['objetivo_clase'] ?? '');

$observaciones = trim($_POST['observaciones'] ?? '');

$recursos = $_POST['recursos'] ?? [];


//=====================================================
// 2. VALIDAR ID
//=====================================================

if ($reservaId <= 0) {

    $_SESSION['error'] = 'La reserva no es válida.';

    header('Location: agenda.php');

    exit();
}


//=====================================================
// 3. OBTENER RESERVA
//=====================================================

$reserva = obtenerReservaPorId(
    $conexion,
    $reservaId
);

if (!$reserva) {

    $_SESSION['error'] = 'La reserva no existe.';

    header('Location: agenda.php');

    exit();
}


//=====================================================
// 4. VALIDAR ESTADO
//=====================================================

if ($reserva['estado'] !== 'reservada') {

    $_SESSION['error'] =
        'Esta reserva ya no se encuentra disponible para confirmar.';

    header('Location: ver.php?id=' . $reservaId);

    exit();
}

if (!reservaPuedeConfirmarse($reserva)) {

    $_SESSION['error'] =
        'El uso de la sala todavía no puede ser confirmado porque el horario aún no comienza.';

    header('Location: confirmar.php?id=' . $reservaId);

    exit();
}

//=====================================================
// 5. VALIDAR ACTIVIDAD
//=====================================================

if ($actividad === '') {

    $_SESSION['error'] =
        'Debe ingresar la actividad realizada.';

    header('Location: confirmar.php?id=' . $reservaId);

    exit();
}

if (mb_strlen($actividad) > 150) {

    $_SESSION['error'] =
        'La actividad no puede superar los 150 caracteres.';

    header('Location: confirmar.php?id=' . $reservaId);

    exit();
}


//=====================================================
// 6. VALIDAR OBJETIVO
//=====================================================

if ($objetivoClase === '') {

    $_SESSION['error'] =
        'Debe ingresar el objetivo de la clase.';

    header('Location: confirmar.php?id=' . $reservaId);

    exit();
}

if (mb_strlen($objetivoClase) > 150) {

    $_SESSION['error'] =
        'El objetivo de la clase no puede superar los 150 caracteres.';

    header('Location: confirmar.php?id=' . $reservaId);

    exit();
}


//=====================================================
// 7. VALIDAR RECURSOS
//=====================================================

if (!is_array($recursos)) {

    $recursos = [];
}

// Convertir todos los IDs a enteros

$recursos = array_map(
    'intval',
    $recursos
);

// Eliminar valores inválidos y duplicados

$recursos = array_values(
    array_unique(
        array_filter(
            $recursos,
            fn($id) => $id > 0
        )
    )
);


//=====================================================
// 8. INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();

try {

    //=================================================
    // ACTUALIZAR RESERVA
    //=================================================

    $sqlReserva = "
        UPDATE reservas
        SET
            actividad = ?,
            objetivo_clase = ?,
            estado = 'utilizada'
        WHERE id = ?
          AND estado = 'reservada'
    ";

    $stmtReserva = $conexion->prepare($sqlReserva);

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

    // Verificar que realmente se actualizó

    if ($stmtReserva->affected_rows !== 1) {

        $stmtReserva->close();

        throw new Exception(
            'La reserva ya no está disponible para confirmar.'
        );
    }

    $stmtReserva->close();


    //=================================================
    // CREAR BITÁCORA
    //=================================================

    $sqlBitacora = "
        INSERT INTO bitacoras (
            reserva_id,
            observaciones
        )
        VALUES (?, ?)
    ";

    $stmtBitacora = $conexion->prepare($sqlBitacora);

    if (!$stmtBitacora) {

        throw new Exception(
            'No se pudo preparar la creación de la bitácora.'
        );
    }

    $stmtBitacora->bind_param(
        "is",
        $reservaId,
        $observaciones
    );

    if (!$stmtBitacora->execute()) {

        $stmtBitacora->close();

        throw new Exception(
            'No se pudo crear la bitácora.'
        );
    }

    $bitacoraId = $conexion->insert_id;

    $stmtBitacora->close();


    //=================================================
    // GUARDAR RECURSOS UTILIZADOS
    //=================================================

    if (!empty($recursos)) {

        $sqlRecurso = "
            INSERT INTO bitacora_recursos (
                bitacora_id,
                recurso_id
            )
            VALUES (?, ?)
        ";

        $stmtRecurso = $conexion->prepare($sqlRecurso);

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
    // CONFIRMAR TRANSACCIÓN
    //=================================================

    $conexion->commit();

    $_SESSION['exito'] =
        'El uso de la sala fue confirmado correctamente.';
} catch (Throwable $e) {

    //=================================================
    // DESHACER TODO SI OCURRE UN ERROR
    //=================================================

    $conexion->rollback();

    $_SESSION['error'] =
        'No se pudo confirmar el uso de la sala.';
}


//=====================================================
// REDIRECCIÓN
//=====================================================

header(
    'Location: ver.php?id=' . $reservaId
);

exit();
