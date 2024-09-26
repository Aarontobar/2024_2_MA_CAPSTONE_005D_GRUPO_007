<?php
session_start();
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
$fecha = date('Y-m-d'); // Obtener la fecha actual en la zona horaria correcta
$result = $conn->query("SELECT * FROM Estado_Dia WHERE fecha = '$fecha'");
$estadoDia = $result->fetch_assoc();

if (!$estadoDia || $estadoDia['estado'] != 'Iniciado') {
    header("Location: index.php");
    exit();
}

// Aquí puedes agregar el contenido del dashboard del metre
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
        .notification-bell {
            position: relative;
            cursor: pointer;
        }
        .notification-bell .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            font-size: 0.75rem;
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
                    <a class="nav-link" href="#">Pedidos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Mesas</a>
                </li>
                <li class="nav-item notification-bell" data-bs-toggle="tooltip" title="Notificaciones">
                    <a class="nav-link" href="#">
                        <i class="bi bi-bell"></i>
                        <span class="badge">3</span> <!-- Cambiar según las notificaciones -->
                    </a>
                </li>
                <li class="nav-item">
                    <button class="btn btn-danger" id="terminarDiaBtn">Terminar Día</button>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <h1 class="mb-4">Dashboard del Metre</h1>

    <div class="row">
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
                        <tr>
                            <td>1</td>
                            <td>En Preparación</td>
                            <td><button class="btn btn-link">Ver</button></td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Listo</td>
                            <td><button class="btn btn-link">Ver</button></td>
                        </tr>
                        <!-- Más filas según los pedidos -->
                    </tbody>
                </table>
            </div>
        </div>

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
                        <tr>
                            <td>Pizza</td>
                            <td>
                                <select class="form-select">
                                    <option>Disponible</option>
                                    <option>No Disponible</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>Ensalada</td>
                            <td>
                                <select class="form-select">
                                    <option>Disponible</option>
                                    <option>No Disponible</option>
                                </select>
                            </td>
                        </tr>
                        <!-- Más filas según los platillos -->
                    </tbody>
                </table>
            </div>
        </div>

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
                        <tr>
                            <td>Juan Pérez</td>
                            <td>2024-09-23</td>
                            <td>3</td>
                        </tr>
                        <tr>
                            <td>Ana Gómez</td>
                            <td>2024-09-24</td>
                            <td>2</td>
                        </tr>
                        <!-- Más filas según las reservas -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
                <tr>
                    <td>1</td>
                    <td>Disponible</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Ocupada</td>
                </tr>
                <!-- Más filas según las mesas -->
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
    document.getElementById('terminarDiaBtn').addEventListener('click', () => {
        // Aquí puedes agregar la lógica para terminar el día
        if (confirm("¿Estás seguro de que quieres terminar el día?")) {
            // Redirigir o ejecutar lógica para terminar el día
            alert("Día terminado."); // Esto es solo un ejemplo
        }
    });
</script>
</body>
</html>