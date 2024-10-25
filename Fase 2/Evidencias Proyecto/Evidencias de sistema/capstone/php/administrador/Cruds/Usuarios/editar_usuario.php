<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "restaurante_bd");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$id_usuario = $_POST['id_usuario'];
$nombre_usuario = $_POST['nombre_usuario'];
$contrasena = !empty($_POST['contrasena']) ? password_hash($_POST['contrasena'], PASSWORD_DEFAULT) : null;
$nombre = $_POST['nombre'];
$rut = $_POST['rut'];
$horario = $_POST['horario'];
$disponible = isset($_POST['disponible']) ? 1 : 0;
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$direccion = $_POST['direccion'];
$fecha_ingreso = $_POST['fecha_ingreso'];
$tipo_usuario = $_POST['tipo_usuario'];

// Preparar la consulta SQL
$sql = "UPDATE usuarios SET nombre_usuario='$nombre_usuario', nombre='$nombre', rut='$rut', horario='$horario', disponible='$disponible', telefono='$telefono', email='$email', direccion='$direccion', fecha_ingreso='$fecha_ingreso', tipo_usuario='$tipo_usuario'";

if ($contrasena) {
    $sql .= ", contrasena='$contrasena'";
}

$sql .= " WHERE id_usuario='$id_usuario'";

if ($conn->query($sql) === TRUE) {
    // Redirigir a la página de administración de usuarios
    header("Location: admin_usuarios.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>