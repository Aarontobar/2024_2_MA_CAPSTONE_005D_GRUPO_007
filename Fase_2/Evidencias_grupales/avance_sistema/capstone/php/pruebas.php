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
// Obtener el ID del pedido desde la URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($order_id > 0) {
    // Consulta para obtener el estado del pedido
    $query = "SELECT estado FROM Pedido WHERE id_pedido = $order_id";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $estado = $row['estado'];
        echo json_encode(['estado' => $estado]);
    } else {
        echo json_encode(['error' => 'Pedido no encontrado']);
    }
} else {
    echo json_encode(['error' => 'ID de pedido inválido']);
}

// Cerrar la conexión
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso del Pedido</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        /* Navbar */
        .navbar {
            background-color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .navbar img {
            height: 30px;
            margin-right: 10px;
        }
        
        .navbar h1 {
            font-size: 20px;
            margin: 0;
        }
        
        /* Main content */
        .main-content {
            text-align: center;
            padding: 20px;
        }
        
        #status-image {
            width: 80%;
            height: auto;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Progress bar */
        .progress-container {
            width: 80%;
            margin: 20px auto;
        }
        
        .progress-bar {
            height: 10px;
            background-color: #ccc;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }
        
        .progress-bar div {
            height: 100%;
            width: 75%; /* Placeholder percentage */
            background-color: #28a745;
            position: absolute;
            top: 0;
            left: 0;
        }
        
        .progress-text {
            text-align: right;
            margin-top: 5px;
            font-size: 14px;
            color: #555;
        }
        
        /* Order status */
        .order-status {
            width: 80%;
            margin: 20px auto;
            text-align: left;
        }
        
        .order-status ul {
            list-style: none;
            padding: 0;
        }
        
        .order-status ul li {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }
        
        .order-status ul li img {
            height: 20px;
            margin-right: 10px;
        }
        
        .order-status ul li p {
            margin: 0;
        }
        
        .order-status ul li .time {
            margin-left: auto;
            color: #888;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <img src="logo.png" alt="Restaurant Logo">
        <h1>Restaurant Name</h1>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h2>Progreso de su Pedido</h2>
        <img id="status-image" src="../../imagenes/banner_acompañamientos.jpg" alt="Estado del Pedido">
        
        <!-- Progress bar -->
        <div class="progress-container">
            <div class="progress-bar">
                <div></div>
            </div>
            <div class="progress-text">75%</div>
        </div>
        
        <!-- Order status -->
        <div class="order-status">
            <ul>
                <li>
                    <img src="order-sent-icon.png" alt="Order Sent Icon">
                    <p>Order sent</p>
                    <span class="time">2022-01-01 12:00:00</span>
                </li>
                <li>
                    <img src="order-confirmed-icon.png" alt="Order Confirmed Icon">
                    <p>Restaurant confirmed order and started working on it</p>
                    <span class="time">2022-01-01 12:30:00</span>
                </li>
                <li>
                    <img src="delivery-icon.png" alt="Delivery In Progress Icon">
                    <p>Delivery in progress</p>
                    <span class="time">2022-01-01 13:00:00</span>
                </li>
                <li>
                    <img src="delivered-icon.png" alt="Delivered Icon">
                    <p>Delivered</p>
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

        // Función para actualizar la imagen del estado
        function updateStatusImage(orderId) {
            fetch(`menu/estado.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error('Error:', data.error);
                        return;
                    }

                    let imageUrl = '../imagenes/banner_acompañamientos.jpg'; // Imagen predeterminada
                    let progressPercentage = 0; // Progreso predeterminado
                    switch (data.estado) {
                        case 'En Preparación':
                            imageUrl = '../imagenes/banner_bebidas.jpg';
                            progressPercentage = 25;
                            break;
                        case 'En Cocina':
                            imageUrl = '../imagenes/banner_entradas.jpeg';
                            progressPercentage = 50;
                            break;
                        case 'Listo':
                            imageUrl = '../imagenes/estado_listo.png';
                            progressPercentage = 75;
                            break;
                        case 'Servido':
                            imageUrl = '../imagenes/estado_entregado.png';
                            progressPercentage = 100;
                            break;
                    }

                    // Actualizar imagen
                    document.getElementById('status-image').src = imageUrl;

                    // Actualizar barra de progreso
                    const progressBar = document.querySelector('.progress-bar div');
                    progressBar.style.width = progressPercentage + '%';
                    document.querySelector('.progress-text').innerText = progressPercentage + '%';

                    // Mostrar estado en la consola
                    console.log(`Estado del pedido: ${data.estado}`);
                })
                .catch(error => console.error('Error:', error));
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