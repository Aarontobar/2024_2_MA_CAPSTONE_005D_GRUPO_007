<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "restaurante_bd");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$nombre_usuario = $_POST['nombre_usuario'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
$nombre = $_POST['nombre'];
$rut = $_POST['rut'];
$horario = $_POST['horario'];
$disponible = isset($_POST['disponible']) ? 1 : 0;
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$direccion = $_POST['direccion'];
$fecha_ingreso = $_POST['fecha_ingreso'];
$tipo_usuario = $_POST['tipo_usuario'];

// Insertar el nuevo usuario
$sql = "INSERT INTO usuarios (nombre_usuario, contrasena, nombre, rut, horario, disponible, telefono, email, direccion, fecha_ingreso, tipo_usuario)
        VALUES ('$nombre_usuario', '$contrasena', '$nombre', '$rut', '$horario', '$disponible', '$telefono', '$email', '$direccion', '$fecha_ingreso', '$tipo_usuario')";

if ($conn->query($sql) === TRUE) {
    // Redirigir a la página de administración de usuarios
    header("Location: admin_usuarios.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>