<?php
include "../conexion/conciliacion.php";
$nombres = mysqli_query($conect, "SELECT * FROM rrhh");

// Verificar si se recibió un mensaje de error a través de la URL
if (isset($_GET['error'])) {
    $errorMessage = $_GET['error'];
    echo "<script>alert('$errorMessage');</script>";
}

// Verificar si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener las fechas del formulario
    $desde = $_POST['desde'];
    $hasta = $_POST['hasta'];

    // Convertir las fechas a un formato comparable
    $desdeTimestamp = strtotime($desde);
    $hastaTimestamp = strtotime($hasta);

    // Verificar si la fecha "desde" es mayor que la fecha "hasta"
    if ($hastaTimestamp < $desdeTimestamp ) {
        echo "<script>alert('La fecha hasta no puede ser mayor que la fecha desde')</script>";
    } else {
        // Procesar el formulario normalmente
        // ...
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div>
        <h3>Reportes</h3>
        <form method="POST" action="">
            <label for="desde">Desde</label>
            <input type="date" id="desde" name="desde" placeholder="dd/mm/aaaa" required />
            <label for="hasta">Hasta</label>
            <input type="date" id="hasta" name="hasta" placeholder="dd/mm/aaaa" required />
            <select name="nombres" id="nombres">
                <?php while ($listaNombre = mysqli_fetch_assoc($nombres)) { ?>
                    <option value=""><?php echo $listaNombre['nombre1'] . " " . $listaNombre['apellido1'] ?></option>
                <?php } ?>
            </select>
            <button type="submit">Buscar</button>
        </form>
    </div>
</body>
</html>