<?php
// Conexión a la base de datos
$conexion = new mysqli('localhost', 'root', '', 'restaurante_bd');

if ($conexion->connect_error) {
    die('Error en la conexión: ' . $conexion->connect_error);
}

// Obtener las reseñas para el promedio
$sqlPromedio = "SELECT calificacion FROM Reseñas";
$resultPromedio = $conexion->query($sqlPromedio);

$totalCalificaciones = 0;
$numeroReseñas = 0;

if ($resultPromedio->num_rows > 0) {
    while ($row = $resultPromedio->fetch_assoc()) {
        $totalCalificaciones += $row['calificacion'];
        $numeroReseñas++;
    }
}

// Calcular el promedio
$promedioCalificacion = $numeroReseñas > 0 ? $totalCalificaciones / $numeroReseñas : 0;
$promedioCalificacion = round($promedioCalificacion, 1); // Redondear a 1 decimal

// Obtener las últimas 10 reseñas
$sqlUltimasReseñas = "SELECT nombre_cliente, apellido_cliente, calificacion, comentario FROM Reseñas ORDER BY id_reseña DESC LIMIT 10";
$resultUltimasReseñas = $conexion->query($sqlUltimasReseñas);

$reseñas = [];

if ($resultUltimasReseñas->num_rows > 0) {
    while ($row = $resultUltimasReseñas->fetch_assoc()) {
        $reseñas[] = $row;
    }
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Restaurante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="../../css/reseñas.css">
    <style>
        .average-rating {
            font-size: 24px;
            color: #FFD700; /* Color dorado */
            display: flex;
            align-items: center;
        }
        .average-rating .stars {
            margin-left: 10px;
        }
        .average-rating p {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="logo">
            <img src="../../imagenes/logo.png" alt="Logo del Restaurante" />
        </div>
        <div class="nav-links">
            <a href="../../index.php">Inicio</a>
            <a href="nosotros.php">Nosotros</a>
            <a href="reseñas.php">Reseñas</a>
            <a href="../menu/ver_menu.php">Pedir</a>
        </div>
        <a class="sign-in" href="../login/login.php">Trabajadores</a>
    </div>
    <div class="container">
        <h2 class="average-rating">
            Promedio de Calificación: 
            <span class="stars">
                <?php 
                // Mostrar estrellas del promedio
                echo str_repeat('★', floor($promedioCalificacion)) . str_repeat('☆', 5 - floor($promedioCalificacion)); 
                ?>
            </span>
        </h2>
        <p>Calificación promedio: <?php echo $promedioCalificacion; ?></p>

        <!-- Mostrar las últimas 10 reseñas -->
        <?php foreach ($reseñas as $index => $reseña): ?>
            <div class="review">
                <img src="https://randomuser.me/api/portraits/men/<?php echo $index + 1; ?>.jpg" alt="Cliente">
                <div class="review-content">
                    <h3><?php echo $reseña['nombre_cliente'] . ' ' . $reseña['apellido_cliente']; ?></h3>
                    <p>"<?php echo $reseña['comentario']; ?>"</p>
                    <div class="stars">
                        <?php 
                        // Mostrar estrellas de la reseña
                        echo str_repeat('★', floor($reseña['calificacion'])) . 
                             str_repeat('☆', 5 - floor($reseña['calificacion'])); 
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="footer">
        <div class="footer-links">
            <a href="#">Nosotros</a>
            <a href="#">Nuestro menú</a>
        </div>
        <div class="social-icons">
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-tiktok"></i></a>
        </div>
        <p>© 2024 Restaurante. Todos los derechos reservados.</p>
    </div>
</body>
</html>