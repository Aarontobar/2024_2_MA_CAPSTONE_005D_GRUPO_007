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

// Fecha actual
$fechaHoy = date('Y-m-d'); // Obtiene la fecha en formato YYYY-MM-DD

// Verificar si ya hay un día iniciado para hoy
$sql = "SELECT * FROM estado_dia WHERE fecha = '$fechaHoy' LIMIT 1";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
    // Si ya hay un día iniciado, redirigir a la página del metre
    header("Location: metre.php");
    exit();
}

// Consulta de platillos y mesas (solo si el día no está iniciado)
$platillos = $conn->query("SELECT * FROM Platillos");
$mesas = $conn->query("SELECT COUNT(*) as totalMesas FROM Mesa")->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio del Día</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Helvetica Neue', Arial, sans-serif;
        background-color: #f4f7f6;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .center-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
    }

    #iniciarDiaBtn {
        padding: 15px 30px;
        font-size: 1.5rem;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #iniciarDiaBtn:hover {
        background-color: #0056b3;
    }

    .container {
        display: none;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 20px;
        text-align: center;
    }

    form {
        background-color: white;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
    }

    input[type="text"], input[type="number"], select, input[type="time"] {
        width: 100%;
        padding: 12px;
        margin: 10px 0 20px 0;
        display: inline-block;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }

    button[type="submit"] {
        width: 100%;
        background-color: #007bff;
        color: white;
        padding: 14px 20px;
        margin-top: 10px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 1.2rem;
    }

    button[type="submit"]:hover {
        background-color: #0056b3;
    }

    .hora-actual, .fecha-actual {
        font-size: 18px;
        font-weight: bold;
        margin: 20px 0;
        color: #555;
    }

    table {
        width: 100%;
        margin: 20px 0;
        border-collapse: collapse;
    }

    table th, table td {
        padding: 12px;
        text-align: center;
        border: 1px solid #ddd;
    }

    table th {
        background-color: #f1f1f1;
        font-weight: bold;
    }

    table input, table select {
        width: 100%;
        padding: 8px;
    }

    .scrollable-container {
        max-height: 300px;
        overflow-y: auto;
    }
    </style>
</head>
<body>

<!-- Contenedor central con botón para iniciar el día -->
<div class="center-container" id="centerContainer">
    <button id="iniciarDiaBtn">Iniciar Día</button>
</div>

<!-- Formulario para llenar cuando se inicie el día -->
<div class="container" id="formulario">
    <form action="inicio_dia.php" method="POST">
        <!-- Platillos disponibles -->
        <div class="form-section">
            <input type="checkbox" id="togglePlatillos" name="togglePlatillos">
            <label for="togglePlatillos">Platillos Disponibles</label>

            <div id="platillosContainer" class="section scrollable-container" style="display:none;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre del Platillo</th>
                            <th>Estado</th>
                            <th>Precio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($platillo = $platillos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $platillo['nombre_platillo']; ?></td>
                            <td>
                                <select name="estado_platillo[<?php echo $platillo['id_platillo']; ?>]">
                                    <option value="Disponible" <?php if ($platillo['estado'] == 'Disponible') echo 'selected'; ?>>Disponible</option>
                                    <option value="No Disponible" <?php if ($platillo['estado'] == 'No Disponible') echo 'selected'; ?>>No Disponible</option>
                                </select>
                            </td>
                            <td><input type="number" name="precio_platillo[<?php echo $platillo['id_platillo']; ?>]" value="<?php echo $platillo['precio']; ?>"></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mesas disponibles -->
        <div class="form-section">
            <input type="checkbox" id="toggleMesas" name="toggleMesas">
            <label for="toggleMesas">Mesas Disponibles</label>

            <div id="mesasContainer" class="section" style="display:none;">
                <label for="mesasDisponibles">Cantidad de Mesas Disponibles (máx: <?php echo $mesas['totalMesas']; ?>)</label>
                <input type="number" id="mesasDisponibles" name="mesasDisponibles" min="1" max="<?php echo $mesas['totalMesas']; ?>" class="form-control">
            </div>
        </div>

        <!-- Hora de cierre -->
        <div class="form-section">
            <label for="horaCierre">Hora de Cierre</label>
            <input type="time" id="horaCierre" name="horaCierre" class="form-control" required>
        </div>

        <!-- Hora y Fecha Actual -->
        <div class="form-section time-container">
            <div class="hora-actual">
                Hora actual: <span id="horaActual"></span>
            </div>
            <div class="fecha-actual">
                Fecha actual: <span id="fechaActual"></span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar Configuración</button>
    </form>
</div>

<script>
    const iniciarDiaBtn = document.getElementById('iniciarDiaBtn');
    const centerContainer = document.getElementById('centerContainer');
    const formulario = document.getElementById('formulario');
    const platillosContainer = document.getElementById('platillosContainer');
    const togglePlatillos = document.getElementById('togglePlatillos');
    const mesasContainer = document.getElementById('mesasContainer');
    const toggleMesas = document.getElementById('toggleMesas');
    const horaActual = document.getElementById('horaActual');
    const fechaActual = document.getElementById('fechaActual');

    // Mover botón al hacer clic y mostrar el formulario
    iniciarDiaBtn.addEventListener('click', () => {
        centerContainer.style.display = 'none';
        formulario.style.display = 'block';
    });

    // Mostrar/ocultar platillos
    togglePlatillos.addEventListener('change', () => {
        platillosContainer.style.display = togglePlatillos.checked ? 'block' : 'none';
    });

    // Mostrar/ocultar mesas
    toggleMesas.addEventListener('change', () => {
        mesasContainer.style.display = toggleMesas.checked ? 'block' : 'none';
    });

    // Actualizar la hora y fecha actual en tiempo real
    function actualizarHoraFecha() {
        const now = new Date();
        horaActual.textContent = now.toLocaleTimeString('es-CL'); // Formato de hora en Chile
        fechaActual.textContent = now.toLocaleDateString('es-CL'); // Formato de fecha en Chile
    }
    setInterval(actualizarHoraFecha, 1000);
    actualizarHoraFecha();
</script>

</body>
</html>