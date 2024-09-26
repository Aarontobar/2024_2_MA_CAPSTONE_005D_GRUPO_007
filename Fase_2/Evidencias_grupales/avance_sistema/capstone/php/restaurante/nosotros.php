<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros - Restaurante</title>
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

        .text-right img {
            max-width: 100%;
            height: auto;
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
    <h1><a href="../../index.php" style="color: #ffffff; text-decoration: none;">Restaurante</a></h1>
    <nav>
        <a href="php/restaurante/nosotros.php">Nosotros</a>
        <a href="reseñas.php">Reseñas</a>
        <a href="../menu/ver_menu.php">Pedir</a>
        <a href="../login/login.php">Trabajadores</a>
    </nav>
</header>

<!-- Sección Historia -->
<div class="container" id="historia">
    <div class="text-left">
        <h2>Nuestra Historia</h2>
        <p>
            El restaurante fue fundado hace más de 20 años con el objetivo de crear un lugar acogedor para disfrutar de la mejor comida
            local. A lo largo de los años, hemos crecido y evolucionado, pero siempre manteniendo nuestras raíces en la calidad y el
            servicio. Nuestros clientes nos han apoyado desde el principio, y hoy en día seguimos siendo un referente en la ciudad.
        </p>
    </div>
    <div class="text-right">
        <img src="../../imagenes/imagen_nosotros1.jpg" alt="Historia del Restaurante">
    </div>
</div>

<!-- Sección Misión -->
<div class="container" id="mision">
    <div class="text-right">
        <img src="../../imagenes/imagen_nosotros2.jpg" alt="Misión del Restaurante">
    </div>
    <div class="text-left">
        <h3>Nuestra Misión</h3>
        <p>
            Nuestra misión es ofrecer una experiencia culinaria única, combinando ingredientes frescos de la más alta calidad con un
            servicio excepcional. Creemos en crear platos que no solo satisfacen el apetito, sino que también dejan una impresión
            duradera en nuestros clientes.
        </p>
    </div>
</div>

<!-- Sección Visión -->
<div class="container" id="vision">
    <div class="text-left">
        <h2>Nuestra Visión</h2>
        <p>
            Queremos ser reconocidos como el mejor restaurante de la región, donde la innovación y la tradición se fusionan para crear
            experiencias memorables. Nuestro objetivo es continuar creciendo, expandiendo nuestra oferta y manteniéndonos a la vanguardia
            de la gastronomía local.
        </p>
    </div>
    <div class="text-right">
        <img src="../../imagenes/imagen_nosotros3.jpg" alt="Visión del Restaurante">
    </div>
</div>

<!-- Footer -->
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