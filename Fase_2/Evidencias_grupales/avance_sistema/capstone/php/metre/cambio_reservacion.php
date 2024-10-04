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
    $id_reserva = $_POST['id_reserva'];

    $sql = "UPDATE Reserva SET estado_reserva = 'Completada' WHERE id_reserva = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_reserva);

    if ($stmt->execute()) {
        echo "Reservación actualizada con éxito.";
    } else {
        echo "Error al actualizar la reservación: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    // Redirigir a la página de listado de reservaciones
    header("Location: asignar_mesa.php");
    exit();
}
?>