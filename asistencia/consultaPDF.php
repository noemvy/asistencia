<?php
include "../conexion/conciliacion.php";
session_start(); // Iniciar la sesión;

ini_set('max_execution_time', 300); // 300 segundos = 5 minutos
ini_set('memory_limit', '256M'); // Aumenta si es necesario

if (isset($_FILES["archivo"])) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Crear el directorio si no existe
    }

    $target_file = $target_dir . basename($_FILES["archivo"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es de texto
    if ($fileType != "dat" && $fileType != "txt" && $fileType != "log") {
        echo "Lo siento, solo se permiten archivos .dat, .txt, .log.";
        $uploadOk = 0;
    }

    // Verificar si $uploadOk es 0 por un error
    if ($uploadOk == 0) {
        echo "Tu archivo no fue subido.";
    // Si todo está bien, intenta subir el archivo
    } else {
        if (move_uploaded_file($_FILES["archivo"]["tmp_name"], $target_file)) {
            echo "El archivo " . htmlspecialchars(basename($_FILES["archivo"]["name"])) . " ha sido subido.";

            // Procesar el archivo subido
            $file = fopen($target_file, "r");

            if ($file) {
                // Iniciar transacción
                $conect->begin_transaction();

                // Preparar la declaración SQL para insertar
                $stmt = $conect->prepare("INSERT INTO datos (codigo, fecha, hora, filler1, filler2, filler3, filler4) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    die("Error en la preparación de la consulta: " . $conect->error);
                }
                $stmt->bind_param("sssssss", $codigo, $fecha, $hora, $filler1, $filler2, $filler3, $filler4);

                // Leer línea por línea e insertar en la base de datos
                $lineNumber = 0;
                $batchSize = 100; // Tamaño del lote
                $success = true;

                while (($line = fgets($file)) !== false) {
                    $lineNumber++;
                    // Limpiar espacios en blanco al principio y al final
                    $line = trim($line);

                    // Separar los campos de la línea utilizando un delimitador de espacio
                    $fields = preg_split('/\s+/', $line);

                    if (count($fields) == 7) {
                        $codigo = trim($fields[0]);
                        $fecha = trim($fields[1]);
                        $hora = trim($fields[2]);
                        $filler1 = trim($fields[3]);
                        $filler2 = trim($fields[4]);
                        $filler3 = trim($fields[5]);
                        $filler4 = trim($fields[6]);

                        // Depuración: mostrar los valores extraídos
                        echo "Extrayendo (línea $lineNumber): codigo=$codigo, fecha=$fecha, hora=$hora, filler1=$filler1, filler2=$filler2, filler3=$filler3, filler4=$filler4\n";

                        // Ejecutar la consulta SQL
                        if (!$stmt->execute()) {
                            echo "Error al insertar los datos (línea $lineNumber): " . $stmt->error . "\n";
                            $success = false;
                            break;
                        }
                    } else {
                        echo "Formato de línea incorrecto (línea $lineNumber): $line\n";
                    }

                    // Confirmar el lote
                    if ($lineNumber % $batchSize == 0) {
                        $conect->commit();
                        $conect->begin_transaction();
                    }
                }

                // Confirmar los últimos datos
                if ($success) {
                    $conect->commit();
                    echo "Datos insertados correctamente.\n";
                } else {
                    $conect->rollback();
                    echo "Error al insertar los datos. Transacción revertida.\n";
                }

                fclose($file);

                // Cerrar la declaración y la conexión
                $stmt->close();
                $conect->close();
            } else {
                echo "Error al abrir el archivo.\n";
            }
        } else {
            echo "Lo siento, hubo un error al subir tu archivo.";
        }
    }
} else {
    echo "No se ha seleccionado ningún archivo.";
}


$nombres = $conect->query("SELECT codigo_marcacion, nombre1, apellido1 FROM rrhh");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscar'])) {
    $desde = $_POST['desde'];
    $hasta = $_POST['hasta'];
    $nombre_seleccionado = $_POST['nombres'];

    $query = "SELECT DATE(fecha) as fecha, MIN(hora) as entrada, MAX(hora) as salida
            FROM datos
            WHERE codigo = ? AND fecha BETWEEN ? AND ?
            GROUP BY DATE(fecha)
            ORDER BY fecha ASC";
    $stmt = $conect->prepare($query);
    $stmt->bind_param('sss', $nombre_seleccionado, $desde, $hasta);
    $stmt->execute();
    $result = $stmt->get_result();

    $datos = [];
    while ($row = $result->fetch_assoc()) {
        $datos[] = $row;
    }
    $stmt->close();

    $query_nombre = "SELECT nombre1, apellido1 FROM rrhh WHERE codigo_marcacion = ?";
    $stmt_nombre = $conect->prepare($query_nombre);
    $stmt_nombre->bind_param('s', $nombre_seleccionado);
    $stmt_nombre->execute();
    $result_nombre = $stmt_nombre->get_result();
    $row_nombre = $result_nombre->fetch_assoc();
    $nombre_empleado = $row_nombre['nombre1'] . ' ' . $row_nombre['apellido1'];
    $stmt_nombre->close();

    if (!empty($datos)) {
        $_SESSION['datos'] = $datos;
        $_SESSION['nombre_empleado'] = $nombre_empleado;
        header('Location: pdf.php');
        exit;
    } else {
        echo "No se pudo completar la creacion de archivo";
    }
}


// Verificación de datos de asistencia
if (isset($_POST['buscar'])) {
    // Obtener las fechas del formulario
    $desde = $_POST['desde'];
    $hasta = $_POST['hasta'];

    // Obtener la cédula seleccionada del formulario
    $nombre_seleccionado = $_POST['nombres'];

    // Convertir las fechas a un formato comparable
    $desdeTimestamp = strtotime($desde);
    $hastaTimestamp = strtotime($hasta);

    // Verificar si la fecha "desde" es mayor que la fecha "hasta"
    if ($hastaTimestamp < $desdeTimestamp) {
        echo "<script>alert('La fecha hasta no puede ser mayor que la fecha desde')</script>";
    } else {
}
}


?>
