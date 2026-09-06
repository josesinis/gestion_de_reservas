<?php

//=====================================================
// BITÁCORA - ACTUALIZAR
//=====================================================
//
// Guarda los cambios realizados sobre:
//
// - Objetivo
// - Actividad
// - Recursos
// - Observaciones
//
// Acceso:
// - admin
// - superadmin
//=====================================================


//=====================================================
// 1. VALIDAR SESIÓN Y ROL
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();

require_once '../../includes/permisos.php';

requiereRol(['admin', 'superadmin']);


//=====================================================
// 2. ARCHIVOS NECESARIOS
//=====================================================

require_once '../../config/database.php';


//=====================================================
// 3. RECIBIR DATOS
//=====================================================

$bitacoraId =
    (int) ($_POST['id'] ?? 0);


$objetivoClase =
    trim(
        $_POST['objetivo_clase'] ?? ''
    );


$actividad =
    trim(
        $_POST['actividad'] ?? ''
    );


$observaciones =
    trim(
        $_POST['observaciones'] ?? ''
    );


$recursos =
    $_POST['recursos'] ?? [];


//=====================================================
// 4. VALIDAR ID
//=====================================================

if ($bitacoraId <= 0) {

    $_SESSION['error'] =
        'El registro de bitácora no es válido.';

    header('Location: index.php');

    exit();
}


//=====================================================
// 5. VALIDAR OBJETIVO
//=====================================================

if ($objetivoClase === '') {

    $_SESSION['error'] =
        'Debe ingresar el objetivo de la clase.';

    header(
        'Location: editar.php?id='
        . $bitacoraId
    );

    exit();
}


if (mb_strlen($objetivoClase) > 150) {

    $_SESSION['error'] =
        'El objetivo de la clase no puede superar los 150 caracteres.';

    header(
        'Location: editar.php?id='
        . $bitacoraId
    );

    exit();
}


//=====================================================
// 6. VALIDAR ACTIVIDAD
//=====================================================

if ($actividad === '') {

    $_SESSION['error'] =
        'Debe ingresar la actividad realizada.';

    header(
        'Location: editar.php?id='
        . $bitacoraId
    );

    exit();
}


if (mb_strlen($actividad) > 150) {

    $_SESSION['error'] =
        'La actividad no puede superar los 150 caracteres.';

    header(
        'Location: editar.php?id='
        . $bitacoraId
    );

    exit();
}


//=====================================================
// 7. VALIDAR RECURSOS
//=====================================================

if (!is_array($recursos)) {

    $recursos = [];
}


$recursos =
    array_values(
        array_unique(
            array_filter(
                array_map(
                    'intval',
                    $recursos
                ),
                fn($id) => $id > 0
            )
        )
    );


//=====================================================
// 8. COMPROBAR QUE EXISTE LA BITÁCORA
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


$stmtExiste->execute();


$resultadoExiste =
    $stmtExiste->get_result();


$existe =
    $resultadoExiste->fetch_assoc();


$stmtExiste->close();


if (!$existe) {

    $_SESSION['error'] =
        'El registro de bitácora no existe.';

    header('Location: index.php');

    exit();
}


//=====================================================
// 9. INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();


try {


    //=================================================
    // ACTUALIZAR BITÁCORA
    //=================================================

    $sqlBitacora = "

        UPDATE bitacoras

        SET
            objetivo_clase = ?,
            actividad = ?,
            observaciones = ?

        WHERE id = ?

    ";


    $stmtBitacora =
        $conexion->prepare(
            $sqlBitacora
        );


    if (!$stmtBitacora) {

        throw new Exception(
            'No se pudo preparar la actualización de la bitácora.'
        );
    }


    $stmtBitacora->bind_param(
        "sssi",
        $objetivoClase,
        $actividad,
        $observaciones,
        $bitacoraId
    );


    if (!$stmtBitacora->execute()) {

        $stmtBitacora->close();

        throw new Exception(
            'No se pudo actualizar la bitácora.'
        );
    }


    $stmtBitacora->close();


    //=================================================
    // ELIMINAR RECURSOS ANTERIORES
    //=================================================

    $sqlEliminarRecursos = "

        DELETE FROM bitacora_recursos

        WHERE bitacora_id = ?

    ";


    $stmtEliminar =
        $conexion->prepare(
            $sqlEliminarRecursos
        );


    if (!$stmtEliminar) {

        throw new Exception(
            'No se pudieron preparar los recursos anteriores.'
        );
    }


    $stmtEliminar->bind_param(
        "i",
        $bitacoraId
    );


    if (!$stmtEliminar->execute()) {

        $stmtEliminar->close();

        throw new Exception(
            'No se pudieron actualizar los recursos.'
        );
    }


    $stmtEliminar->close();


    //=================================================
    // GUARDAR NUEVOS RECURSOS
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
            $conexion->prepare(
                $sqlRecurso
            );


        if (!$stmtRecurso) {

            throw new Exception(
                'No se pudo preparar el registro de recursos.'
            );
        }


        foreach (
            $recursos
            as $recursoId
        ) {


            $stmtRecurso->bind_param(
                "ii",
                $bitacoraId,
                $recursoId
            );


            if (!$stmtRecurso->execute()) {

                $stmtRecurso->close();

                throw new Exception(
                    'No se pudo guardar uno de los recursos.'
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
        'La bitácora fue actualizada correctamente.';


} catch (Throwable $e) {


    //=================================================
    // DESHACER CAMBIOS
    //=================================================

    $conexion->rollback();


    $_SESSION['error'] =
        'No se pudo actualizar la bitácora.';
}


//=====================================================
// 10. VOLVER AL DETALLE
//=====================================================

header(
    'Location: ver.php?id='
    . $bitacoraId
);

exit();
