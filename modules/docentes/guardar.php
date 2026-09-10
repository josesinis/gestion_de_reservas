<?php
/*
|--------------------------------------------------------------------------
| Sistema     : Gestión Institucional
| Archivo     : modules/docentes/guardar.php
|--------------------------------------------------------------------------
| Descripción :
| Guarda un nuevo docente y sus asignaturas en la base de datos.
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

$nombres = trim($_POST['nombres'] ?? '');

$apellidos = trim($_POST['apellidos'] ?? '');

$correo = trim($_POST['correo'] ?? '');

$activo = $_POST['activo'] ?? '';

$asignaturas = $_POST['asignaturas'] ?? [];


//=====================================================
// 6. VALIDAR DATOS DEL DOCENTE
//=====================================================

if ($nombres === '' || $apellidos === '' || $correo === '') {

    $_SESSION['error'] = 'Todos los campos son obligatorios.';

    header('Location: crear.php');
    exit();
}


if (mb_strlen($nombres) > 50) {

    $_SESSION['error'] = 'El nombre no puede superar los 50 caracteres.';

    header('Location: crear.php');
    exit();
}


if (mb_strlen($apellidos) > 50) {

    $_SESSION['error'] = 'Los apellidos no pueden superar los 50 caracteres.';

    header('Location: crear.php');
    exit();
}


if (mb_strlen($correo) > 50) {

    $_SESSION['error'] = 'El correo no puede superar los 50 caracteres.';

    header('Location: crear.php');
    exit();
}


if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] = 'El correo electrónico no es válido.';

    header('Location: crear.php');
    exit();
}


if ($activo !== '0' && $activo !== '1') {

    $_SESSION['error'] = 'El estado seleccionado no es válido.';

    header('Location: crear.php');
    exit();
}


//=====================================================
// 7. VALIDAR ASIGNATURAS
//=====================================================

if (!is_array($asignaturas)) {

    $_SESSION['error'] = 'Las asignaturas seleccionadas no son válidas.';

    header('Location: crear.php');
    exit();
}


// Convertir IDs a enteros y eliminar duplicados

$asignaturas = array_map('intval', $asignaturas);

$asignaturas = array_unique($asignaturas);


// Eliminar valores inválidos

$asignaturas = array_filter(
    $asignaturas,
    function ($id) {
        return $id > 0;
    }
);


//=====================================================
// 8. INICIAR TRANSACCIÓN
//=====================================================

$conexion->begin_transaction();


try {


    //=================================================
    // 9. INSERTAR DOCENTE
    //=================================================

    $sql = "
        INSERT INTO docentes (
            nombres,
            apellidos,
            correo,
            activo
        )
        VALUES (?, ?, ?, ?)
    ";


    $stmt = $conexion->prepare($sql);


    if (!$stmt) {

        throw new Exception(
            'No fue posible preparar el registro del docente.'
        );
    }


    $activoInt = (int) $activo;


    $stmt->bind_param(
        'sssi',
        $nombres,
        $apellidos,
        $correo,
        $activoInt
    );


    if (!$stmt->execute()) {

        $stmt->close();

        throw new Exception(
            'No fue posible guardar el docente.'
        );
    }


    // Obtener ID del docente recién creado

    $docenteId = $conexion->insert_id;


    $stmt->close();


    //=================================================
    // 10. INSERTAR ASIGNATURAS
    //=================================================

    if (!empty($asignaturas)) {


        $sql = "
            INSERT INTO docentes_asignaturas (
                docente_id,
                asignatura_id
            )
            VALUES (?, ?)
        ";


        $stmtAsignatura = $conexion->prepare($sql);


        if (!$stmtAsignatura) {

            throw new Exception(
                'No fue posible preparar las asignaturas del docente.'
            );
        }


        foreach ($asignaturas as $asignaturaId) {


            // Verificar que la asignatura exista y esté activa

            $sqlVerificar = "
                SELECT id
                FROM asignaturas
                WHERE id = ?
                  AND activo = 1
                LIMIT 1
            ";


            $stmtVerificar = $conexion->prepare($sqlVerificar);


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

            $resultado = $stmtVerificar->get_result();

            $asignaturaValida = $resultado->fetch_assoc();

            $stmtVerificar->close();


            if (!$asignaturaValida) {

                $stmtAsignatura->close();

                throw new Exception(
                    'Una de las asignaturas seleccionadas no es válida o está inactiva.'
                );
            }


            // Guardar relación docente ↔ asignatura

            $stmtAsignatura->bind_param(
                'ii',
                $docenteId,
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
    // 11. CONFIRMAR TRANSACCIÓN
    //=================================================

    $conexion->commit();


} catch (Exception $e) {


    //=================================================
    // 12. DESHACER CAMBIOS SI HAY ERROR
    //=================================================

    $conexion->rollback();


    $_SESSION['error'] = $e->getMessage();

    header('Location: crear.php');
    exit();

}


//=====================================================
// 13. MENSAJE DE ÉXITO
//=====================================================

$_SESSION['exito'] =
    'Docente creado correctamente.';


//=====================================================
// 14. REDIRECCIÓN
//=====================================================

header('Location: index.php');

exit();
