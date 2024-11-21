<?php
include '../modulos/conexion.php';

$id_usuario = intval($_GET['id_usuario']);

$query = "SELECT u.id_usuario, u.nombre_usuario, 
          (SELECT mensaje FROM mensajes WHERE (id_usuario_envia = u.id_usuario OR id_usuario_recibe = u.id_usuario) ORDER BY fecha_hora DESC LIMIT 1) as ultimo_mensaje 
          FROM usuarios u WHERE u.id_usuario != $id_usuario";

$result = mysqli_query($conn, $query);

$usuarios = array();
while ($row = mysqli_fetch_assoc($result)) {
    $usuarios[] = $row;
}

echo json_encode($usuarios);
?>