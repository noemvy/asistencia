<?php include "../asistencia/eAsistencia.php" 
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
    </form>

</body>

</html>
