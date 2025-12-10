<?php
// Muestra errores de ejecución para ayudarte a diagnosticar el problema
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// =================================================================
// 1. CONFIGURACIÓN DE LA BASE DE DATOS - ¡ESTA ES LA CONFIGURACIÓN ESTÁNDAR PARA XAMPP/MAMP!
// =================================================================

$servername = "localhost"; 
$username = "root";     
$password = "";         
$dbname = "sella_db";   

// Crea conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Función para mostrar la página de resultado (éxito o error) con estilo
function mostrar_resultado($titulo, $mensaje, $clase_color, $nombre = null, $email = null, $fecha = null, $precio = null) {
    // Código HTML y CSS en línea para la página de confirmación/error
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Resultado de la Reserva</title>
        <style>
            body { font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
            .contenedor-resultado { background-color: #ffffff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); text-align: center; max-width: 500px; width: 90%; }
            h2 { font-size: 2em; margin-bottom: 15px; }
            .success h2 { color: #28a745; }
            .error h2 { color: #dc3545; }
            p { color: #555; font-size: 1.1em; line-height: 1.5; }
            strong { font-weight: bold; color: #333; }
            .detalle { text-align: left; margin: 20px 0; padding: 15px; background-color: #f9f9f9; border-radius: 5px; border-left: 5px solid ' . ($clase_color == 'success' ? '#28a745' : '#dc3545') . '; }
            .cta-button { display: inline-block; background-color: #007bff; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; margin-top: 20px; font-weight: bold; transition: background-color 0.3s; }
            .cta-button:hover { background-color: #0056b3; }
        </style>
    </head>
    <body>
        <div class="contenedor-resultado ' . $clase_color . '">
            <h2>' . $titulo . '</h2>
            <p>' . $mensaje . '</p>';

            // Si es éxito, mostramos los detalles
            if ($clase_color == 'success') {
                echo '<div class="detalle">';
                echo '<p><strong>Cliente:</strong> ' . htmlspecialchars($nombre) . '</p>';
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($email) . '</p>';
                echo '<p><strong>Fecha y Hora:</strong> ' . date('d-m-Y H:i', strtotime($fecha)) . '</p>';
                echo '<p><strong>Costo total:</strong> ' . number_format($precio, 2) . ' €</p>';
                echo '</div>';
            }

            echo '<a href="alquiler.html" class="cta-button">Hacer otra reserva</a>
                <a href="index.html" class="cta-button" style="background-color: #555; margin-left: 10px;">Volver al inicio</a>
        </div>
    </body>
    </html>';
    exit;
}

// Verifica conexión
if ($conn->connect_error) {
    mostrar_resultado("❌ Error de Conexión", "No se pudo conectar a la base de datos. Error: " . $conn->connect_error . " Revisa las credenciales.", "error");
}

// =================================================================
// 2. RECUPERAR DATOS DEL FORMULARIO Y CALCULAR PRECIO
// =================================================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitizar y obtener datos
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefono = $conn->real_escape_string($_POST['telefono']);
    $tipo_canoa = $conn->real_escape_string($_POST['tipo_canoa']); 
    $fecha_inicio = $conn->real_escape_string($_POST['fecha_inicio']);

    // Cálculo de precio
    $precio_total = 0;
    if ($tipo_canoa == 'individual') {
        $precio_total = 30.00;
    } elseif ($tipo_canoa == 'doble') {
        $precio_total = 50.00; 
    } elseif ($tipo_canoa == 'triple') {
        $precio_total = 60.00; 
    }

    // =================================================================
    // 3. PROCESAR CLIENTE (Buscar o Insertar)
    // =================================================================

    $sql_cliente_check = "SELECT ID_Cliente FROM Clientes WHERE Email = '$email'";
    $result = $conn->query($sql_cliente_check);
    $id_cliente = 0;

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_cliente = $row['ID_Cliente'];
    } else {
        $sql_insert_cliente = "INSERT INTO Clientes (Nombre, Email, Telefono) VALUES ('$nombre', '$email', '$telefono')";

        if ($conn->query($sql_insert_cliente) === TRUE) {
            $id_cliente = $conn->insert_id;
        } else {
            mostrar_resultado("❌ Error al Registrar Cliente", "Hubo un problema al guardar tus datos de contacto.", "error");
        }
    }

    // =================================================================
    // 4. VERIFICAR DISPONIBILIDAD DE CANOA E INSERTAR RESERVA
    // =================================================================

    // Búsqueda de disponibilidad
    $sql_canoa_disponible = "
        SELECT ID_Canoa FROM Canoas c
        WHERE c.Tipo = '$tipo_canoa' 
        AND c.Estado = 'Disponible' 
        AND c.ID_Canoa NOT IN (
            SELECT ID_Canoa FROM Reservas 
            WHERE 
                (Fecha_Inicio <= '$fecha_inicio' AND Fecha_Fin >= '$fecha_inicio') OR 
                (Fecha_Inicio >= '$fecha_inicio' AND Fecha_Fin <= DATE_ADD('$fecha_inicio', INTERVAL 4 HOUR))
        )
        LIMIT 1";

    $result_canoa = $conn->query($sql_canoa_disponible);
    $id_canoa = 0;

    if ($result_canoa && $result_canoa->num_rows > 0) {
        $row_canoa = $result_canoa->fetch_assoc();
        $id_canoa = $row_canoa['ID_Canoa'];

        // Calcular la hora de finalización (4 horas de alquiler)
        $fecha_fin_calculada = date('Y-m-d H:i:s', strtotime($fecha_inicio . ' +4 hours'));

        $sql_insert_reserva = "INSERT INTO Reservas (ID_Canoa, ID_Cliente, Fecha_Inicio, Fecha_Fin, Precio_Total) 
                            VALUES ('$id_canoa', '$id_cliente', '$fecha_inicio', '$fecha_fin_calculada', '$precio_total')";

        if ($conn->query($sql_insert_reserva) === TRUE) {
            // ÉXITO
            mostrar_resultado(
                "¡Reserva Realizada con Éxito! 🎉", 
                "Tu alquiler ha sido confirmado. La canoa tipo <strong>$tipo_canoa</strong> te espera.", 
                "success", 
                $nombre, 
                $email, 
                $fecha_inicio, 
                $precio_total
            );
        } else {
            mostrar_resultado("❌ Error al Reservar", "Ocurrió un error al guardar la reserva. Posiblemente por tablas SQL no creadas.", "error");
        }

    } else {
        // ERROR: No hay canoas disponibles
        mostrar_resultado(
            "¡Lo sentimos!", 
            "No tenemos canoas del tipo <strong>$tipo_canoa</strong> disponibles para la fecha y hora seleccionadas.", 
            "error"
        );
    }

} else {
    // Si acceden directamente al archivo sin POST
    header("Location: alquiler.html");
    exit();
}

$conn->close();
?>