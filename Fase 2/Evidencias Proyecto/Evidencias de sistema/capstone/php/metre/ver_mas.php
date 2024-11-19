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

// Definir qué tipo de datos se mostrarán
$tipo = $_GET['tipo'];
$search = isset($_GET['search']) ? $_GET['search'] : '';

switch ($tipo) {
    case 'pedidos':
        $titulo = "Lista de Pedidos";
        $query = "SELECT id_pedido, estado FROM Pedido";
        if ($search) {
            $query .= " WHERE id_pedido = '$search'";
        }
        break;
    case 'platillos':
        $titulo = "Lista de Platillos";
        $query = "SELECT id_platillo, nombre_platillo, estado FROM Platillos";
        if ($search) {
            $query .= " WHERE nombre_platillo LIKE '%$search%'";
        }
        break;
    case 'reservas':
        $titulo = "Lista de Reservas";
        $query = "SELECT nombre_reserva, apellido_reserva, fecha, id_mesa FROM Reserva";
        if ($search) {
            $query .= " WHERE id_mesa = '$search' OR nombre_reserva LIKE '%$search%' OR apellido_reserva LIKE '%$search%'";
        }
        break;
    case 'mesas':
        $titulo = "Estado de Mesas";
        $query = "SELECT M.id_mesa, M.estado, M.cantidad_asientos, U.nombre_usuario AS mesero_asignado
                  FROM Mesa M
                  LEFT JOIN detalle_mesero_mesa DMM ON M.id_mesa = DMM.id_mesa AND DMM.estado = 'activo'
                  LEFT JOIN Usuarios U ON DMM.id_usuario = U.id_usuario";
        if ($search) {
            $query .= " WHERE M.id_mesa = '$search' OR M.cantidad_asientos LIKE '%$search%'";
        }
        break;
    default:
        echo "Tipo no válido";
        exit();
}

// Ejecutar la consulta
$resultado = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f7f6;
        }
        .search-bar {
            margin-bottom: 20px;
        }
        .table {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
        }
        thead {
            background-color: #007bff;
            color: white;
        }
        .container {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4"><?= $titulo ?></h1>

    <!-- Barra de búsqueda -->
    <div class="search-bar">
        <input type="text" class="form-control" id="search" placeholder="Buscar..." value="<?= htmlspecialchars($search) ?>" aria-label="Buscar">
    </div>

    <table class="table" id="result-table">
        <thead>
            <tr>
                <?php if ($tipo == 'pedidos'): ?>
                    <th>ID Pedido</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                <?php elseif ($tipo == 'platillos'): ?>
                    <th>ID Platillo</th>
                    <th>Nombre Platillo</th>
                    <th>Disponibilidad</th>
                    <th>Acciones</th>
                <?php elseif ($tipo == 'reservas'): ?>
                    <th>Nombre</th>
                    <th>Fecha</th>
                    <th>Mesa</th>
                    <th>Acciones</th>
                <?php elseif ($tipo == 'mesas'): ?>
                    <th>ID Mesa</th>
                    <th>Cantidad de Asientos</th>
                    <th>Estado</th>
                    <th>Mesero Asignado</th>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody id="table-body">
            <?php while($fila = $resultado->fetch_assoc()): ?>
                <tr>
                    <?php if ($tipo == 'pedidos'): ?>
                        <td><?= $fila['id_pedido'] ?></td>
                        <td><?= $fila['estado'] ?></td>
                        <td class="text-end">
                            <button class="btn btn-warning" onclick="prioritizeOrder(<?= $fila['id_pedido'] ?>)">Priorizar</button>
                            <button class="btn btn-danger" onclick="cancelOrder(<?= $fila['id_pedido'] ?>)">Cancelar</button>
                        </td>
                    <?php elseif ($tipo == 'platillos'): ?>
                        <td><?= $fila['id_platillo'] ?></td>
                        <td><?= $fila['nombre_platillo'] ?></td>
                        <td><?= $fila['estado'] ?></td>
                        <td class="text-end">
                            <button class="btn btn-success" onclick="toggleAvailability(<?= $fila['id_platillo'] ?>)">Cambiar Disponibilidad</button>
                        </td>
                    <?php elseif ($tipo == 'reservas'): ?>
                        <td><?= $fila['nombre_reserva'] . ' ' . $fila['apellido_reserva'] ?></td>
                        <td><?= $fila['fecha'] ?></td>
                        <td><?= $fila['id_mesa'] ?></td>
                        <td class="text-end">
                            <button class="btn btn-primary" onclick="confirmReservation(<?= $fila['id_mesa'] ?>)">Confirmar</button>
                            <button class="btn btn-danger" onclick="cancelReservation(<?= $fila['id_mesa'] ?>)">Cancelar</button>
                        </td>
                    <?php elseif ($tipo == 'mesas'): ?>
                        <td><?= $fila['id_mesa'] ?></td>
                        <td><?= $fila['cantidad_asientos'] ?></td>
                        <td><?= $fila['estado'] ?></td>
                        <td><?= $fila['mesero_asignado'] ? $fila['mesero_asignado'] : 'No asignado' ?></td>
                        <td class="text-end">
                            <?php if (!$fila['mesero_asignado']): ?>
                                <button class="btn btn-info" onclick="assignTable(<?= $fila['id_mesa'] ?>)">Asignar</button>
                            <?php endif; ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script>
document.getElementById('search').addEventListener('input', function() {
    const searchQuery = this.value;

    // Crear una instancia de XMLHttpRequest
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'buscar.php?tipo=<?= $tipo ?>&search=' + encodeURIComponent(searchQuery), true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById('table-body').innerHTML = xhr.responseText;
        }
    };
    xhr.send();
});

// Aquí puedes agregar las funciones que se llaman al hacer clic en los botones
function prioritizeOrder(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cambio_estado.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert('Pedido priorizado con éxito.');
                location.reload(); // Recargar la página para reflejar los cambios
            } else {
                alert('Error al priorizar el pedido: ' + response.error);
            }
        } else {
            alert('Error en la solicitud: ' + xhr.status);
        }
    };
    xhr.send('action=prioritize&id_pedido=' + id);
}

function cancelOrder(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cambio_estado.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.success) {
                alert('Pedido cancelado con éxito.');
                location.reload(); // Recargar la página para reflejar los cambios
            } else {
                alert('Error al cancelar el pedido: ' + response.error);
            }
        } else {
            alert('Error en la solicitud: ' + xhr.status);
        }
    };
    xhr.send('action=cancel&id_pedido=' + id);
}

function toggleAvailability(id) {
    alert('Cambiar disponibilidad del platillo ID: ' + id);
    // Implementa la llamada AJAX para cambiar la disponibilidad
}

function confirmReservation(id) {
    alert('Confirmar reserva en mesa ID: ' + id);
    // Implementa la llamada AJAX para confirmar la reserva
}

function cancelReservation(id) {
    alert('Cancelar reserva en mesa ID: ' + id);
    // Implementa la llamada AJAX para cancelar la reserva
}

function assignTable(id) {
    console.log("ID de mesa a asignar:", id); // Para depuración
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cambio_mesa.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        console.log("Respuesta del servidor:", xhr.responseText); // Para depuración
        if (xhr.status === 200) {
            alert('Mesa asignada con éxito.'); // Mensaje de éxito
            location.reload(); // Actualizar la página
        } else {
            alert('Error al asignar la mesa: ' + xhr.responseText);
        }
    };
    xhr.send('id_mesa=' + id); // Envío del id_mesa
}
</script>
</body>
</html>