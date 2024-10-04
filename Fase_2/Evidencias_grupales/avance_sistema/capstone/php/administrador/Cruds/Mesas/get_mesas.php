<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurante_bd";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id_mesa = $_GET['id'] ?? '';

if ($id_mesa) {
    $sql = "SELECT * FROM Mesa WHERE id_mesa=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_mesa);
    $stmt->execute();
    $result = $stmt->get_result();
    $mesa = $result->fetch_assoc();

    if ($mesa) {
        echo '<h2>Editar Mesa</h2>
              <form id="formEditarMesa" action="crud_mesas.php" method="post">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id_mesa" value="' . htmlspecialchars($mesa['id_mesa']) . '">
                  <div class="mb-3">
                      <label for="cantidad_asientos" class="form-label">Cantidad de Asientos</label>
                      <input type="number" class="form-control" id="cantidad_asientos" name="cantidad_asientos" value="' . htmlspecialchars($mesa['cantidad_asientos']) . '" required>
                  </div>
                  <div class="mb-3">
                      <label for="estado" class="form-label">Estado</label>
                      <select class="form-select" id="estado" name="estado" required>
                          <option value="Disponible"' . ($mesa['estado'] === 'Disponible' ? ' selected' : '') . '>Disponible</option>
                          <option value="Ocupada"' . ($mesa['estado'] === 'Ocupada' ? ' selected' : '') . '>Ocupada</option>
                      </select>
                  </div>
                  <button type="submit" class="btn btn-primary">Guardar Cambios</button>
              </form>';
    } else {
        echo '<p>No se encontró la mesa.</p>';
    }

    $conn->close();
}
?>