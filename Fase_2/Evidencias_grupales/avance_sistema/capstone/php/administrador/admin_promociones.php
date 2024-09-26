<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Promociones</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/administrador.css">
    <script>
        function agregarCondicion() {
            const contenedor = document.getElementById('condiciones');
            const nuevoInput = document.createElement('div');
            nuevoInput.classList.add('mb-3');
            nuevoInput.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">Tipo de Condición</label>
                    <select class="form-select" name="tipo_condicion[]" onchange="actualizarCamposCondicion(this)">
                        <option value="fecha">Fecha</option>
                        <option value="hora">Hora</option>
                        <option value="cantidad_personas">Cantidad de Personas</option>
                        <option value="cantidad_platillos">Cantidad de Platillos</option>
                    </select>
                </div>
                <div class="condiciones-campos"></div>
                <button type="button" class="btn btn-danger mt-2" onclick="eliminarCondicion(this)">Eliminar</button>
            `;
            contenedor.appendChild(nuevoInput);
        }

        function actualizarCamposCondicion(select) {
            const tipo = select.value;
            const contenedorCampos = select.nextElementSibling;
            contenedorCampos.innerHTML = ''; // Limpiar campos anteriores

            if (tipo === 'fecha') {
                contenedorCampos.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Fecha de Inicio</label>
                        <input type="date" class="form-control" name="inicio[]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Fecha de Fin</label>
                        <input type="date" class="form-control" name="fin[]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Platillo</label>
                        <input type="text" class="form-control" name="tipo_platillo[]" required>
                    </div>
                `;
            } else if (tipo === 'hora') {
                contenedorCampos.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Hora de Inicio</label>
                        <input type="time" class="form-control" name="hora_inicio[]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Hora de Fin</label>
                        <input type="time" class="form-control" name="hora_fin[]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Platillo</label>
                        <input type="text" class="form-control" name="tipo_platillo[]" required>
                    </div>
                `;
            } else if (tipo === 'cantidad_personas') {
                contenedorCampos.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Número Mínimo de Personas</label>
                        <input type="number" class="form-control" name="minimo_personas[]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Platillo</label>
                        <input type="text" class="form-control" name="tipo_platillo[]" required>
                    </div>
                `;
            } else if (tipo === 'cantidad_platillos') {
                contenedorCampos.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Número Mínimo de Platillos</label>
                        <input type="number" class="form-control" name="minimo_platillos[]" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Platillo</label>
                        <input type="text" class="form-control" name="tipo_platillo[]" required>
                    </div>
                `;
            }
        }

        function eliminarCondicion(button) {
            button.parentElement.remove();
        }

        function actualizarCamposAccion() {
            const tipoAccion = document.getElementById('tipo_accion').value;
            const contenedorCampos = document.getElementById('camposAccion');
            contenedorCampos.innerHTML = ''; // Limpiar campos anteriores

            if (tipoAccion === 'descuento_platillos') {
                contenedorCampos.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Descuento (%)</label>
                        <input type="number" class="form-control" id="valor" name="valor" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tipo de Platillo</label>
                        <input type="text" class="form-control" id="tipo_platillo_accion" name="tipo_platillo_accion" required>
                    </div>
                `;
            } else if (tipoAccion === 'platillo_gratis') {
                contenedorCampos.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Tipo de Platillo Gratis</label>
                        <input type="text" class="form-control" id="tipo_platillo_gratis" name="tipo_platillo_gratis" required>
                    </div>
                `;
            } else if (tipoAccion === '2x1') {
                contenedorCampos.innerHTML = `
                    <div class="mb-3">
                        <label class="form-label">Tipo de Platillo</label>
                        <input type="text" class="form-control" id="tipo_platillo_2x1" name="tipo_platillo_2x1" required>
                    </div>
                `;
            }
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="administrador.php">Administrador</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="admin_usuarios.php">Usuarios</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_platillos.php">Platillos</a></li>                   
                    <li class="nav-item"><a class="nav-link" href="#">Reservas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Mesas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reportes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h1>Administrar Promociones</h1>
            <button class="btn btn-primary" onclick="document.getElementById('crearPromocionForm').style.display='block'">Crear Promoción</button>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Descuento (%)</th>
                    <th>Estado</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="promocionesTableBody">
                <?php
                $conn = new mysqli("localhost", "root", "", "restaurante_bd");

                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM promociones";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id_promocion']}</td>";
                        echo "<td>{$row['nombre_promocion']}</td>";
                        echo "<td>{$row['descripcion']}</td>";
                        echo "<td>{$row['descuento']}</td>";
                        echo "<td>{$row['estado']}</td>";
                        echo "<td><img src='{$row['ruta_foto']}' alt='{$row['nombre_promocion']}' width='100'></td>";
                        echo "<td><a href='edit_promocion.php?id={$row['id_promocion']}' class='btn btn-warning'>Editar</a> <a href='delete_promocion.php?id={$row['id_promocion']}' class='btn btn-danger'>Eliminar</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No se encontraron promociones</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>

        <div id="crearPromocionForm" style="display:none;">
            <h2>Crear Promoción</h2>
            <form action="insert_promocion.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre de la Promoción</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="descuento" class="form-label">Descuento (%)</label>
                    <input type="number" class="form-control" id="descuento" name="descuento" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="Activo">Activo</option>
                        <option value="Inactivo">Inactivo</option>
                    </select>
                </div>

                <!-- Condiciones -->
                <h3>Condiciones</h3>
                <div id="condiciones">
                    <!-- Aquí se agregarán las condiciones dinámicamente -->
                </div>
                <button type="button" class="btn btn-secondary mt-2" onclick="agregarCondicion()">Agregar Condición</button>

                <!-- Acción -->
                <h3>Acción</h3>
                <div class="mb-3">
                    <label for="tipo_accion" class="form-label">Tipo de Acción</label>
                    <select class="form-select" id="tipo_accion" name="tipo_accion" onchange="actualizarCamposAccion()" required>
                        <option value="descuento_platillos">Descuento en Platillos</option>
                        <option value="platillo_gratis">Platillo Gratis</option>
                        <option value="2x1">2x1</option>
                    </select>
                </div>
                <div id="camposAccion">
                    <!-- Aquí se agregarán los campos para la acción seleccionada -->
                </div>

                <div class="mb-3">
                    <label for="ruta_foto" class="form-label">Foto</label>
                    <input type="file" class="form-control" id="ruta_foto" name="ruta_foto" required>
                </div>

                <button type="submit" class="btn btn-primary">Crear Promoción</button>
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('crearPromocionForm').style.display='none'">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>