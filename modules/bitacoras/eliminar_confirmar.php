<?php

//=====================================================
// BITÁCORA - ELIMINAR
//=====================================================
//
// Elimina un registro de bitácora y sus recursos
// asociados.
//
// Acceso:
// - Solo superadmin
//
// NO elimina:
// - La reserva
// - La ocurrencia de horario fijo
//
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN Y ROL
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

require_once '../../includes/permisos.php';

requiereRol('superadmin');


//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';


//=====================================================
// 3. VALIDAR ID
//=====================================================

$bitacoraId = (int) ($_POST['id'] ?? 0);

if ($bitacoraId <= 0) {

    $_SESSION['error'] =
        'El registro de bitácora no es válido.';

    header('Location: index.php');

    exit();
}


//=====================================================
// 4. COMPROBAR QUE EXISTE
//=====================================================

$sqlExiste = "

    SELECT id

    FROM bitacoras

    WHERE id = ?

    LIMIT 1

";


$stmtExiste =
    $conexion->prepare($sqlExiste);


if (!$stmtExiste) {

    $_SESSION['error'] =
        'No se pudo comprobar el registro de bitácora.';

    header('Location: index.php');

    exit();
}


$stmtExiste->bind_param(
    "i",
    $bitacoraId
);


if (!$stmtExiste->execute()) {

    $stmtExiste->close();

    $_SESSION['error'] =
        'No se pudo comprobar el registro de bitácora.';

    header('Location: index.php');

    exit();
}


$resultadoExiste =
    $stmtExiste->get_result();


$registroExiste =
    $resultadoExiste->fetch_assoc();


$stmtExiste->close();


if (!$registroExiste) {

    $_SESSION['error'] =
        'El registro de bitácora no existe.';

    header('Location: index.php');

    exit();
}


//=====================================================
// 5. INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();


try {


    //=================================================
    // 5.1 ELIMINAR RECURSOS ASOCIADOS
    //=================================================

    $sqlRecursos = "

        DELETE FROM bitacora_recursos

        WHERE bitacora_id = ?

    ";


    $stmtRecursos =
        $conexion->prepare(
            $sqlRecursos
        );


    if (!$stmtRecursos) {

        throw new Exception(
            'No se pudo preparar la eliminación de recursos.'
        );
    }


    $stmtRecursos->bind_param(
        "i",
        $bitacoraId
    );


    if (!$stmtRecursos->execute()) {

        $stmtRecursos->close();

        throw new Exception(
            'No se pudieron eliminar los recursos asociados.'
        );
    }


    $stmtRecursos->close();


    //=================================================
    // 5.2 ELIMINAR BITÁCORA
    //=================================================

    $sqlBitacora = "

        DELETE FROM bitacoras

        WHERE id = ?

    ";


    $stmtBitacora =
        $conexion->prepare(
            $sqlBitacora
        );


    if (!$stmtBitacora) {

        throw new Exception(
            'No se pudo preparar la eliminación de la bitácora.'
        );
    }


    $stmtBitacora->bind_param(
        "i",
        $bitacoraId
    );


    if (!$stmtBitacora->execute()) {

        $stmtBitacora->close();

        throw new Exception(
            'No se pudo eliminar la bitácora.'
        );
    }


    if (
        $stmtBitacora->affected_rows !== 1
    ) {

        $stmtBitacora->close();

        throw new Exception(
            'El registro de bitácora no pudo ser eliminado.'
        );
    }


    $stmtBitacora->close();


    //=================================================
    // 5.3 CONFIRMAR TRANSACCIÓN
    //=================================================

    $conexion->commit();


    $_SESSION['exito'] =
        'El registro de bitácora fue eliminado correctamente.';


} catch (Throwable $e) {


    //=================================================
    // 5.4 DESHACER CAMBIOS
    //=================================================

    $conexion->rollback();


    $_SESSION['error'] =
        'No se pudo eliminar el registro de bitácora.';
}


//=====================================================
// 6. VOLVER AL LISTADO
//=====================================================

header('Location: index.php');

exit();
