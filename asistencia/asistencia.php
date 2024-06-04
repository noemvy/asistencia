<?php include "../asistencia/consultaPDF.php" ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencia</title>
</head>

<body>

    <form action="consultaPDF.php" method="post" enctype="multipart/form-data">
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
            <select name="nombres" id="nombres">
                                        <?php if ($nombres && $nombres->num_rows > 0): ?>
                                            <?php while ($row = $nombres->fetch_assoc()): ?>
                                                <option value="<?= htmlspecialchars($row["codigo_marcacion"]) ?>" <?= (isset($selectedValue) && $row["codigo_marcacion"] == $selectedValue) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($row["nombre1"]) ?> <?= htmlspecialchars($row["apellido1"]) ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <option value="">No hay datos disponibles</option>
                                        <?php endif; ?>
                                    </select>
            </div>
            <div>
                <button type="submit" name="buscar">Buscar</button>
            </div>
        </div>
    </form>

</body>

</html>

