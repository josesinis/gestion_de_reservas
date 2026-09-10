<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/docentes/actualizar.php
|--------------------------------------------------------------------------
| Descripción :
| Actualiza los datos de un docente existente y sus asignaturas.
|
| Acceso :
| Exclusivo para superadmin.
|--------------------------------------------------------------------------
*/


//=====================================================
// 1. AUTENTICACIÓN
//=====================================================

require_once '../../includes/auth.php';

requiereLogin();


//=====================================================
// 2. PERMISOS Y BASE DE DATOS
//=====================================================

require_once '../../includes/permisos.php';
require_once '../../config/database.php';


//=====================================================
// 3. VALIDAR ROL
//=====================================================

requiereRol('superadmin');


//=====================================================
// 4. VALIDAR MÉTODO
//=====================================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    $_SESSION['error'] = 'Solicitud no válida.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 5. RECIBIR DATOS
//=====================================================

$id = filter_input(
    INPUT_POST,
    'id',
    FILTER_VALIDATE_INT
);

$nombres = trim($_POST['nombres'] ?? '');

$apellidos = trim($_POST['apellidos'] ?? '');

$correo = trim($_POST['correo'] ?? '');

$activo = $_POST['activo'] ?? '';

$asignaturas = $_POST['asignaturas'] ?? [];


//=====================================================
// 6. VALIDAR ID
//=====================================================

if (!$id || $id <= 0) {

    $_SESSION['error'] = 'Docente no válido.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 7. VALIDAR CAMPOS OBLIGATORIOS
//=====================================================

if ($nombres === '' || $apellidos === '' || $correo === '') {

    $_SESSION['error'] = 'Todos los campos son obligatorios.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 8. VALIDAR LONGITUDES
//=====================================================

if (mb_strlen($nombres) > 50) {

    $_SESSION['error'] = 'El nombre no puede superar los 50 caracteres.';

    header('Location: editar.php?id=' . $id);
    exit();
}


if (mb_strlen($apellidos) > 50) {

    $_SESSION['error'] = 'Los apellidos no pueden superar los 50 caracteres.';

    header('Location: editar.php?id=' . $id);
    exit();
}


if (mb_strlen($correo) > 50) {

    $_SESSION['error'] = 'El correo no puede superar los 50 caracteres.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 9. VALIDAR CORREO
//=====================================================

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] = 'El correo electrónico no es válido.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 10. VALIDAR ESTADO
//=====================================================

if ($activo !== '0' && $activo !== '1') {

    $_SESSION['error'] = 'El estado seleccionado no es válido.';

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 11. VALIDAR ASIGNATURAS
//=====================================================

if (!is_array($asignaturas)) {

    $_SESSION['error'] =
        'Las asignaturas seleccionadas no son válidas.';

    header('Location: editar.php?id=' . $id);
    exit();
}


// Convertir IDs a enteros

$asignaturas = array_map(
    'intval',
    $asignaturas
);


// Eliminar duplicados

$asignaturas = array_unique(
    $asignaturas
);


// Eliminar valores inválidos

$asignaturas = array_filter(
    $asignaturas,
    function ($asignaturaId) {

        return $asignaturaId > 0;
    }
);


//=====================================================
// 12. COMPROBAR QUE EL DOCENTE EXISTA
//=====================================================

$sql = "
    SELECT id
    FROM docentes
    WHERE id = ?
";


$stmt = $conexion->prepare($sql);


if (!$stmt) {

    $_SESSION['error'] =
        'No fue posible consultar el docente.';

    header('Location: index.php');
    exit();
}


$stmt->bind_param(
    'i',
    $id
);

$stmt->execute();

$resultado = $stmt->get_result();

$docente = $resultado->fetch_assoc();

$stmt->close();


if (!$docente) {

    $_SESSION['error'] =
        'El docente no existe.';

    header('Location: index.php');
    exit();
}


//=====================================================
// 13. INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();


try {


    //=================================================
    // 14. ACTUALIZAR DOCENTE
    //=================================================

    $sql = "
        UPDATE docentes
        SET
            nombres = ?,
            apellidos = ?,
            correo = ?,
            activo = ?
        WHERE id = ?
    ";


    $stmt = $conexion->prepare($sql);


    if (!$stmt) {

        throw new Exception(
            'No fue posible preparar la actualización.'
        );
    }


    $activoInt = (int) $activo;


    $stmt->bind_param(
        'sssii',
        $nombres,
        $apellidos,
        $correo,
        $activoInt,
        $id
    );


    if (!$stmt->execute()) {

        $stmt->close();

        throw new Exception(
            'No fue posible actualizar el docente.'
        );
    }


    $stmt->close();


    //=================================================
    // 15. ELIMINAR RELACIONES ANTERIORES
    //=================================================

    $sql = "
        DELETE FROM docentes_asignaturas
        WHERE docente_id = ?
    ";


    $stmt = $conexion->prepare($sql);


    if (!$stmt) {

        throw new Exception(
            'No fue posible actualizar las asignaturas del docente.'
        );
    }


    $stmt->bind_param(
        'i',
        $id
    );


    if (!$stmt->execute()) {

        $stmt->close();

        throw new Exception(
            'No fue posible actualizar las asignaturas del docente.'
        );
    }


    $stmt->close();


    //=================================================
    // 16. GUARDAR NUEVAS ASIGNATURAS
    //=================================================

    if (!empty($asignaturas)) {


        $sql = "
            INSERT INTO docentes_asignaturas (
                docente_id,
                asignatura_id
            )
            VALUES (?, ?)
        ";


        $stmtAsignatura =
            $conexion->prepare($sql);


        if (!$stmtAsignatura) {

            throw new Exception(
                'No fue posible preparar las asignaturas del docente.'
            );
        }


        foreach ($asignaturas as $asignaturaId) {


            //=========================================
            // Verificar asignatura
            //=========================================

            $sqlVerificar = "
                SELECT id
                FROM asignaturas
                WHERE id = ?
                  AND activo = 1
                LIMIT 1
            ";


            $stmtVerificar =
                $conexion->prepare($sqlVerificar);


            if (!$stmtVerificar) {

                $stmtAsignatura->close();

                throw new Exception(
                    'No fue posible validar las asignaturas.'
                );
            }


            $stmtVerificar->bind_param(
                'i',
                $asignaturaId
            );


            $stmtVerificar->execute();

            $resultado =
                $stmtVerificar->get_result();

            $asignaturaValida =
                $resultado->fetch_assoc();

            $stmtVerificar->close();


            if (!$asignaturaValida) {

                $stmtAsignatura->close();

                throw new Exception(
                    'Una de las asignaturas seleccionadas no es válida o está inactiva.'
                );
            }


            //=========================================
            // Insertar relación
            //=========================================

            $stmtAsignatura->bind_param(
                'ii',
                $id,
                $asignaturaId
            );


            if (!$stmtAsignatura->execute()) {

                $stmtAsignatura->close();

                throw new Exception(
                    'No fue posible guardar las asignaturas del docente.'
                );
            }
        }


        $stmtAsignatura->close();
    }


    //=================================================
    // 17. CONFIRMAR TRANSACCIÓN
    //=================================================

    $conexion->commit();
} catch (Exception $e) {


    //=================================================
    // 18. DESHACER CAMBIOS
    //=================================================

    $conexion->rollback();


    $_SESSION['error'] =
        $e->getMessage();

    header('Location: editar.php?id=' . $id);
    exit();
}


//=====================================================
// 19. MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] =
    'Docente actualizado correctamente.';


//=====================================================
// 20. REDIRECCIÓN
//=====================================================

header('Location: index.php');

exit();
