<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "restaurante_bd";

// Habilitar la visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['username']) || !isset($_POST['password'])) {
        echo json_encode(['success' => false, 'message' => 'No se recibieron el usuario o la contraseña']);
        exit();
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Log para verificar los datos recibidos
    error_log("Datos recibidos: username=$username, password=$password");

    // Protege contra inyecciones SQL
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Verificar credenciales (sin codificación de contraseña)
    $sql = "SELECT id_usuario, nombre_usuario, contrasena, tipo_usuario FROM usuarios WHERE nombre_usuario='$username'";
    $result = $conn->query($sql);

    if ($result) {
        if ($row = $result->fetch_assoc()) {
            // Comparar contraseñas en texto plano
            if ($password === $row['contrasena']) {
                // Guardar datos del usuario en la sesión
                $_SESSION['user_id'] = $row['id_usuario'];
                $_SESSION['user_name'] = $row['nombre_usuario'];
                $_SESSION['user_type'] = $row['tipo_usuario'];

                // Devolver respuesta JSON según tipo de usuario
                echo json_encode(['success' => true, 'user_type' => $row['tipo_usuario'], 'user_id' => $row['id_usuario']]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nombre de usuario o contraseña incorrectos']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Nombre de usuario o contraseña incorrectos']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud no permitido']);
}
?>