<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
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
    <?php include 'barra.php'; ?>

    <div class="content">
        <div class="container mt-5">
            <div class="d-flex justify-content-between mb-4">
                <h1>Administrar Usuarios</h1>
                <button class="btn btn-primary" onclick="toggleForm('crear')">Crear Usuario</button>
            </div>

            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre de Usuario</th>
                        <th>Nombre</th>
                        <th>Tipo de Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Conexión a la base de datos
                    $conn = new mysqli("localhost", "root", "", "restaurante_bd");
                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    // Obtener usuarios
                    $sql = "SELECT * FROM usuarios";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['id_usuario']}</td>
                                    <td>{$row['nombre_usuario']}</td>
                                    <td>{$row['nombre']}</td>
                                    <td>{$row['tipo_usuario']}</td>
                                    <td>
                                        <button class='btn btn-warning btn-sm' onclick='toggleForm(\"editar\", {$row['id_usuario']})'>Modificar</button>
                                        <a href='eliminar_usuario.php?id={$row['id_usuario']}' class='btn btn-danger btn-sm' onclick='return confirm(\"¿Estás seguro de que deseas eliminar este usuario?\");'>Eliminar</a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center'>No hay usuarios disponibles</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>

            <!-- Formulario para crear usuario -->
            <div id="crear-form" class="form-container">
                <h2>Crear Usuario</h2>
                <form action="crear_usuario.php" method="post">
                    <div class="mb-3">
                        <label for="nombre_usuario" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="nombre_usuario" name="nombre_usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" required>
                    </div>
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="rut" class="form-label">RUT</label>
                        <input type="text" class="form-control" id="rut" name="rut" required>
                    </div>
                    <div class="mb-3">
                        <label for="horario" class="form-label">Horario</label>
                        <input type="time" class="form-control" id="horario" name="horario" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                        <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                            <option value="administrador">Administrador</option>
                            <option value="cocina">Cocina</option>
                            <option value="mesero">Mesero</option>
                            <option value="metre">Metre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_ingreso" class="form-label">Fecha de Ingreso</label>
                        <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm('')">Cancelar</button>
                </form>
            </div>

            <!-- Formulario para editar usuario -->
            <div id="editar-form" class="form-container">
                <h2>Editar Usuario</h2>
                <form id="form-editar" action="editar_usuario.php" method="post">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <div class="mb-3">
                        <label for="nombre_usuario_edit" class="form-label">Nombre de Usuario</label>
                        <input type="text" class="form-control" id="nombre_usuario_edit" name="nombre_usuario" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena_edit" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena_edit" name="contrasena">
                    </div>
                    <div class="mb-3">
                        <label for="nombre_edit" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre_edit" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="rut_edit" class="form-label">RUT</label>
                        <input type="text" class="form-control" id="rut_edit" name="rut" required>
                    </div>
                    <div class="mb-3">
                        <label for="horario_edit" class="form-label">Horario</label>
                        <input type="time" class="form-control" id="horario_edit" name="horario" required>
                    </div>
                    <div class="mb-3">
                        <label for="tipo_usuario_edit" class="form-label">Tipo de Usuario</label>
                        <select class="form-select" id="tipo_usuario_edit" name="tipo_usuario" required>
                            <option value="administrador">Administrador</option>
                            <option value="cocina">Cocina</option>
                            <option value="mesero">Mesero</option>
                            <option value="metre">Metre</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="telefono_edit" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono_edit" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label for="email_edit" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email_edit" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="direccion_edit" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion_edit" name="direccion">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_ingreso_edit" class="form-label">Fecha de Ingreso</label>
                        <input type="date" class="form-control" id="fecha_ingreso_edit" name="fecha_ingreso" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleForm('')">Cancelar</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleForm(formType, userId = null) {
            const crearForm = document.getElementById('crear-form');
            const editarForm = document.getElementById('editar-form');
            
            if (formType === 'crear') {
                crearForm.style.display = 'block';
                editarForm.style.display = 'none';
            } else if (formType === 'editar') {
                crearForm.style.display = 'none';
                editarForm.style.display = 'block';

                // Cargar datos del usuario para edición
                fetch(`obtener_usuario.php?id=${userId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('id_usuario').value = data.id_usuario;
                        document.getElementById('nombre_usuario_edit').value = data.nombre_usuario;
                        document.getElementById('contrasena_edit').value = data.contrasena; // En caso de que quieras mostrar la contraseña actual, si no, puedes quitar este campo o modificarlo
                        document.getElementById('nombre_edit').value = data.nombre;
                        document.getElementById('rut_edit').value = data.rut;
                        document.getElementById('horario_edit').value = data.horario;
                        document.getElementById('tipo_usuario_edit').value = data.tipo_usuario;
                        document.getElementById('telefono_edit').value = data.telefono;
                        document.getElementById('email_edit').value = data.email;
                        document.getElementById('direccion_edit').value = data.direccion;
                        document.getElementById('fecha_ingreso_edit').value = data.fecha_ingreso;
                    });
            } else {
                crearForm.style.display = 'none';
                editarForm.style.display = 'none';
            }
        }
    </script>
</body>
</html>