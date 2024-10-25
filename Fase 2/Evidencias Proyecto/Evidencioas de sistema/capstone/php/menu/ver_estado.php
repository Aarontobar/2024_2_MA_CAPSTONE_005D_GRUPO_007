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
$tipo = ''; // Variable para almacenar el tipo de pedido
if ($order_id > 0) {
    $sql = "SELECT id_pedido, total_cuenta, tipo FROM Pedido WHERE id_pedido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order_info = $result->fetch_assoc();
        $tipo = $order_info['tipo']; // Obtener el tipo de pedido
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
    <title>Progreso del Pedido</title>
    <link rel="stylesheet" href="../../css/ver_estado.css">
    <style>
        /* Estilos para el botón */
        .action-button {
            display: none; /* Ocultar por defecto */
            margin-top: 20px;
            padding: 10px 20px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <h1>Progreso pedido</h1>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Progreso de su Pedido</h2>
        <img id="status-image" src="../../imagenes/banner_acompañamientos.jpg" alt="Estado del Pedido">
        <!-- Botón de acción -->
        <button id="action-button" class="action-button" onclick="handleAction()"></button>
        <br>
        <!-- Progress bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div></div>
            </div>
            <div class="progress-text">0%</div>
        </div>
        
        <!-- Order status -->
        <div class="order-status">
            <ul>
                <li>
                    <img src="../../imagenes/bill.png" alt="Order Sent Icon">
                    <p>Pedido recibido</p>
                    <span class="time">2022-01-01 12:00:00</span>
                </li>
                <li>
                    <img src="../../imagenes/cooking.png" alt="Order Confirmed Icon">
                    <p>Estamos preparando tu pedido</p>
                    <span class="time">2022-01-01 12:30:00</span>
                </li>
                <li>
                    <img src="../../imagenes/serving.png" alt="Delivery In Progress Icon">
                    <p>Tu pedido está preparado</p>
                    <span class="time">2022-01-01 13:00:00</span>
                </li>
                <li>
                    <img src="../../imagenes/recivido.png" alt="Delivered Icon">
                    <p>Ya recibiste tu pedido</p>
                    <span class="time">2022-01-01 13:30:00</span>
                </li>
            </ul>
        </div>
    </div>

    <script>
        // Función para obtener el parámetro 'id' de la URL
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        let lastState = ''; // Variable para almacenar el último estado

        // Función para obtener un gif aleatorio de una carpeta específica
        function getRandomGifPath(folder) {
            const gifCount = 5; // Supón que hay un número conocido de gifs en cada carpeta
            const randomIndex = Math.floor(Math.random() * gifCount) + 1;
            return `../../gifs/${folder}/gif${randomIndex}.gif`;
        }

        // Función para actualizar la imagen del estado y los estilos
        function updateStatusImage(orderId) {
            fetch(`estado.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    // Verificar si el estado ha cambiado
                    if (data.estado !== lastState) {
                        lastState = data.estado; // Actualizar el último estado

                        let imageUrl = '../imagenes/banner_acompañamientos.jpg'; // Imagen predeterminada
                        let progressPercentage = 0; // Progreso predeterminado
                        let currentStatusIndex = 0; // Índice del estado actual

                        // Restablecer estilos anteriores
                        document.querySelectorAll('.order-status li p').forEach(el => {
                            el.style.fontSize = '';
                            el.style.color = '';
                        });

                        // Manejo del botón de acción
                        const actionButton = document.getElementById('action-button');
                        switch (data.estado) {
                            case 'recibido':
                                imageUrl = getRandomGifPath('recibido');
                                progressPercentage = 25;
                                currentStatusIndex = 0;
                                actionButton.style.display = 'none'; // Ocultar botón
                                break;
                            case 'en preparación':
                                imageUrl = getRandomGifPath('en_preparacion');
                                progressPercentage = 50;
                                currentStatusIndex = 1;
                                actionButton.style.display = 'none'; // Ocultar botón
                                break;
                            case 'preparado':
                                imageUrl = getRandomGifPath('preparado');
                                progressPercentage = 75;
                                currentStatusIndex = 2;
                                actionButton.style.display = 'block'; // Mostrar botón
                                actionButton.innerText = '<?php echo $tipo === "Para Llevar" ? "Dejar Reseña" : "Pagar"; ?>'; // Cambiar texto del botón
                                break;
                            case 'servido':
                                imageUrl = getRandomGifPath('servido');
                                progressPercentage = 100;
                                currentStatusIndex = 3;
                                actionButton.style.display = 'block'; // Mostrar botón
                                actionButton.innerText = '<?php echo $tipo === "Para Llevar" ? "Dejar Reseña" : "Pagar"; ?>'; // Cambiar texto del botón
                                break;
                        }

                        // Actualizar imagen
                        document.getElementById('status-image').src = imageUrl;

                        // Actualizar barra de progreso
                        const progressBar = document.querySelector('.progress-bar div');
                        progressBar.style.width = progressPercentage + '%';
                        document.querySelector('.progress-text').innerText = progressPercentage + '%';

                        // Cambiar el estilo del estado actual
                        const currentStatus = document.querySelectorAll('.order-status li')[currentStatusIndex].querySelector('p');
                        currentStatus.style.fontSize = '1.5em'; // Aumentar tamaño del texto
                        currentStatus.style.color = 'green'; // Cambiar color a verde

                        // Mostrar estado en la consola
                        console.log(`Estado del pedido: ${data.estado}`);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Función para manejar la acción del botón
        function handleAction() {
            const orderId = getQueryParam('id');
            const tipoPedido = "<?php echo $tipo; ?>"; // Obtener el tipo de pedido

            if (tipoPedido === "Para Llevar") {
                // Redirigir a la página de dejar reseña
                window.location.href = `../cliente/reseña.php?id=${orderId}`; // Cambia 'dejar_reseña.php' por el nombre real de tu página de reseña
            } else {
                // Redirigir a la página de pago
                window.location.href = `pagar.php?id=${orderId}`;
            }
        }

        // Obtener el ID del pedido de la URL y actualizar el estado
        const orderId = getQueryParam('id');
        if (orderId) {
            updateStatusImage(orderId);
            setInterval(() => updateStatusImage(orderId), 5000); // Actualiza cada 5 segundos
        } else {
            console.error('No se proporcionó un ID de pedido en la URL.');
        }
    </script>
</body>
</html>