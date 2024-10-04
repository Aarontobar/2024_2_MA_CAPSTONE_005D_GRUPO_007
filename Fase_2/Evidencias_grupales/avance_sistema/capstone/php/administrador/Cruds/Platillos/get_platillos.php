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

$id_platillo = $_GET['id'] ?? '';

if ($id_platillo) {
    $sql = "SELECT * FROM Platillos WHERE id_platillo=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_platillo);
    $stmt->execute();
    $result = $stmt->get_result();
    $platillo = $result->fetch_assoc();

    if ($platillo) {
        echo '<h2>Editar Platillo</h2>
              <form id="formEditarPlatillo" action="crud_platillos.php" method="post">
                  <input type="hidden" name="action" value="update">
                  <input type="hidden" name="id_platillo" value="' . htmlspecialchars($platillo['id_platillo']) . '">
                  <div class="mb-3">
                      <label for="nombre" class="form-label">Nombre</label>
                      <input type="text" class="form-control" id="nombre" name="nombre_platillo" value="' . htmlspecialchars($platillo['nombre_platillo']) . '" required>
                  </div>
                  <div class="mb-3">
                      <label for="descripcion" class="form-label">Descripción</label>
                      <textarea class="form-control" id="descripcion" name="descripcion_platillo" rows="3" required>' . htmlspecialchars($platillo['descripcion_platillo']) . '</textarea>
                  </div>
                  <div class="mb-3">
                      <label for="precio" class="form-label">Precio</label>
                      <input type="number" class="form-control" id="precio" name="precio" value="' . htmlspecialchars($platillo['precio']) . '" step="0.01" required>
                  </div>
                  <div class="mb-3">
                      <label for="estado" class="form-label">Estado</label>
                      <select class="form-select" id="estado" name="estado" required>
                          <option value="Disponible"' . ($platillo['estado'] === 'Disponible' ? ' selected' : '') . '>Disponible</option>
                          <option value="No Disponible"' . ($platillo['estado'] === 'No Disponible' ? ' selected' : '') . '>No Disponible</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label for="tiempo_preparacion" class="form-label">Tiempo de Preparación</label>
                      <input type="time" class="form-control" id="tiempo_preparacion" name="tiempo_preparacion" value="' . htmlspecialchars($platillo['tiempo_preparacion']) . '" required>
                  </div>
                  <div class="mb-3">
                      <label for="ruta_foto" class="form-label">Foto (URL)</label>
                      <input type="text" class="form-control" id="ruta_foto" name="ruta_foto" value="' . htmlspecialchars($platillo['ruta_foto']) . '">
                  </div>
                  <div class="mb-3">
                      <label for="tipo_platillo" class="form-label">Tipo de Platillo</label>
                      <select class="form-select" id="tipo_platillo" name="tipo_platillo" required>
                          <option value="Entrada"' . ($platillo['tipo_platillo'] === 'Entrada' ? ' selected' : '') . '>Entrada</option>
                          <option value="Plato Principal"' . ($platillo['tipo_platillo'] === 'Plato Principal' ? ' selected' : '') . '>Plato Principal</option>
                          <option value="Acompañamientos"' . ($platillo['tipo_platillo'] === 'Acompañamientos' ? ' selected' : '') . '>Acompañamientos</option>
                          <option value="Postres"' . ($platillo['tipo_platillo'] === 'Postres' ? ' selected' : '') . '>Postres</option>
                          <option value="Menú Infantil"' . ($platillo['tipo_platillo'] === 'Menú Infantil' ? ' selected' : '') . '>Menú Infantil</option>
                          <option value="Bebida"' . ($platillo['tipo_platillo'] === 'Bebida' ? ' selected' : '') . '>Bebida</option>
                      </select>
                  </div>
                  <button type="submit" class="btn btn-primary">Actualizar Platillo</button>
                  <button type="button" class="btn btn-secondary" onclick="document.getElementById(\'editarPlatilloForm\').style.display=\'none\'">Cancelar</button>
              </form>';
    } else {
        echo "Platillo no encontrado.";
    }
    $stmt->close();
}

$conn->close();
?>