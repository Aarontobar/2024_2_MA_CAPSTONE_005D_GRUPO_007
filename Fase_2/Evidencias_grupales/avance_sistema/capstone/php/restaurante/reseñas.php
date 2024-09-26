<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reseñas - Restaurante</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #ffffff;
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
            flex-direction: column;
            gap: 20px;
        }

        .review {
            background-color: #1f1f1f;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .review img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        .review-content {
            flex: 1;
        }

        .stars {
            color: #ffcc00;
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
        <a href="nosotros.php">Nosotros</a>
        <a href="#reseñas">Reseñas</a>
        <a href="../menu/ver_menu.php">Pedir</a>
        <a href="../login/login.php">Trabajadores</a>
    </nav>
</header>

<div class="container">
    <h2>Reseñas de Nuestros Clientes</h2>

    <!-- Reseñas -->
    <div class="review">
        <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Cliente 1">
        <div class="review-content">
            <h3>Juan Pérez</h3>
            <p>"La comida es deliciosa y el servicio es excepcional. Definitivamente volveré!"</p>
            <div class="stars">
                ★★★★★
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="Cliente 2">
        <div class="review-content">
            <h3>María González</h3>
            <p>"Un ambiente acogedor y platos sorprendentes. ¡Lo recomiendo al 100%!"</p>
            <div class="stars">
                ★★★★☆
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Cliente 3">
        <div class="review-content">
            <h3>Pedro Rodríguez</h3>
            <p>"Me encantó la variedad del menú y la atención al cliente fue increíble."</p>
            <div class="stars">
                ★★★★☆
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/women/2.jpg" alt="Cliente 4">
        <div class="review-content">
            <h3>Ana López</h3>
            <p>"Un lugar perfecto para disfrutar de una cena especial. ¡Simplemente increíble!"</p>
            <div class="stars">
                ★★★★★
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/men/3.jpg" alt="Cliente 5">
        <div class="review-content">
            <h3>Carlos Martínez</h3>
            <p>"Los precios son razonables para la calidad que ofrecen. ¡Muy recomendable!"</p>
            <div class="stars">
                ★★★★☆
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/women/3.jpg" alt="Cliente 6">
        <div class="review-content">
            <h3>Lucía Torres</h3>
            <p>"Un servicio rápido y amable. Los platos estaban bien presentados y sabrosos."</p>
            <div class="stars">
                ★★★★★
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/men/4.jpg" alt="Cliente 7">
        <div class="review-content">
            <h3>Javier Hernández</h3>
            <p>"El mejor restaurante en el que he estado. Cada visita es una experiencia única."</p>
            <div class="stars">
                ★★★★★
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/women/4.jpg" alt="Cliente 8">
        <div class="review-content">
            <h3>Clara Fernández</h3>
            <p>"El ambiente es increíble y la comida nunca decepciona. ¡Volveré pronto!"</p>
            <div class="stars">
                ★★★★☆
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/men/5.jpg" alt="Cliente 9">
        <div class="review-content">
            <h3>Diego Ríos</h3>
            <p>"Todo estaba delicioso y el personal fue muy atento. ¡Recomiendo el postre!"</p>
            <div class="stars">
                ★★★★★
            </div>
        </div>
    </div>

    <div class="review">
        <img src="https://randomuser.me/api/portraits/women/5.jpg" alt="Cliente 10">
        <div class="review-content">
            <h3>Valentina Castro</h3>
            <p>"Una experiencia maravillosa en un lugar encantador. No puedo esperar para volver."</p>
            <div class="stars">
                ★★★★★
            </div>
        </div>
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