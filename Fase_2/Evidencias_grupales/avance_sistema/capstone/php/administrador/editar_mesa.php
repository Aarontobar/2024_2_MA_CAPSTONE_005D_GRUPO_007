<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "restaurante_bd");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener datos del formulario
$id_mesa = $_POST['id_mesa'];
$cantidad_asientos = $_POST['cantidad_asientos'];
$estado = $_POST['estado'];

// Preparar la consulta SQL
$sql = "UPDATE mesa SET cantidad_asientos='$cantidad_asientos', estado='$estado' WHERE id_mesa='$id_mesa'";

if ($conn->query($sql) === TRUE) {
    // Redirigir a la página de administración de mesas
    header("Location: admin_mesas.php");
    exit();
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>