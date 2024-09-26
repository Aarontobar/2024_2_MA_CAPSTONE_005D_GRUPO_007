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
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Establecer la zona horaria a Santiago, Chile
date_default_timezone_set('America/Santiago');

// Obtener la fecha actual
$fecha = date('Y-m-d');  // Obtiene la fecha en formato YYYY-MM-DD

// Obtener la hora de cierre del formulario
$horaCierre = isset($_POST['horaCierre']) ? $_POST['horaCierre'] : null;  // Capturar la hora de cierre

// Obtener los datos del formulario
$mesasDisponibles = isset($_POST['mesasDisponibles']) ? intval($_POST['mesasDisponibles']) : 0;
$platillosNoDisponibles = isset($_POST['estado_platillo']) ? $_POST['estado_platillo'] : [];
$preciosPlatillos = isset($_POST['precio_platillo']) ? $_POST['precio_platillo'] : [];

// Obtener todos los IDs de los platillos
$resultPlatillos = $conn->query("SELECT id_platillo FROM Platillos");
$platillosTotales = [];
while ($row = $resultPlatillos->fetch_assoc()) {
    $platillosTotales[] = $row['id_platillo'];
}

// Lógica para manejar el estado de los platillos
if (!isset($_POST['togglePlatillos'])) {
    foreach ($platillosTotales as $platilloId) {
        $platillosNoDisponibles[$platilloId] = 'Disponible';
    }
}

// Actualizar precios y estado de los platillos
foreach ($platillosNoDisponibles as $id => $estado) {
    if (isset($preciosPlatillos[$id])) {
        $precio = floatval($preciosPlatillos[$id]);
        $updatePrecio = $conn->prepare("UPDATE Platillos SET estado = ?, precio = ? WHERE id_platillo = ?");
        $updatePrecio->bind_param("sdi", $estado, $precio, $id);
        $updatePrecio->execute();
    }
}

// Obtener todos los IDs de las mesas
$resultMesas = $conn->query("SELECT id_mesa FROM Mesa");
$mesasTotales = [];
while ($row = $resultMesas->fetch_assoc()) {
    $mesasTotales[] = $row['id_mesa'];
}

// Si no se seleccionan mesas disponibles, marcamos todas como disponibles
$mesasDisponiblesArray = $mesasDisponibles > 0 ? array_slice($mesasTotales, 0, $mesasDisponibles) : $mesasTotales;

// Mesas que no están disponibles
$mesasNoDisponiblesArray = array_diff($mesasTotales, $mesasDisponiblesArray);

$mesasDisponiblesString = implode(",", $mesasDisponiblesArray);
$platillosNoDisponiblesArray = array_keys(array_filter($platillosNoDisponibles, function($estado) {
    return $estado === 'No Disponible';
}));

if (empty($platillosNoDisponiblesArray)) {
    $platillosNoDisponiblesString = "";
    $conn->query("UPDATE Platillos SET estado = 'Disponible'");
} else {
    $platillosNoDisponiblesString = implode(",", $platillosNoDisponiblesArray);
}

// Actualizar o insertar el estado del día
$result = $conn->query("SELECT * FROM Estado_Dia WHERE fecha = '$fecha'");
if ($result->num_rows > 0) {
    // Actualizar el registro existente
    $update = $conn->prepare("UPDATE Estado_Dia SET estado = 'Iniciado', mesas_disponibles = ?, platillos_no_disponibles = ?, hora_cierre = ? WHERE fecha = ?");
    $update->bind_param("ssss", $mesasDisponiblesString, $platillosNoDisponiblesString, $horaCierre, $fecha);
    $update->execute();
} else {
    // Insertar un nuevo registro
    $insert = $conn->prepare("INSERT INTO Estado_Dia (fecha, estado, mesas_disponibles, platillos_no_disponibles, hora_cierre) VALUES (?, 'Iniciado', ?, ?, ?)");
    $insert->bind_param("ssss", $fecha, $mesasDisponiblesString, $platillosNoDisponiblesString, $horaCierre);
    $insert->execute();
}

// Actualizar el estado de las mesas
if (!empty($mesasDisponiblesArray)) {
    $mesasIds = implode(",", $mesasDisponiblesArray);
    $conn->query("UPDATE Mesa SET estado = 'Disponible' WHERE id_mesa IN ($mesasIds)");
}

if (!empty($mesasNoDisponiblesArray)) {
    $mesasNoDisponiblesIds = implode(",", $mesasNoDisponiblesArray);
    $conn->query("UPDATE Mesa SET estado = 'En Espera' WHERE id_mesa IN ($mesasNoDisponiblesIds)");
}

// Redirigir al dashboard del metre
header("Location: metre.php");
exit();
?>