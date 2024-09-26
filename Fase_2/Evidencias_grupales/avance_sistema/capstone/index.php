<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            min-height: 100vh; /* Altura mínima para el body */
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #1f1f1f;
        }

        header h1 {
            margin: 0;
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            color: #ffffff;
            text-decoration: none;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #ffcc00;
        }

        .container {
            padding: 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex: 1; /* Permite que el contenedor crezca y ocupe el espacio disponible */
        }

        .text-left {
            flex: 1;
            padding: 20px;
        }

        .text-left img {
            max-width: 100%;
            height: auto;
        }

        .text-right {
            flex: 1;
            padding: 20px;
        }

        footer {
            background-color: #1f1f1f;
            padding: 20px;
            text-align: center;
        }

        footer .social-icons a {
            color: #ffffff;
            margin: 0 10px;
            font-size: 1.5em;
            transition: color 0.3s;
        }

        footer .social-icons a:hover {
            color: #ffcc00;
        }
    </style>
</head>
<body>

<header>
    <h1><a href="index.php" style="color: #ffffff; text-decoration: none;">Restaurante</a></h1>
    <nav>
        <a href="php/restaurante/nosotros.php">Nosotros</a>
        <a href="php/restaurante/reseñas.php">Reseñas</a>
        <a href="php/menu/ver_menu.php">Pedir</a>
        <a href="php/login/login.php">Trabajadores</a>
    </nav>
</header>

<div class="container">
    <div class="text-left">
        <h2>Bienvenidos a Nuestro Restaurante</h2>
        <p>
            En nuestro restaurante, ofrecemos una experiencia culinaria única que combina lo mejor de la cocina local
            con un ambiente acogedor y moderno. Nuestro equipo de chefs está dedicado a utilizar ingredientes frescos y
            de alta calidad para crear platos deliciosos que satisfacen todos los paladares.
        </p>
    </div>
    <div class="text-right">
        <img src="imagenes/restaurante.jpg" alt="Comida del Restaurante">
    </div>
</div>

<div class="container">
    <div class="text-right">
        <img src="imagenes/imagen_ambiente.jpg" alt="Ambiente del Restaurante">
    </div>
    <div class="text-left">
        <h3>Nuestra Filosofía</h3>
        <p>
            Creemos en ofrecer no solo buena comida, sino también un ambiente donde cada cliente se sienta como en casa.
            Ya sea que desees un almuerzo ligero o una cena exquisita, tenemos algo especial para ti.
        </p>
    </div>
</div>

<footer>
    <div class="social-icons">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-twitter"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-yelp"></i></a>
    </div>
</footer>

</body>
</html>