<?php
// Conectar a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el id del mesero desde el enlace
$id_usuario = isset($_GET['id_mesero']) ? intval($_GET['id_mesero']) : 0;

// Consultar las mesas asignadas al mesero desde la tabla Pedido
$sql = "SELECT M.id_mesa, M.estado, P.id_pedido, P.total_cuenta, P.hora, P.fecha
        FROM Pedido P
        INNER JOIN Mesa M ON P.id_mesa = M.id_mesa
        WHERE P.id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

$mesas = [];
while ($row = $result->fetch_assoc()) {
    $mesas[] = $row;
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú por Mesa</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background-color: #fbf8f3;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 220px;
            background-color: #f7f3ee;
            padding-top: 40px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar a {
            display: block;
            color: #000;
            padding: 15px 20px;
            text-decoration: none;
            font-size: 18px;
        }
        .sidebar a:hover {
            background-color: #ddd;
        }
        .content {
            margin-left: 240px;
            padding: 40px;
        }
        h1 {
            font-size: 24px;
            color: #333;
        }
        .table-list {
            list-style: none;
            padding: 0;
        }
        .table-item {
            padding: 20px;
            background-color: #f3f1ed;
            margin-bottom: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid transparent;
            text-decoration: none;
            color: inherit;
        }
        .table-item:hover {
            background-color: #e8e5df;
        }
        .table-item a {
            text-decoration: none;
            color: inherit;
            display: block;
            width: 100%;
            height: 100%;
        }
        .occupied {
            border-left: 4px solid #ff7b54;
        }
        .free {
            border-left: 4px solid #4caf50;
        }
        .served {
            border-left: 4px solid #1e90ff;
        }
        .occupied:hover {
            box-shadow: 0 0 10px rgba(255, 123, 84, 0.7);
        }
        .free:hover {
            box-shadow: 0 0 10px rgba(76, 175, 80, 0.7);
        }
        .served:hover {
            box-shadow: 0 0 10px rgba(30, 144, 255, 0.7);
        }
        .table-status {
            color: #555;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <a href="mesero.php?id_mesero=<?= urlencode($id_usuario); ?>">Mesas asignadas</a>
        <a href="menu_por_mesa.php?id_mesero=<?= urlencode($id_usuario); ?>">Menú por mesa</a>
    </div>

    <div class="content">
        <h1>Menú por Mesa</h1>
        <ul class="table-list">
            <?php if (!empty($mesas)): ?>
                <?php foreach ($mesas as $mesa): ?>
                    <li class="table-item <?= strtolower($mesa['estado']) ?>">
                        <a href="../menu/ver_menu.php?mesa_id=<?= urlencode($mesa['id_mesa']); ?>&id_mesero=<?= urlencode($id_usuario); ?>">
                            <span>Mesa <?= $mesa['id_mesa'] ?></span>
                            <span class="table-status"><?= $mesa['estado'] ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No hay mesas asignadas al mesero.</li>
            <?php endif; ?>
        </ul>
    </div>

</body>
</html>