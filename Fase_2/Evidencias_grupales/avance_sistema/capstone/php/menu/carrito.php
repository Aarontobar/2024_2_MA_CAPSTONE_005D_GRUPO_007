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

// Verificar si el carrito tiene artículos
$cart_items = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

// Contar las cantidades de cada producto en el carrito
$cart_count = [];
$total = 0; // Total general

foreach ($cart_items as $item) {
    $product_id = $item['id'];
    $product_price = isset($item['price']) ? $item['price'] : 0; // Asegúrate de que el precio esté disponible

    // Acumular la cantidad de cada producto
    if (isset($cart_count[$product_id])) {
        $cart_count[$product_id]['quantity']++;
    } else {
        $cart_count[$product_id] = [
            'quantity' => 1,
            'price' => $product_price
        ];
    }
}

// Obtener los IDs de los productos en el carrito
$cart_ids = array_keys($cart_count);

// Solo realizar la consulta si hay productos en el carrito
if (!empty($cart_ids)) {
    // Traer la información completa de los productos desde la base de datos
    $placeholders = implode(',', array_fill(0, count($cart_ids), '?'));
    $sql = "SELECT id_platillo, nombre_platillo, descripcion_platillo, precio, ruta_foto FROM Platillos WHERE id_platillo IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    // Ligamos los parámetros
    $types = str_repeat('i', count($cart_ids));
    $stmt->bind_param($types, ...$cart_ids);
    $stmt->execute();
    $result = $stmt->get_result();

    // Guardar los datos de los productos en un array
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[$row['id_platillo']] = $row;
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    $products = [];
}

// Cerrar la conexión
$conn->close();

// Obtener los IDs de mesa y mesero si están disponibles
$mesa_id = isset($_GET['id_mesa']) ? intval($_GET['id_mesa']) : null;
$id_mesero = isset($_GET['id_mesero']) ? intval($_GET['id_mesero']) : null;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito</title>
    <link rel="stylesheet" href="../../css/ver_menu.css">
    <script src="https://www.paypal.com/sdk/js?client-id=AUD_x5nmUmrip9LqstY_CsPhj4gIxyf_c4C98xCmkluVCTupFrIOd2Q5Soinn_OF-r4Hl6rHJodJsuVJ"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Detalle Orden</div>
        </nav>
    </header>

    <main>
        <h1>Carrito</h1>
        <?php if (empty($cart_items)) : ?>
            <p>No hay productos en el carrito.</p>
        <?php else : ?>
            <ul>
                <?php foreach ($cart_count as $id => $item) : ?>
                    <?php if (isset($products[$id])): ?>
                        <li>
                            <img src="<?php echo htmlspecialchars($products[$id]['ruta_foto']); ?>" alt="<?php echo htmlspecialchars($products[$id]['nombre_platillo']); ?>" style="width: 100px; height: auto;">
                            <strong><?php echo htmlspecialchars($products[$id]['nombre_platillo']); ?></strong> - 
                            <?php echo htmlspecialchars($products[$id]['descripcion_platillo']); ?> - 
                            Cantidad: <?php echo htmlspecialchars($item['quantity']); ?> - 
                            Precio Unitario: $<?php echo htmlspecialchars(number_format($products[$id]['precio'], 2, '.', '')); ?> - 
                            Total: $<?php echo htmlspecialchars(number_format($products[$id]['precio'] * $item['quantity'], 2, '.', '')); ?>
                        </li>
                        <?php 
                        // Sumar al total general
                        $total += $products[$id]['precio'] * $item['quantity'];
                        ?>
                    <?php else: ?>
                        <li>Producto con ID <?php echo htmlspecialchars($id); ?> no encontrado en la base de datos.</li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>

            <!-- Muestra el monto total a pagar -->
            <h2>Total: $<?php echo htmlspecialchars(number_format($total, 2, '.', '')); ?></h2>

            <!-- Botón de PayPal -->
            <div id="paypal-button-container"></div>
            <script>
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '<?php echo number_format($total, 2, '.', ''); ?>' // Monto total
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            alert('Transacción completada por ' + details.payer.name.given_name);
                            // Aquí puedes redirigir a otra página o guardar la información del pedido
                            window.location.href = 'generar_pedido.php?orderId=' + data.orderID + '&mesa_id=<?php echo htmlspecialchars($mesa_id); ?>&id_mesero=<?php echo htmlspecialchars($id_mesero); ?>';
                        });
                    },
                    onError: function(err) {
                        console.error(err);
                        alert('Ocurrió un error durante el proceso de pago. Intenta nuevamente.');
                    }
                }).render('#paypal-button-container');
            </script>
        <?php endif; ?>
    </main>
</body>
</html>