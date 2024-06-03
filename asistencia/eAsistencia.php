<?php
ini_set('max_execution_time', 300); // 300 segundos = 5 minutos
ini_set('memory_limit', '256M'); // Aumenta si es necesario

$servername = "localhost";
$username = "d52024";
$password = "12345";
$dbname = "conciliacion";

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

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
                $conn->begin_transaction();

                // Preparar la declaración SQL para insertar
                $stmt = $conn->prepare("INSERT INTO datos (codigo, fecha, hora, filler1, filler2, filler3, filler4) VALUES (?, ?, ?, ?, ?, ?, ?)");
                if ($stmt === false) {
                    die("Error en la preparación de la consulta: " . $conn->error);
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
                        $conn->commit();
                        $conn->begin_transaction();
                    }
                }

                // Confirmar los últimos datos
                if ($success) {
                    $conn->commit();
                    echo "Datos insertados correctamente.\n";
                } else {
                    $conn->rollback();
                    echo "Error al insertar los datos. Transacción revertida.\n";
                }

                fclose($file);

                // Cerrar la declaración y la conexión
                $stmt->close();
                $conn->close();
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
?>
