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

// Obtener el ID de la mesa desde la URL
$id_mesa = isset($_GET['mesa_id']) ? intval($_GET['mesa_id']) : 0;

// Consultar si hay un detalle mesero mesa para la mesa seleccionada
$sql = "SELECT id_usuario FROM detalle_mesero_mesa WHERE id_mesa = ? AND estado = 'activo'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_mesa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $detalle = $result->fetch_assoc();
    $id_usuario = $detalle['id_usuario'];

    // Generar el enlace al menú con el ID de la mesa y el ID de usuario
    $link_menu = "../menu/ver_menu.php?mesa_id=" . urlencode($id_mesa) . "&usuario_id=" . urlencode($id_usuario);

    // URL de la API para generar el código QR
    $qr_api_url = "https://api.qrserver.com/v1/create-qr-code/?data=" . urlencode($link_menu) . "&size=200x200";

} else {
    $id_usuario = null;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código QR para Mesa</title>
    <link rel="stylesheet" href="../../css/qr.css">
    <script>
        // Función para actualizar el estado de la mesa
        function checkMesaStatus() {
            const mesaId = <?php echo json_encode($id_mesa); ?>;
            fetch(`check_mesa_status.php?mesa_id=${mesaId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.id_usuario) {
                        // Si hay un mesero asignado, actualizar el QR
                        document.getElementById('qrImage').src = data.qr_url;
                        document.getElementById('linkMenu').href = data.link_menu;
                        document.getElementById('qrSection').style.display = 'block';
                        document.getElementById('noMeseroMessage').style.display = 'none';
                    } else {
                        // Si no hay mesero asignado, mostrar el mensaje
                        document.getElementById('qrSection').style.display = 'none';
                        document.getElementById('noMeseroMessage').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Revisar el estado de la mesa cada 5 segundos
        setInterval(checkMesaStatus, 5000);
    </script>
</head>
<body>
    <h1>Código QR para la Mesa <?php echo htmlspecialchars($id_mesa); ?></h1>

    <main>
        <div id="qrSection" style="display: <?php echo $id_usuario ? 'block' : 'none'; ?>;">
            <p>Escanea el siguiente código QR para acceder al menú:</p>
            <div class="qr-container">
                <img id="qrImage" src="<?php echo htmlspecialchars($qr_api_url); ?>" alt="Código QR">
            </div>
            <a id="linkMenu" href="<?php echo htmlspecialchars($link_menu); ?>" class="boton-volver">Ir al Menú</a>
            <button class="boton-imprimir" onclick="window.print();">Imprimir Código QR</button>
        </div>
        <p id="noMeseroMessage" class="mensaje" style="display: <?php echo $id_usuario ? 'none' : 'block'; ?>;">
            Esta mesa aún no está asignada a un mesero.
        </p>
    </main>
</body>
</html>