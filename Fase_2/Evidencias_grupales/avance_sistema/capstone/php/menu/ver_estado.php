<?php
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progreso del Pedido</title>
    <link rel="stylesheet" href="../../css/ver_estado.css">
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
                    <img src="../../imagenes/bill.png" alt="Order Sent Icon">
                    <p>Pedido resivido</p>
                    <span class="time">2022-01-01 12:00:00</span>
                </li>
                <li>
                    <img src="../../imagenes/cooking.png" alt="Order Confirmed Icon">
                    <p>Estamos preparando tu pedido</p>
                    <span class="time">2022-01-01 12:30:00</span>
                </li>
                <li>
                    <img src="../../imagenes/serving.png" alt="Delivery In Progress Icon">
                    <p>Tu pedido se dirije a tu mesa</p>
                    <span class="time">2022-01-01 13:00:00</span>
                </li>
                <li>
                    <img src="../../imagenes/recivido.png" alt="Delivered Icon">
                    <p>Ya resiviste tu pedido</p>
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
            // Supón que hay un número conocido de gifs en cada carpeta
            const gifCount = 5; // Por ejemplo, si tienes 5 gifs en cada carpeta
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

                        switch (data.estado) {
                            case 'En Preparación':
                                imageUrl = getRandomGifPath('en_preparacion');
                                progressPercentage = 25;
                                currentStatusIndex = 1;
                                break;
                            case 'En Cocina':
                                imageUrl = getRandomGifPath('en_cocina');
                                progressPercentage = 50;
                                currentStatusIndex = 2;
                                break;
                            case 'Listo':
                                imageUrl = getRandomGifPath('listo');
                                progressPercentage = 75;
                                currentStatusIndex = 3;
                                break;
                            case 'Servido':
                                imageUrl = getRandomGifPath('servido');
                                progressPercentage = 100;
                                currentStatusIndex = 4;
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