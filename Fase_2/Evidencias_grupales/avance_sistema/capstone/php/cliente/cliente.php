<?php
session_start();

// Establecer conexión a la base de datos
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

// Obtener las mesas disponibles desde la base de datos
$sql = "SELECT id_mesa, cantidad_asientos, estado FROM Mesa WHERE estado = 'disponible'"; // Mostrar solo las mesas disponibles
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $mesas = [];
    while($row = $result->fetch_assoc()) {
        $mesas[] = $row;
    }
} else {
    $mesas = [];
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cliente - Restaurante</title>
    <link rel="stylesheet" href="css/sistema_gestion.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        h1 {
            text-align: center;
            color: #333;
            padding: 20px;
        }
        nav {
            background-color: #333;
            padding: 10px;
        }
        nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        nav ul li {
            margin: 0 15px;
        }
        nav ul li a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }
        .menu {
            display: flex;
            justify-content: center;
            margin: 20px;
        }
        .menu a {
            display: block;
            padding: 15px 25px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .menu a:hover {
            background-color: #0056b3;
        }
        main {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        ul li a {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        ul li a:hover {
            text-decoration: underline;
        }
        .no-mesas {
            text-align: center;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Bienvenido Cliente - Restaurante</h1>

    <main>
        <h2>Selecciona una mesa para hacer una orden:</h2>
        <ul>
            <?php if (count($mesas) > 0) : ?>
                <?php foreach ($mesas as $mesa) : ?>
                    <li>
                        <a href="mesa_QR.php?mesa_id=<?php echo urlencode($mesa['id_mesa']); ?>">
                            Mesa <?php echo htmlspecialchars($mesa['id_mesa']); ?> - Asientos: <?php echo htmlspecialchars($mesa['cantidad_asientos']); ?> - Estado: <?php echo htmlspecialchars($mesa['estado']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else : ?>
                <li class="no-mesas">No hay mesas disponibles en este momento.</li>
            <?php endif; ?>
        </ul>
    </main>

    <script src="js/sistema_gestion.js"></script>
</body>
</html>