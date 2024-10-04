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

// Obtener los IDs de mesa y mesero desde el formulario (POST)
$mesa_id = isset($_GET['mesa_id']) ? intval($_GET['mesa_id']) : null;
$id_mesero = isset($_GET['id_mesero']) ? intval($_GET['id_mesero']) : null;

// Mostrar el ID de la mesa recibido
echo "ID de la mesa recibido: " . htmlspecialchars($mesa_id) . "<br>";

// Verificar si el carrito tiene artículos
$cart_items = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Contar las cantidades de cada producto en el carrito
$cart_count = [];
$total_amount = 0;

foreach ($cart_items as $item) {
    $product_id = $item['id'];
    $product_price = isset($item['price']) ? $item['price'] : 0;

    if (isset($cart_count[$product_id])) {
        $cart_count[$product_id]['quantity']++;
    } else {
        $cart_count[$product_id] = [
            'quantity' => 1,
            'price' => $product_price
        ];
    }
    $total_amount += $product_price;
}

// Obtener los IDs de los productos en el carrito
$cart_ids = array_keys($cart_count);

// Solo realizar la consulta si hay productos en el carrito
if (!empty($cart_ids)) {
    $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
    $sql = "SELECT id_platillo, nombre_platillo, precio FROM Platillos WHERE id_platillo IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $types = str_repeat('i', count($cart_ids));
    $stmt->bind_param($types, ...$cart_ids);
    $stmt->execute();
    $product_result = $stmt->get_result();

    $products = [];
    while ($row = $product_result->fetch_assoc()) {
        $products[$row['id_platillo']] = $row;
    }

    $stmt->close();
} else {
    $products = [];
}

// Calcular el monto total del pedido
foreach ($cart_count as $product_id => $item) {
    if (isset($products[$product_id])) {
        $total_amount += $products[$product_id]['precio'] * $item['quantity'];
    }
}

$tipo_pedido = ($mesa_id === 0) ? 'Para Llevar' : 'Para Servir';

// Buscar el id_detalle_mesero_mesa
$id_detalle_mesero_mesa = null;
if ($mesa_id > 0 || ($id_mesero !== null && $id_mesero > 0)) {
    $sql = "SELECT id_detalle_mesero_mesa FROM detalle_mesero_mesa 
            WHERE (id_usuario = ? OR id_mesa = ?) AND estado = 'activo' LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $id_mesero, $mesa_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $id_detalle_mesero_mesa = $row['id_detalle_mesero_mesa'];
    }
    $stmt->close();
}

// Crear consulta SQL para insertar el pedido
$sql = "INSERT INTO Pedido (id_detalle_mesero_mesa, total_cuenta, hora, fecha, estado, tipo) VALUES (?, ?, NOW(), CURDATE(), 'Recibido', ?)";
$stmt = $conn->prepare($sql);

// Si no hay detalle mesero mesa, se deja NULL
if ($id_detalle_mesero_mesa === null) {
    $id_detalle_mesero_mesa = null;
}

// Agregar el tipo de pedido
$stmt->bind_param('ids', $id_detalle_mesero_mesa, $total_amount, $tipo_pedido);
$stmt->execute();

if ($stmt->affected_rows === 0) {
    die("Error al insertar el pedido: " . $stmt->error);
}

$id_pedido = $stmt->insert_id; // Obtener el ID del pedido insertado
$stmt->close();

// Insertar los detalles del pedido
foreach ($cart_count as $product_id => $item) {
    if (isset($products[$product_id])) {
        $sql = "INSERT INTO Detalle_Pedido_Platillo (id_pedido, id_platillo, cantidad) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('iii', $id_pedido, $product_id, $item['quantity']);
        $stmt->execute();
        $stmt->close();
    }
}

// Actualizar el estado de la mesa a 'Ocupada' si se proporcionó el id_mesa y no es 0
if ($mesa_id !== null && $mesa_id > 0) {
    $sql = "UPDATE Mesa SET estado = 'Ocupada' WHERE id_mesa = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $mesa_id);
    $stmt->execute();
    $stmt->close();
}

// Limpiar el carrito después de crear el pedido
unset($_SESSION['carrito']);

// Cerrar la conexión
$conn->close();

// Redirigir a la página de estado del pedido
header("Location: ver_estado.php?id=" . $id_pedido);
exit();
?>