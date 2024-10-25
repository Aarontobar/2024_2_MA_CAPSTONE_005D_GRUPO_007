<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Platillos</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .form-container {
            display: none;
            margin-top: 20px;
        }
        .sidebar {
            height: 100vh;
            background-color: #343a40;
            padding: 15px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            display: flex;
            align-items: center;
        }
        .sidebar a i {
            margin-right: 10px;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 17rem; /* Ajustar según el ancho de la barra de navegación */
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include '../barra.php'; ?>

    <div class="content">
        <div class="container mt-5">
            <div class="d-flex justify-content-between mb-4">
                <h1>Administrar Platillos</h1>
                <button class="btn btn-primary" onclick="document.getElementById('crearPlatilloForm').style.display='block'">Crear Platillo</button>
            </div>

            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th>Tiempo de Preparación</th>
                        <th>Tipo</th>
                        <th>Foto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="platillosTableBody">
                    <?php
                    $conn = new mysqli("localhost", "root", "", "restaurante_bd");
                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM Platillos";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='platillo_{$row['id_platillo']}'>
                                    <td>{$row['id_platillo']}</td>
                                    <td>{$row['nombre_platillo']}</td>
                                    <td>{$row['descripcion_platillo']}</td>
                                    <td>{$row['precio']}</td>
                                    <td>{$row['estado']}</td>
                                    <td>{$row['tiempo_preparacion']}</td>
                                    <td>{$row['tipo_platillo']}</td>
                                    <td><img src='{$row['ruta_foto']}' alt='Foto' width='100'></td>
                                    <td>
                                        <button class='btn btn-warning btn-sm' onclick='mostrarFormularioEditar({$row['id_platillo']})'>Modificar</button>
                                        <button class='btn btn-danger btn-sm' onclick='eliminarPlatillo({$row['id_platillo']})'>Eliminar</button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9' class='text-center'>No hay platillos disponibles</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>

            <div id="crearPlatilloForm" style="display:none;">
                <h2>Crear Nuevo Platillo</h2>
                <form id="formCrearPlatillo" action="crud_platillos.php" method="post">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre_platillo" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion_platillo" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Disponible">Disponible</option>
                            <option value="No Disponible">No Disponible</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tiempo_preparacion" class="form-label">Tiempo de Preparación</label>
                        <input type="time" class="form-control" id="tiempo_preparacion" name="tiempo_preparacion" required>
                    </div>
                    <div class="mb-3">
                        <label for="ruta_foto" class="form-label">Foto (URL)</label>
                        <input type="text" class="form-control" id="ruta_foto" name="ruta_foto">
                    </div>
                    <div class="mb-3">
                        <label for="tipo_platillo" class="form-label">Tipo de Platillo</label>
                        <select class="form-select" id="tipo_platillo" name="tipo_platillo" required>
                            <option value="Entrada">Entrada</option>
                            <option value="Plato Principal">Plato Principal</option>
                            <option value="Acompañamientos">Acompañamientos</option>
                            <option value="Postres">Postres</option>
                            <option value="Menú Infantil">Menú Infantil</option>
                            <option value="Bebida">Bebida</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear Platillo</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('crearPlatilloForm').style.display='none'">Cancelar</button>
                </form>
            </div>

            <div id="editarPlatilloForm" style="display:none;"></div>
        </div>
    </div>

    <script>
        function mostrarFormularioEditar(idPlatillo) {
            fetch('get_platillos.php?id=' + idPlatillo)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editarPlatilloForm').innerHTML = html;
                    document.getElementById('editarPlatilloForm').style.display = 'block';
                });
        }

        function eliminarPlatillo(idPlatillo) {
            if (confirm('¿Estás seguro de que deseas eliminar este platillo?')) {
                fetch('crud_platillos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'delete', id_platillo: idPlatillo })
                })
                .then(response => response.text())
                .then(() => location.reload());
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>