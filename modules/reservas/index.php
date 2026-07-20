<?php

// ============================================
// 1. Archivos necesarios
// ============================================

require_once '../../includes/header.php';
require_once '../../includes/menu.php';
require_once '../../config/database.php';


// ============================================
// 2. Calcular semana a mostrar
// ============================================

$fecha = $_GET['fecha'] ?? date('Y-m-d');

$lunes = new DateTime($fecha);

if ($lunes->format('N') != 1) {
    $lunes->modify('last monday');
}


// ============================================
// 3. Días de la semana
// ============================================

$dias = [];

for ($i = 0; $i < 5; $i++) {

    $dia = clone $lunes;
    $dia->modify("+{$i} day");

    $dias[] = $dia;
}

$ultimoDia = $dias[4];

$nombresDias = [
    'Lunes',
    'Martes',
    'Miércoles',
    'Jueves',
    'Viernes'
];


// ============================================
// 4. Navegación entre semanas
// ============================================

$semanaAnterior = clone $lunes;
$semanaAnterior->modify('-7 days');

$semanaSiguiente = clone $lunes;
$semanaSiguiente->modify('+7 days');


// ============================================
// 5. Obtener bloques
// ============================================

$sqlBloques = "
    SELECT
        id,
        numero_bloque,
        hora_inicio,
        hora_termino
    FROM Bloques
    ORDER BY numero_bloque
";

$resultadoBloques = $conexion->query($sqlBloques);

$bloques = [];

while ($fila = $resultadoBloques->fetch_assoc()) {

    $bloques[] = $fila;
}


// ============================================
// 6. Obtener reservas de la semana
// ============================================

$inicioSemana = $lunes->format('Y-m-d');
$finSemana    = $ultimoDia->format('Y-m-d');

$sqlReservas = "
SELECT

    r.id,
    r.fecha,
    r.bloque_id,
    r.actividad,

    c.nombre_curso,
    a.asignatura_nombre,

    CONCAT(d.nombres,' ',d.apellidos) AS docente

FROM Reservas r

INNER JOIN Cursos c
        ON c.id = r.curso_id

INNER JOIN Asignaturas a
        ON a.id = r.asignatura_id

INNER JOIN Docentes d
        ON d.id = r.docente_id

WHERE r.fecha BETWEEN ? AND ?

ORDER BY
    r.fecha,
    r.bloque_id
";

$stmt = $conexion->prepare($sqlReservas);
$stmt->bind_param("ss", $inicioSemana, $finSemana);
$stmt->execute();

$resultadoReservas = $stmt->get_result();


// ============================================
// 7. Construir matriz del horario
// ============================================

$horario = [];

while ($fila = $resultadoReservas->fetch_assoc()) {

    $horario[$fila['fecha']][$fila['bloque_id']] = $fila;
}

?>

<link rel="stylesheet" href="../../assets/css/reservas.css">

<div class="contenedor-reservas">

    <h1>Gestión de Reservas - Sala de Computación</h1>

    <div class="barra-semana">

        <a class="btn-semana"
            href="?fecha=<?= $semanaAnterior->format('Y-m-d'); ?>">

            &laquo; Semana anterior

        </a>

        <h2>

            Semana del

            <?= $lunes->format('d/m'); ?>

            al

            <?= $ultimoDia->format('d/m/Y'); ?>

        </h2>

        <a class="btn-semana"
            href="?fecha=<?= $semanaSiguiente->format('Y-m-d'); ?>">

            Semana siguiente &raquo;

        </a>

    </div>

    <div class="horario">

        <div class="encabezado">

            Bloque

        </div>

        <?php foreach ($dias as $indice => $dia): ?>

            <div class="encabezado">

                <?= $nombresDias[$indice] ?>

                <br>

                <?= $dia->format('d/m') ?>

            </div>

        <?php endforeach; ?>


        <?php foreach ($bloques as $bloque): ?>

            <div class="bloque">

                <strong>

                    Bloque <?= $bloque['numero_bloque'] ?>

                </strong>

                <br>

                <?= substr($bloque['hora_inicio'], 0, 5) ?>

                -

                <?= substr($bloque['hora_termino'], 0, 5) ?>

            </div>


            <?php foreach ($dias as $dia): ?>

                <?php

                $fechaActual = $dia->format('Y-m-d');

                ?>

                <?php if (isset($horario[$fechaActual][$bloque['id']])):

                    $reserva = $horario[$fechaActual][$bloque['id']];

                ?>

                    <a class="celda reservada" href="ver.php?id=<?= $reserva['id'] ?>">

                        <strong><?= htmlspecialchars($reserva['nombre_curso']) ?></strong>

                        <br>

                        <?= htmlspecialchars($reserva['asignatura_nombre']) ?>

                        <br>

                        <small><?= htmlspecialchars($reserva['docente']) ?></small>

                    </a>

                <?php else: ?>

                    <a class="celda libre" href="agregar.php?fecha=<?= $fechaActual ?>&bloque=<?= $bloque['id'] ?>">

                        <div class="icono">+</div>

                        <div>Libre</div>

                    </a>

                <?php endif; ?>

            <?php endforeach; ?>

        <?php endforeach; ?>

    </div>

</div>

<?php require_once '../../includes/footer.php'; ?>
