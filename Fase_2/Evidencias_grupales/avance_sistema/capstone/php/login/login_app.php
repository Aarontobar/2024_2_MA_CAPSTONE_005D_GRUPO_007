<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$database = "restaurante_bd";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed']);
    exit();
}

// login.php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Protege contra inyecciones SQL
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Verificar credenciales
    $sql = "SELECT id_usuario, nombre_usuario, contrasena, tipo_usuario FROM usuarios WHERE nombre_usuario='$username'";
    $result = $conn->query($sql);

    if ($result && $row = $result->fetch_assoc()) {
        // Comparar contraseñas (sin codificación)
        if ($password === $row['contrasena']) {
            // Guardar los datos del usuario en la respuesta JSON
            $response = [
                'status' => 'success',
                'user_id' => $row['id_usuario'],
                'user_name' => $row['nombre_usuario'],
                'user_type' => $row['tipo_usuario']
            ];
            echo json_encode($response);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nombre de usuario o contraseña incorrectos']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Nombre de usuario o contraseña incorrectos']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método no permitido']);
}
?>