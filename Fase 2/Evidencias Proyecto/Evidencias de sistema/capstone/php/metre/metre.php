<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $conn->connect_error]);
    exit();
}

// Establecer la zona horaria a Santiago, Chile
date_default_timezone_set('America/Santiago');

// Verificar el estado del día
$fecha = date('Y-m-d');
$result = $conn->query("SELECT * FROM Estado_Dia WHERE fecha = '$fecha'");
$estadoDia = $result->fetch_assoc();

if (!$estadoDia || $estadoDia['estado'] != 'Iniciado') {
    header("Location: index.php");
    exit();
}

// Consultar Pedidos (mostramos solo los primeros 5)
$pedidos = $conn->query("SELECT id_pedido, estado FROM Pedido WHERE fecha = '$fecha' LIMIT 5");

// Consultar Platillos (mostramos solo los primeros 5)
$platillos = $conn->query("SELECT nombre_platillo, estado FROM Platillos LIMIT 5");

// Consultar Reservas (mostramos solo las primeras 5)
$reservas = $conn->query("SELECT nombre_reserva, apellido_reserva, fecha, id_mesa FROM Reserva WHERE fecha >= '$fecha' LIMIT 5");

// Consultar Mesas (mostramos solo las primeras 5)
$mesas = $conn->query("SELECT id_mesa, estado FROM Mesa LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard del Metre</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .dashboard-module {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Restaurante Nombre</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="ver_mas.php?tipo=pedidos">Pedidos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ver_mas.php?tipo=mesas">Mesas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ver_mas.php?tipo=reservas">Reservaciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ver_mas.php?tipo=platillos">Platillos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M10 15a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V1a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v1h1V1a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-1h-1v1z"/>
                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L14.293 8H5.5a.5.5 0 0 0 0 1h8.793l-2.647 2.646a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="mb-4">Dashboard del Metre</h1>

    <div class="row">
        <!-- Lista de Pedidos -->
        <div class="col-md-4">
            <div class="dashboard-module">
                <h5>Lista de Pedidos</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Pedido</th>
                            <th>Estado</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($pedido = $pedidos->fetch_assoc()): ?>
                        <tr>
                            <td><?= $pedido['id_pedido'] ?></td>
                            <td><?= $pedido['estado'] ?></td>
                            <td><button class="btn btn-link">Ver</button></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="ver_mas.php?tipo=pedidos" class="btn btn-primary">Ver más</a>
            </div>
        </div>

        <!-- Platillos -->
        <div class="col-md-4">
            <div class="dashboard-module">
            <h5>Platillos</h5>
            <table class="table">
                <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Disponibilidad</th>
                </tr>
                </thead>
                <tbody>
                <?php while($platillo = $platillos->fetch_assoc()): ?>
                <tr>
                    <td><?= $platillo['nombre_platillo'] ?></td>
                    <td>
                    <select class="form-select" onchange="cambiarEstadoPlatillo('<?= $platillo['nombre_platillo'] ?>', this.value)">
                        <option <?= $platillo['estado'] == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                        <option <?= $platillo['estado'] == 'No Disponible' ? 'selected' : '' ?>>No Disponible</option>
                    </select>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <a href="ver_mas.php?tipo=platillos" class="btn btn-primary">Ver más</a>
            </div>
        </div>

        <script>
            function cambiarEstadoPlatillo(nombrePlatillo, nuevoEstado) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'cambiar_estado_platillo.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
                }
            };
            xhr.send('nombre_platillo=' + encodeURIComponent(nombrePlatillo) + '&estado=' + encodeURIComponent(nuevoEstado));
            }
        </script>

        <!-- Reservas -->
        <div class="col-md-4">
            <div class="dashboard-module">
                <h5>Reservas</h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th>Mesas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($reserva = $reservas->fetch_assoc()): ?>
                        <tr>
                            <td><?= $reserva['nombre_reserva'] . ' ' . $reserva['apellido_reserva'] ?></td>
                            <td><?= $reserva['fecha'] ?></td>
                            <td><?= $reserva['id_mesa'] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="ver_mas.php?tipo=reservas" class="btn btn-primary">Ver más</a>
                <a href="crear_reserva.php" class="btn btn-success">Crear Reserva</a>
            </div>
        </div>
    </div>

    <!-- Estado de Mesas -->
    <div class="dashboard-module">
        <h5>Estado de Mesas</h5>
        <table class="table">
            <thead>
                <tr>
                    <th>ID Mesa</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php while($mesa = $mesas->fetch_assoc()): ?>
                <tr>
                    <td><?= $mesa['id_mesa'] ?></td>
                    <td><?= $mesa['estado'] ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="ver_mas.php?tipo=mesas" class="btn btn-primary">Ver más</a>
    </div>

    <?php include '../modulos/Chat.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>