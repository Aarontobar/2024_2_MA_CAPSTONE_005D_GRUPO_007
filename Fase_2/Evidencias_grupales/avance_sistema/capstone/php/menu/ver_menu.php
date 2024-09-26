
<?php
session_start();

// Establecer la conexión a la base de datos
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

// Establecer la zona horaria a Santiago, Chile
date_default_timezone_set('America/Santiago');

// Obtener la fecha y hora actual
$fecha_actual = date('Y-m-d');
$hora_actual = date('H:i:s');

// Consulta SQL para obtener el estado del día y la hora de cierre
$sql_estado = "SELECT estado, hora_cierre FROM Estado_Dia WHERE fecha = ?";
$stmt_estado = $conn->prepare($sql_estado);

// Verificar si la consulta se preparó correctamente
if ($stmt_estado === false) {
    die("Error preparando la consulta: " . $conn->error);
}

// Enlazar parámetros y ejecutar la consulta
$stmt_estado->bind_param('s', $fecha_actual);
$stmt_estado->execute();
$result_estado = $stmt_estado->get_result();

// Verificar si se encontró un registro para la fecha actual
if ($result_estado->num_rows > 0) {
    $row = $result_estado->fetch_assoc();
    $estado_dia = $row['estado'];
    $hora_cierre = $row['hora_cierre'];
} else {
    // Si no se encontró un registro, mostrar un mensaje
    echo "No se encontró ningún registro para la fecha: " . $fecha_actual . "<br>";
    $estado_dia = null;
    $hora_cierre = null;
}

// Verificar si el día está iniciado
if ($estado_dia === null || $estado_dia === 'No Iniciado') {
    // Si el día no ha iniciado
    header('Location: hora_antes.php');
    exit;
}

// Verificar si la hora de cierre ha pasado
if ($hora_cierre !== null) {
    // Crear objeto DateTime para la hora de cierre
    $hora_cierre_dt = DateTime::createFromFormat('H:i:s', $hora_cierre);
    $hora_actual_dt = DateTime::createFromFormat('H:i:s', $hora_actual);

    // Comparar las horas
    if ($hora_actual_dt > $hora_cierre_dt) {
        // Si ya pasó la hora de cierre, redirigir a otra página
        header('Location: cierre.php');
        exit;
    }
}


$id_mesa = isset($_GET['mesa_id']) ? intval($_GET['mesa_id']) : 0;
$id_mesero = isset($_GET['id_mesero']) ? intval($_GET['id_mesero']) : 0;

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Manejar la adición de artículos al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['precio'])) {
    $item = [
        'id' => $_POST['id'],
        'nombre' => $_POST['nombre'],
        'precio' => $_POST['precio'],
    ];

    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    $_SESSION['carrito'][] = $item;
    echo count($_SESSION['carrito']); // Devolver la cantidad de artículos en el carrito
    exit;
}

// Manejar la eliminación de artículos del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    if (isset($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['carrito'][$key]);
                $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar el array
                echo "Producto eliminado del carrito";
                exit;
            }
        }

        echo "No se encontró el producto en el carrito.";
        exit;
    }

    echo "Carrito no encontrado.";
    exit;
}

// Obtener el tipo de platillo seleccionado o usar "Plato Principal" por defecto
$tipo_platillo = isset($_GET['tipo_platillo']) ? $_GET['tipo_platillo'] : 'Plato Principal';

// Inicializar el array de platillos
$items = [];

// Seleccionar imagen del banner según el tipo de platillo
$banner_img = '';
switch ($tipo_platillo) {
    case 'Entrada':
        $banner_img = '../../imagenes/banner_entradas.jpeg';
        break;
    case 'Plato Principal':
        $banner_img = '../../imagenes/banner_principal.jpg';
        break;
    case 'Acompañamientos':
        $banner_img = '../../imagenes/banner_acompañamientos.jpg';
        break;
    case 'Postres':
        $banner_img = '../../imagenes/banner_postres.jpg';
        break;
    case 'Bebida':  // Mantenemos el caso de bebidas para el banner
        $banner_img = '../../imagenes/banner_bebidas.jpg';
        break;
    case 'Menú Infantil':
        $banner_img = '../../imagenes/banner_menu_niños.png';
        break;
    default:
        $banner_img = '../../imagenes/banner_default.jpeg'; // Imagen por defecto
        break;
}

// Consulta para obtener los platillos según el tipo seleccionado
$sql = "SELECT nombre_platillo AS nombre, descripcion_platillo AS descripcion, precio, ruta_foto, id_platillo, tipo_platillo
        FROM Platillos 
        WHERE estado = 'Disponible' AND tipo_platillo = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $tipo_platillo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
} else {
    echo "No hay platillos disponibles.";
}

$conn->close();

// Obtener la cantidad de artículos en el carrito
$cart_count = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>
    <link rel="stylesheet" href="../../css/ver_menu.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Cucina Italiana</div>
            <ul>
                <li><a href="?tipo_platillo=Entrada&mesa_id=<?php echo urlencode($id_mesa); ?><?php echo $id_mesero > 0 ? '&id_mesero=' . urlencode($id_mesero) : ''; ?>">Entradas</a></li>
                <li><a href="?tipo_platillo=Plato Principal&mesa_id=<?php echo urlencode($id_mesa); ?><?php echo $id_mesero > 0 ? '&id_mesero=' . urlencode($id_mesero) : ''; ?>">Platos principales</a></li>
                <li><a href="?tipo_platillo=Acompañamientos&mesa_id=<?php echo urlencode($id_mesa); ?><?php echo $id_mesero > 0 ? '&id_mesero=' . urlencode($id_mesero) : ''; ?>">Acompañamientos</a></li>
                <li><a href="?tipo_platillo=Postres&mesa_id=<?php echo urlencode($id_mesa); ?><?php echo $id_mesero > 0 ? '&id_mesero=' . urlencode($id_mesero) : ''; ?>">Postres</a></li>
                <li><a href="?tipo_platillo=Bebida&mesa_id=<?php echo urlencode($id_mesa); ?><?php echo $id_mesero > 0 ? '&id_mesero=' . urlencode($id_mesero) : ''; ?>">Bebidas</a></li>
                <li><a href="?tipo_platillo=Menú Infantil&mesa_id=<?php echo urlencode($id_mesa); ?><?php echo $id_mesero > 0 ? '&id_mesero=' . urlencode($id_mesero) : ''; ?>">Menú infantil</a></li>
            </ul>
            <a href="carrito.php?id_mesa=<?php echo urlencode($id_mesa); ?><?php echo $id_mesero > 0 ? '&id_mesero=' . urlencode($id_mesero) : ''; ?>" class="cart-button">
                <div class="cart">Carrito (<?php echo $cart_count; ?>)</div>
            </a>
        </nav>
    </header>
    <div class="hero">
        <img src="<?php echo htmlspecialchars($banner_img); ?>" alt="Hero Image">
    </div>

    <main>
        <section class="menu">
            <h1><?php echo htmlspecialchars($tipo_platillo); ?></h1>
            <?php
                foreach ($items as $item) {
                    $isInCart = false;

                    // Verifica si el artículo ya está en el carrito
                    if (isset($_SESSION['carrito'])) {
                        foreach ($_SESSION['carrito'] as $cartItem) {
                            if ($cartItem['id'] == $item['id_platillo']) {
                                $isInCart = true;
                                break;
                            }
                        }
                    }

                    echo '<div class="meal-item">';
                    echo '    <div class="meal-description">';
                    echo '        <h2>' . htmlspecialchars($item['nombre']) . '</h2>';
                    echo '        <p>' . htmlspecialchars($item['descripcion']) . '</p>';
                    echo '        <p>Precio: $' . htmlspecialchars($item['precio']) . '</p>';

                    // Formulario con identificador único y evento onclick para manejar el envío por AJAX
                    echo '        <form id="order-form-' . htmlspecialchars($item['id_platillo']) . '" method="post">';
                    echo '            <input type="hidden" name="id" value="' . htmlspecialchars($item['id_platillo']) . '">';
                    echo '            <input type="hidden" name="nombre" value="' . htmlspecialchars($item['nombre']) . '">';
                    echo '            <input type="hidden" name="precio" value="' . htmlspecialchars($item['precio']) . '">';
                    
                    // Botón de Ordenar
                    echo '            <button type="button" onclick="addToCart(' . htmlspecialchars($item['id_platillo']) . ')">Ordenar</button>';
                    
                    // Mostrar el botón "Eliminar" solo si el artículo está en el carrito
                    echo '            <button type="button" id="delete-button-' . htmlspecialchars($item['id_platillo']) . '" class="delete-button"';
                    echo $isInCart ? ' style="display:inline;"' : ' style="display:none;"';  // Ajustar estilo según si está en el carrito o no
                    echo ' onclick="removeFromCart(' . htmlspecialchars($item['id_platillo']) . ')">Eliminar</button>';
                    
                    echo '        </form>';
                    echo '    </div>';
                    echo '    <div class="meal-image">';
                    echo '        <img src="' . htmlspecialchars($item['ruta_foto']) . '" alt="' . htmlspecialchars($item['nombre']) . '">';
                    echo '    </div>';
                    echo '</div>';
                }
                ?>
        </section>
    </main>
    <script src="../../js/ver_menu.js"></script>
</body>
</html>