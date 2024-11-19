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
    $nombre_platillo = $_POST['nombre_platillo'];
    $descripcion_platillo = $_POST['descripcion_platillo'];
    $precio = $_POST['precio'];
    $estado = $_POST['estado'];
    $tiempo_preparacion = $_POST['tiempo_preparacion'];
    $tipo_platillo = $_POST['tipo_platillo'];

    // Manejar la carga de la foto
    $ruta_foto = '';
    if (isset($_FILES['ruta_foto']) && $_FILES['ruta_foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['ruta_foto']['tmp_name'];
        $fileName = $_FILES['ruta_foto']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/capstone/imagenes/Platillos/';
        $dest_path = $uploadFileDir . $newFileName;

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $ruta_foto = $newFileName;
        } else {
            echo "Error al mover el archivo subido.";
            exit();
        }
    }

    $sql = "INSERT INTO Platillos (nombre_platillo, descripcion_platillo, precio, estado, tiempo_preparacion, ruta_foto, tipo_platillo)
            VALUES ('$nombre_platillo', '$descripcion_platillo', $precio, '$estado', '$tiempo_preparacion', '$ruta_foto', '$tipo_platillo')";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a admin_platillos.php
        header("Location: admin_platillos.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action === 'update') {
    $id_platillo = $_POST['id_platillo'];
    $nombre_platillo = $_POST['nombre_platillo'];
    $descripcion_platillo = $_POST['descripcion_platillo'];
    $precio = $_POST['precio'];
    $estado = $_POST['estado'];
    $tiempo_preparacion = $_POST['tiempo_preparacion'];
    $tipo_platillo = $_POST['tipo_platillo'];

    // Manejar la carga de la foto
    $ruta_foto = '';
    if (isset($_FILES['ruta_foto']) && $_FILES['ruta_foto']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['ruta_foto']['tmp_name'];
        $fileName = $_FILES['ruta_foto']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = $_SERVER['DOCUMENT_ROOT'] . '/capstone/imagenes/Platillos/';
        $dest_path = $uploadFileDir . $newFileName;

        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0777, true);
        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $ruta_foto = $newFileName;
        } else {
            echo "Error al mover el archivo subido.";
            exit();
        }
    }

    $sql = "UPDATE Platillos SET 
            nombre_platillo = '$nombre_platillo', 
            descripcion_platillo = '$descripcion_platillo', 
            precio = $precio, 
            estado = '$estado', 
            tiempo_preparacion = '$tiempo_preparacion', 
            ruta_foto = '$ruta_foto', 
            tipo_platillo = '$tipo_platillo'
            WHERE id_platillo = $id_platillo";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a admin_platillos.php
        header("Location: admin_platillos.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} elseif ($action === 'delete') {
    $id_platillo = $_POST['id_platillo'];

    $sql = "DELETE FROM Platillos WHERE id_platillo = $id_platillo";

    if ($conn->query($sql) === TRUE) {
        // Redirigir a admin_platillos.php
        header("Location: admin_platillos.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>