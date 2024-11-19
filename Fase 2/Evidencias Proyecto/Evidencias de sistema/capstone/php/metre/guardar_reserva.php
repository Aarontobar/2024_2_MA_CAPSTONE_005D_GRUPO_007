<?php
session_start();

// Establecer la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos del formulario
$nombre = isset($_POST['nombre']) ? $conn->real_escape_string($_POST['nombre']) : '';
$apellido = isset($_POST['apellido']) ? $conn->real_escape_string($_POST['apellido']) : '';
$fecha = isset($_POST['fecha']) ? $conn->real_escape_string($_POST['fecha']) : '';
$hora = isset($_POST['hora']) ? $conn->real_escape_string($_POST['hora']) : '';
$mesa = isset($_POST['mesa']) ? intval($_POST['mesa']) : 0;

// Verificar que todos los campos estén completos
if (empty($nombre) || empty($apellido) || empty($fecha) || empty($hora) || $mesa == 0) {
    die("Todos los campos son obligatorios.");
}

// Insertar la reserva en la base de datos
$sql = "INSERT INTO Reserva (nombre_reserva, apellido_reserva, fecha, hora, id_mesa) VALUES ('$nombre', '$apellido', '$fecha', '$hora', '$mesa')";
if ($conn->query($sql) === TRUE) {
    // Redirigir a la página del metre
    header("Location: metre.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar la conexión
$conn->close();
?>