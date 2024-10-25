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
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

switch ($tipo) {
    case 'pedidos':
        $query = "SELECT id_pedido, estado FROM Pedido";
        if ($search) {
            $query .= " WHERE id_pedido = '$search'";
        }
        break;
    case 'platillos':
        $query = "SELECT id_platillo, nombre_platillo, estado FROM Platillos";
        if ($search) {
            $query .= " WHERE nombre_platillo LIKE '%$search%'";
        }
        break;
    case 'reservas':
        $query = "SELECT nombre_reserva, apellido_reserva, fecha, id_mesa FROM Reserva";
        if ($search) {
            $query .= " WHERE id_mesa = '$search' OR nombre_reserva LIKE '%$search%' OR apellido_reserva LIKE '%$search%'";
        }
        break;
    case 'mesas':
        $query = "SELECT id_mesa, estado, cantidad_asientos FROM Mesa";
        if ($search) {
            $query .= " WHERE id_mesa = '$search' OR cantidad_asientos LIKE '%$search%'";
        }
        break;
    default:
        echo "Tipo no válido";
        exit();
}

// Ejecutar la consulta
$resultado = $conn->query($query);

// Devolver solo el cuerpo de la tabla
if ($resultado->num_rows > 0) {
    while($fila = $resultado->fetch_assoc()): ?>
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
                <td class="text-end">
                    <button class="btn btn-info" onclick="assignTable(<?= $fila['id_mesa'] ?>)">Asignar</button>
                </td>
            <?php endif; ?>
        </tr>
    <?php endwhile; 
} else {
    // Mensaje cuando no hay resultados
    echo '<tr><td colspan="5">No hay resultados.</td></tr>'; // Ajusta el número de columnas según sea necesario
}

$conn->close();
?>