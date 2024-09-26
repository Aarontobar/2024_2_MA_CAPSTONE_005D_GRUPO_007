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

// Obtener ID de la mesa desde la solicitud
$id_mesa = isset($_POST['id_mesa']) ? intval($_POST['id_mesa']) : 0;

if ($id_mesa <= 0) {
    echo "ID de mesa inválido";
    exit();
}

// Actualizar el estado de la mesa (esto es un ejemplo, el estado puede ser diferente)
$sql = "UPDATE Mesa SET estado = CASE 
        WHEN estado = 'Ocupada' THEN 'Libre'
        ELSE 'Ocupada'
        END WHERE id_mesa = $id_mesa";

if ($conn->query($sql) === TRUE) {
    echo "Estado actualizado";
} else {
    echo "Error al actualizar estado: " . $conn->error;
}

$conn->close();
?>