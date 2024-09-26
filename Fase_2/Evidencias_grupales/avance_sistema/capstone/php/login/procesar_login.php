<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "restaurante_bd";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<?php
// login.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Protege contra inyecciones SQL
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Verificar credenciales (sin codificación de contraseña)
    $sql = "SELECT id_usuario, nombre_usuario, contrasena, tipo_usuario FROM usuarios WHERE nombre_usuario='$username'";
    $result = $conn->query($sql);

    if ($result) {
        if ($row = $result->fetch_assoc()) {
            // Mostrar datos por consola para depuración
            echo '<pre>';
            print_r($row);
            echo '</pre>';

            // Comparar contraseñas en texto plano
            if ($password === $row['contrasena']) {
                // Guardar datos del usuario en la sesión
                $_SESSION['user_id'] = $row['id_usuario'];
                $_SESSION['user_name'] = $row['nombre_usuario'];
                $_SESSION['user_type'] = $row['tipo_usuario'];

                // Redireccionar según tipo de usuario
                switch ($row['tipo_usuario']) {
                    case 'administrador':
                        header("Location: ../administrador/administrador.php");
                        break;
                    case 'mesero':
                        // Redirigir al mesero con el ID como parámetro
                        header("Location: ../mesero/mesero.php?id_usuario=" . $row['id_usuario']);
                        break;
                    case 'cocina':
                        header("Location: ../cocina/cocina.php");
                        break;
                    case 'metre':
                        header("Location: ../metre/metre.php");
                        break;
                    default:
                        header("Location: login.php?error=Tipo de usuario desconocido");
                }
                exit(); // Asegúrate de detener la ejecución del script después de redirigir
            } else {
                header("Location: login.php?error=Nombre de usuario o contraseña incorrectos");
            }
        } else {
            header("Location: login.php?error=Nombre de usuario o contraseña incorrectos");
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error en la consulta']);
    }
}
?>