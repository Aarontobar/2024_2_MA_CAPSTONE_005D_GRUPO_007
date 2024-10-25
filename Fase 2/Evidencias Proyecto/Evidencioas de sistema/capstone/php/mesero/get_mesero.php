<?php
// Conectar a la base de datos
$servername = "localhost"; // Cambiar si es necesario
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener todos los meseros
$sql = "SELECT id_usuario, nombre FROM usuarios WHERE tipo_usuario = 'mesero'";
$result = $conn->query($sql);

$meseros = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $meseros[] = $row;
    }
}

$conn->close();

// Enviar los datos como JSON
echo json_encode($meseros);
?>