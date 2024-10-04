<?php
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

$action = $_POST['action'] ?? '';

if ($action === 'create') {
    $cantidad_asientos = $_POST['cantidad_asientos'];
    $estado = $_POST['estado'];

    $sql = "INSERT INTO Mesa (cantidad_asientos, estado) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $cantidad_asientos, $estado);
    $stmt->execute();
} elseif ($action === 'update') {
    $id_mesa = $_POST['id_mesa'];
    $cantidad_asientos = $_POST['cantidad_asientos'];
    $estado = $_POST['estado'];

    $sql = "UPDATE Mesa SET cantidad_asientos=?, estado=? WHERE id_mesa=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isi", $cantidad_asientos, $estado, $id_mesa);
    $stmt->execute();
} elseif ($action === 'delete') {
    $id_mesa = $_POST['id_mesa'];

    $sql = "DELETE FROM Mesa WHERE id_mesa=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_mesa);
    $stmt->execute();
}

$stmt->close();
$conn->close();

header("Location: admin_mesas.php");
exit();
?>