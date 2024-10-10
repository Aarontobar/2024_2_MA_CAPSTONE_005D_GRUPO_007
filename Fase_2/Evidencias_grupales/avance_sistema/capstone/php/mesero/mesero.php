<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Mesas</title>
    <link rel="stylesheet" href="../../css/mesero.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <a href="#">Mesas asignadas</a>
        <a href="menu_mesero.php?id_usuario=<?= $id_usuario ?>">Menú por mesa</a>
    </div>

    <div class="notification-container">
        <button id="notification-button" class="notification-button">
            <i class="fa fa-bell"></i>
        </button>
        <div id="notification-dropdown" class="notification-dropdown">
            <h4>Notificaciones</h4>
            <ul id="notification-list">
                <!-- Las notificaciones se agregarán aquí dinámicamente -->
            </ul>
        </div>
    </div>

    <div class="content">
        <h1>Vista de Mesas</h1>
        <ul class="table-list">
            <?php if (!empty($mesas)): ?>
                <?php foreach ($mesas as $mesa): ?>
                    <li class="table-item">
                        <span>Mesa ID: <?= $mesa['id_mesa'] ?></span>
                        <span class="table-status">
                            <?php
                                // Determinar el estado de la mesa y el pedido
                                $estado_mesa = $mesa['estado'];
                                $pedido_activo = isset($pedidos_hoy[$mesa['id_mesa']]) ? $pedidos_hoy[$mesa['id_mesa']] : null;

                                if ($estado_mesa == 'Disponible') {
                                    echo "Mesa disponible";
                                } elseif ($estado_mesa == 'Ocupada' && !$pedido_activo) {
                                    echo "Mesa ocupada. Debe tomar el pedido.";
                                } elseif ($estado_mesa == 'Reservada') {
                                    echo "Mesa reservada";
                                } elseif ($estado_mesa == 'En Espera') {
                                    echo "Mesa en espera. Debe tomar el pedido.";
                                } elseif ($estado_mesa == 'Para Limpiar') {
                                    echo "Mesa necesita limpieza";
                                }

                                // Mostrar estado del pedido si hay uno
                                if ($pedido_activo) {
                                    if ($pedido_activo['estado'] == 'preparado') {
                                        echo " | Pedido listo para llevar.";
                                    } elseif ($pedido_activo['estado'] == 'en preparación') {
                                        echo " | Pedido en preparación.";
                                    } elseif ($pedido_activo['estado'] == 'recibido') {
                                        echo " | Pedido recibido.";
                                    } elseif ($pedido_activo['estado'] == 'servido') {
                                        echo " | Pedido servido.";
                                    } elseif ($pedido_activo['estado'] == 'completado') {
                                        echo " | Pedido completado.";
                                    } elseif ($pedido_activo['estado'] == 'cancelado') {
                                        echo " | Pedido cancelado.";
                                    }
                                }
                            ?>
                        </span>

                        <!-- Detalles -->
                        <div class="details">
                            <?php if ($estado_mesa == 'Ocupada' && !$pedido_activo): ?>
                                <!-- Mesa ocupada sin pedido -->
                                <p>La mesa está ocupada. Debe tomar el pedido.</p>
                                <a href="../menu/ver_menu.php?mesa_id=<?= $mesa['id_mesa'] ?>&id_usuario=<?= $id_usuario ?>" class="action-button">Tomar Pedido</a>

                            <?php elseif ($estado_mesa == 'Para Limpiar'): ?>
                                <!-- Mesa para limpiar -->
                                <p>La mesa necesita ser limpiada.</p>
                                <a href="url_destino?id_mesa=<?= $mesa['id_mesa'] ?>&id_usuario=<?= $id_usuario ?>" class="action-button">Marcar como limpia</a>

                            <?php elseif ($pedido_activo): ?>
                                <!-- Mesa con pedido activo -->
                                <?php if ($pedido_activo['estado'] == 'preparado'): ?>
                                    <p>El pedido está listo para ser llevado a la mesa.</p>
                                    <a href="url_destino?id_mesa=<?= $mesa['id_mesa'] ?>&id_usuario=<?= $id_usuario ?>" class="action-button">Llevar Pedido</a>
                                <?php else: ?>
                                    <p><strong>Pedido ID:</strong> <?= $pedido_activo['id_pedido'] ?></p>
                                    <p><strong>Total:</strong> $<?= $pedido_activo['total_cuenta'] ?></p>
                                    <p><strong>Hora del pedido:</strong> <?= $pedido_activo['hora'] ?></p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>No hay mesas asignadas.</li>
            <?php endif; ?>
        </ul>
    </div>

    <?php include '../modulos/Chat.php'; ?>
</body>
<script src="../../js/mesero.js"></script>
</html>