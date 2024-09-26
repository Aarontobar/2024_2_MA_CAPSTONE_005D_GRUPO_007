<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Reservas</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../css/administrador.css">
    <script>
        function mostrarFormulario(formId, reservaId) {
            document.getElementById('formularioReservas').style.display = 'block';
            document.getElementById('formularioReservas').action = reservaId ? `edit_reserva.php?id=${reservaId}` : 'insert_reserva.php';
            if (reservaId) {
                // Rellenar el formulario con los datos de la reserva
                fetch(`get_reserva.php?id=${reservaId}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('nombre').value = data.nombre_reserva;
                        document.getElementById('apellido').value = data.apellido_reserva;
                        document.getElementById('cantidad_personas').value = data.cantidad_personas;
                        document.getElementById('hora').value = data.hora;
                        document.getElementById('fecha').value = data.fecha;
                        document.getElementById('id_mesa').value = data.id_mesa;
                        document.getElementById('estado').value = data.estado_reserva;
                    });
            } else {
                // Limpiar el formulario para una nueva reserva
                document.getElementById('nombre').value = '';
                document.getElementById('apellido').value = '';
                document.getElementById('cantidad_personas').value = '';
                document.getElementById('hora').value = '';
                document.getElementById('fecha').value = '';
                document.getElementById('id_mesa').value = '';
                document.getElementById('estado').value = 'Pendiente';
            }
        }

        function ocultarFormulario() {
            document.getElementById('formularioReservas').style.display = 'none';
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
                    <li class="nav-item"><a class="nav-link" href="admin_reservas.php">Reservas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Mesas</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Reportes</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-4">
            <h1>Administrar Reservas</h1>
            <button class="btn btn-primary" onclick="mostrarFormulario('formularioReservas')">Crear Reserva</button>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cantidad de Personas</th>
                    <th>Hora</th>
                    <th>Fecha</th>
                    <th>ID Mesa</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="reservasTableBody">
                <?php
                $conn = new mysqli("localhost", "root", "", "restaurante_bd");

                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM Reserva";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id_reserva']}</td>";
                        echo "<td>{$row['nombre_reserva']}</td>";
                        echo "<td>{$row['apellido_reserva']}</td>";
                        echo "<td>{$row['cantidad_personas']}</td>";
                        echo "<td>{$row['hora']}</td>";
                        echo "<td>{$row['fecha']}</td>";
                        echo "<td>{$row['id_mesa']}</td>";
                        echo "<td>{$row['estado_reserva']}</td>";
                        echo "<td><button class='btn btn-warning' onclick='mostrarFormulario(\"formularioReservas\", {$row['id_reserva']})'>Editar</button> <a href='delete_reserva.php?id={$row['id_reserva']}' class='btn btn-danger'>Eliminar</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No se encontraron reservas</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>

        <div id="formularioReservas" style="display:none;">
            <h2>Formulario de Reserva</h2>
            <form action="insert_reserva.php" method="post">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                </div>
                <div class="mb-3">
                    <label for="apellido" class="form-label">Apellido</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                </div>
                <div class="mb-3">
                    <label for="cantidad_personas" class="form-label">Cantidad de Personas</label>
                    <input type="number" class="form-control" id="cantidad_personas" name="cantidad_personas" required>
                </div>
                <div class="mb-3">
                    <label for="hora" class="form-label">Hora</label>
                    <input type="time" class="form-control" id="hora" name="hora" required>
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" required>
                </div>
                <div class="mb-3">
                    <label for="id_mesa" class="form-label">ID de Mesa</label>
                    <input type="number" class="form-control" id="id_mesa" name="id_mesa" required>
                </div>
                <div class="mb-3">
                    <label for="estado" class="form-label">Estado</label>
                    <select class="form-select" id="estado" name="estado" required>
                        <option value="Pendiente">Pendiente</option>
                        <option value="Realizada">Realizada</option>
                        <option value="Completada">Completada</option>
                        <option value="Cancelada">Cancelada</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Reserva</button>
                <button type="button" class="btn btn-secondary" onclick="ocultarFormulario()">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>