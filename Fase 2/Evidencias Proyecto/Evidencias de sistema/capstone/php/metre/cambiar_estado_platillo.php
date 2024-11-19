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
    echo json_encode(['success' => false, 'message' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Verificar si se recibieron los datos necesarios
if (isset($_POST['nombre_platillo']) && isset($_POST['estado'])) {
    $nombrePlatillo = $conn->real_escape_string($_POST['nombre_platillo']);
    $nuevoEstado = $conn->real_escape_string($_POST['estado']);

    // Actualizar el estado del platillo en la base de datos
    $sql = "UPDATE Platillos SET estado = '$nuevoEstado' WHERE nombre_platillo = '$nombrePlatillo'";
    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Estado del platillo actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado del platillo: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
}

// Cerrar la conexión
$conn->close();
?>