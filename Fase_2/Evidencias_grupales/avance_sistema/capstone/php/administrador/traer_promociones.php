<?php
$conn = new mysqli("localhost", "root", "", "restaurante_bd");
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql = "SELECT * FROM promociones WHERE id_promocion = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$promocion = $result->fetch_assoc();

$foto = $promocion['ruta_foto'] ? "<img src='{$promocion['ruta_foto']}' alt='Foto' style='width: 100px; height: auto;'>" : "No disponible";

echo '<h2>Editar Promoción</h2>
      <form id="formEditarPromocion" action="crud_promociones.php" method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id_promocion" value="' . $promocion['id_promocion'] . '">
          <div class="mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" value="' . $promocion['nombre_promocion'] . '" required>
          </div>
          <div class="mb-3">
              <label for="descripcion" class="form-label">Descripción</label>
              <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required>' . $promocion['descripcion'] . '</textarea>
          </div>
          <div class="mb-3">
              <label for="descuento" class="form-label">Descuento (%)</label>
              <input type="number" class="form-control" id="descuento" name="descuento" step="0.01" value="' . $promocion['descuento'] . '" required>
          </div>
          <div class="mb-3">
              <label for="estado" class="form-label">Estado</label>
              <select class="form-select" id="estado" name="estado" required>
                  <option value="Activo"' . ($promocion['estado'] == 'Activo' ? ' selected' : '') . '>Activo</option>
                  <option value="Inactivo"' . ($promocion['estado'] == 'Inactivo' ? ' selected' : '') . '>Inactivo</option>
              </select>
          </div>
          <div class="mb-3">
              <label for="foto" class="form-label">Foto/Banner</label>
              <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
              <p>Foto actual: ' . $foto . '</p>
          </div>
          <div class="mb-3">
              <label for="condiciones" class="form-label">Condiciones</label>
              <select class="form-select" id="condiciones" name="condiciones[]" multiple required>
                  <option value="minimo_compra"' . (in_array('minimo_compra', json_decode($promocion['condiciones'])) ? ' selected' : '') . '>Mínimo de Compra</option>
                  <option value="porcentaje_categoria"' . (in_array('porcentaje_categoria', json_decode($promocion['condiciones'])) ? ' selected' : '') . '>Porcentaje de Categoría</option>
                  <option value="compra_especifica"' . (in_array('compra_especifica', json_decode($promocion['condiciones'])) ? ' selected' : '') . '>Compra Específica</option>
                  <!-- Agrega más opciones según sea necesario -->
              </select>
          </div>
          <div class="mb-3">
              <label for="accion" class="form-label">Acción</label>
              <select class="form-select" id="accion" name="accion" required>
                  <option value="descuento"' . ($promocion['accion'] == 'descuento' ? ' selected' : '') . '>Descuento</option>
                  <option value="producto_gratuito"' . ($promocion['accion'] == 'producto_gratuito' ? ' selected' : '') . '>Producto Gratuito</option>
                  <option value="compra_uno_lleva_dos"' . ($promocion['accion'] == 'compra_uno_lleva_dos' ? ' selected' : '') . '>Compra Uno Lleva Dos</option>
                  <!-- Agrega más opciones según sea necesario -->
              </select>
          </div>
          <button type="submit" class="btn btn-primary">Actualizar Promoción</button>
          <button type="button" class="btn btn-secondary" onclick="document.getElementById(\'editarPromocionForm\').style.display=\'none\'">Cancelar</button>
      </form>';

$stmt->close();
$conn->close();
?>