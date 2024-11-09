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

// Obtener el tipo de platillo seleccionado o usar "Plato Principal" por defecto
$tipo_platillo = isset($_GET['tipo_platillo']) ? $_GET['tipo_platillo'] : 'Plato Principal';

// Inicializar el array de platillos
$items = [];

// Consulta para obtener los platillos según el tipo seleccionado
$sql = "SELECT nombre_platillo AS nombre, descripcion_platillo AS descripcion, precio, ruta_foto, id_platillo, tipo_platillo
        FROM Platillos 
        WHERE estado = 'Disponible' AND tipo_platillo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $tipo_platillo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
} else {
    echo json_encode(["message" => "No hay platillos disponibles."]);
    exit;
}

$conn->close();

// Devolver los platillos en formato JSON
echo json_encode(["platillos" => $items]);
?>