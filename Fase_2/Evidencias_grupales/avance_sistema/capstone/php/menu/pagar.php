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

// Obtener el ID del pedido de la URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Recuperar la información del pedido usando el ID
$order_info = [];
if ($order_id > 0) {
    $sql = "SELECT id_pedido, total_cuenta FROM Pedido WHERE id_pedido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order_info = $result->fetch_assoc();
    } else {
        die("No se encontró el pedido.");
    }
    $stmt->close();
}

// Obtener los IDs de mesa y mesero si están disponibles
$mesa_id = isset($_GET['id_mesa']) ? intval($_GET['id_mesa']) : null;
$id_mesero = isset($_GET['id_mesero']) ? intval($_GET['id_mesero']) : null;

$total_cuenta = $order_info['total_cuenta'] ?? 0; // total_cuenta del pedido recuperado

// Cerrar la conexión
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagar Pedido</title>
    <link rel="stylesheet" href="../../css/pagar.css">
    <script src="https://www.paypal.com/sdk/js?client-id=AUD_x5nmUmrip9LqstY_CsPhj4gIxyf_c4C98xCmkluVCTupFrIOd2Q5Soinn_OF-r4Hl6rHJodJsuVJ"></script>
</head>
<body>
    <header>
        <nav>
            <div class="logo">Detalle del Pedido</div>
        </nav>
    </header>

    <main>
        <h1>Pagar Pedido</h1>
        
        <?php if (empty($order_info)) : ?>
            <p>No se encontró información del pedido.</p>
        <?php else : ?>
            <div class="total-cuenta">Total a Pagar: $<?php echo htmlspecialchars(number_format($total_cuenta, 2, '.', '')); ?></div>

            <!-- Botón de PayPal -->
            <div id="paypal-button-container"></div>
            <script>
                paypal.Buttons({
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{
                                amount: {
                                    value: '<?php echo number_format($total_cuenta, 2, '.', ''); ?>' // Monto total_cuenta
                                }
                            }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            alert('Transacción completada por ' + details.payer.name.given_name);

                            // Marcar el pedido como completado en la base de datos usando AJAX
                            fetch('marcar_pedido_completado.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    id_pedido: '<?php echo $order_id; ?>'
                                })
                            }).then(response => {
                                if (response.ok) {
                                    // Redirigir a la página de generar QR solo con mesa_id
                                    window.location.href = 'generar_qr.php?mesa_id=<?php echo htmlspecialchars($mesa_id); ?>';
                                }
                            }).catch(error => {
                                console.error('Error al marcar el pedido como completado:', error);
                            });
                        });
                    },
                    onError: function(err) {
                        console.error(err);
                        alert('Ocurrió un error durante el proceso de pago. Intenta nuevamente.');
                    }
                }).render('#paypal-button-container');
            </script>

            <!-- Opción de pago en efectivo -->
            <div class="payment-option">
                <p>Puedes pagar en efectivo</p>
                <form action="marcar_pedido_completado.php" method="POST">
                    <input type="hidden" name="mesa_id" value="<?php echo htmlspecialchars($mesa_id); ?>">
                    <input type="hidden" name="id_pedido" value="<?php echo htmlspecialchars($order_id); ?>">
                    <input type="hidden" name="pago_efectivo" value="1">
                    <button type="submit" class="efectivo-button">Confirmar Pago en Efectivo</button>
                </form>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>