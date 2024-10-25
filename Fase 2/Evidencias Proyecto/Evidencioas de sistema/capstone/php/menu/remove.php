<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $itemRemoved = false;

    if (isset($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $key => $item) {
            if ($item['id'] == $id) {
                unset($_SESSION['carrito'][$key]);
                $_SESSION['carrito'] = array_values($_SESSION['carrito']); // Reindexar el array
                $itemRemoved = true;
                break;
            }
        }
    }

    // Devolver si el producto fue eliminado
    echo $itemRemoved ? "Producto eliminado del carrito" : "Producto no encontrado en el carrito";
}
?>