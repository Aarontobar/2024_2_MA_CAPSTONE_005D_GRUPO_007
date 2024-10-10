<?php
// Habilitar el reporte de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Establecer la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_mesa = $_POST['id_mesa'];

    // 1. Obtener un ID de mesero aleatorio
    $sql_mesero = "SELECT id_usuario FROM usuarios WHERE tipo_usuario = 'mesero' ORDER BY RAND() LIMIT 1"; 
    $resultado_mesero = $conn->query($sql_mesero);
    
    if ($resultado_mesero->num_rows > 0) {
        $fila_mesero = $resultado_mesero->fetch_assoc();
        $id_mesero = $fila_mesero['id_usuario'];

        // 2. Actualizar el estado de la mesa
        $sql = "UPDATE Mesa SET estado = 'Ocupada' WHERE id_mesa = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_mesa);

        if ($stmt->execute()) {
            // 3. Registrar el detalle de la mesa en la tabla correspondiente
            $sql_detalle = "INSERT INTO detalle_mesero_mesa (id_mesa, id_usuario) VALUES (?, ?)";
            $stmt_detalle = $conn->prepare($sql_detalle);
            $stmt_detalle->bind_param("ii", $id_mesa, $id_usuario);

            if ($stmt_detalle->execute()) {
                echo "Mesa actualizada y detalle registrado con éxito.";
            } else {
                echo "Error al registrar el detalle de la mesa: " . $conn->error;
            }

            $stmt_detalle->close();
        } else {
            echo "Error al actualizar la mesa: " . $conn->error;
        }

        $stmt->close();
    } else {
        echo "No hay meseros disponibles.";
    }

    $conn->close();

    // Redirigir a la página de listado de mesas
    header("Location: asignar_mesa.php");
    exit();
}
?>