<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
    <link rel="stylesheet" href="../../css/cocina.css">
</head>
<body>
<div class="orders-header">
        <h1>Pedidos</h1>
        <div class="filters">
            <select id="order-filter">
                <option value="all-orders">Todos los Pedidos</option>
                <option value="new">Recién Pedido</option>
                <option value="waiting">En Espera</option>
                <option value="delayed">Atrasado</option>
            </select>

            <select id="type-filter">
                <option value="all">Todos</option>
                <option value="delivery">Delivery</option>
                <option value="pickup">Pickup</option>
                <option value="dine-in">Dine-In</option>
            </select>
        </div>
    </div>

    <div class="orders-container">
        <!-- Las tarjetas de pedidos se llenarán aquí mediante JavaScript -->
    </div>

    <?php include '../modulos/Chat.php'; ?>
    
    <script src="../../js/cocina.js"></script>
</body>
</html>