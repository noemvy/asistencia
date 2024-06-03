<?php
include "../conexion/conciliacion.php";
// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener las fechas del formulario
    $desde = $_POST['desde'];
    $hasta = $_POST['hasta'];

    // Convertir las fechas a un formato comparable
    $desdeTimestamp = strtotime($desde);
    $hastaTimestamp = strtotime($hasta);

    // Verificar si la fecha "desde" es mayor que la fecha "hasta"
    if ($desdeTimestamp > $hastaTimestamp) {
        // Mostrar un mensaje de error
        echo "La fecha 'Desde' no puede ser mayor que la fecha 'Hasta'.";
    } else {
        // Procesar el formulario normalmente
        // ...
    }
}
?>