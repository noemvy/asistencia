<?php
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
        $error = "La fecha 'Desde' no puede ser mayor que la fecha 'Hasta'.";
    } else {
        // Procesar el formulario normalmente
        // ...
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/reportes.css">
    <title>Asistencia</title>
</head>

<body>

    <form action="eAsistencia.php" method="post" enctype="multipart/form-data">
        <div class="principal">
            <h2 class="title">Procesar Datos</h2>
            <div class="contenido-principal">
                <div class="lado-izquierdo">
                    <label> Seleccionar Archivo </label>
                    <input type="file" name="archivo" id="archivo" accept=".txt, .dat, .log">
                </div>
                <div class="boton">
                    <button type="submit"> Procesar Datos </button>
                </div>
            </div>
        </div>

        <div class="reportes">
            <div>
                <label for=""> Desde </label>
                <input type="date" name="desde" id="desde">
            </div>
            <div>
                <label for=""> Hasta </label>
                <input type="date" name="hasta" id="hasta">
            </div>
            <div>
                <label for=""> Nombre </label>
                <input type="text" name="nombre" id="nombre">
            </div>
            <div>
                <button type="submit">Buscar</button>
            </div>
        </div>
        
        <?php if (isset($error)) { ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php } ?>
    </form>

</body>

</html>