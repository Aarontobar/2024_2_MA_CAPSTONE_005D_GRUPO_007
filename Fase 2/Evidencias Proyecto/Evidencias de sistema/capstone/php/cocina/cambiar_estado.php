<?php
// Conectar a la base de datos
$servername = "localhost"; // Cambiar si es necesario
$username = "root"; // Cambiar si es necesario
$password = ""; // Cambiar si es necesario
$dbname = "restaurante_bd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_POST['id_pedido'])) {
    $id_pedido = $_POST['id_pedido'];
    
    // Obtener el estado actual del pedido
    $sql = "SELECT estado FROM Pedido WHERE id_pedido = $id_pedido";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $current_status = $row['estado'];
    
    // Definir el siguiente estado
    $statuses = ['recibido', 'en preparación', 'preparado', 'servido'];
    $next_status = $statuses[array_search($current_status, $statuses) + 1] ?? null;

    if ($next_status) {
        // Actualizar el estado del pedido
        $sql = "UPDATE Pedido SET estado = '$next_status' WHERE id_pedido = $id_pedido";
        if ($conn->query($sql) === TRUE) {
            echo "Estado actualizado";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "No hay siguiente estado";
    }
}

$conn->close();
?>