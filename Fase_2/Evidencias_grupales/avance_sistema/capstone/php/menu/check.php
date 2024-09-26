<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $count = 0;

    if (isset($_SESSION['carrito'])) {
        foreach ($_SESSION['carrito'] as $item) {
            if ($item['id'] == $id) {
                $count++;
            }
        }
    }

    // Devolver el conteo de productos
    echo $count;
}
?>