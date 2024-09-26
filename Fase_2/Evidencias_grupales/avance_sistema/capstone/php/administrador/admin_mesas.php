<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Mesas</title>
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
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 17rem;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php include 'barra.php'; ?>

    <div class="content">
        <div class="container mt-5">
            <div class="d-flex justify-content-between mb-4">
                <h1>Administrar Mesas</h1>
                <button class="btn btn-primary" onclick="document.getElementById('crearMesaForm').style.display='block'">Crear Mesa</button>
            </div>

            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Cantidad de Asientos</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="mesasTableBody">
                    <?php
                    $conn = new mysqli("localhost", "root", "", "restaurante_bd");
                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    $sql = "SELECT * FROM Mesa";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr id='mesa_{$row['id_mesa']}'>
                                    <td>{$row['id_mesa']}</td>
                                    <td>{$row['cantidad_asientos']}</td>
                                    <td>{$row['estado']}</td>
                                    <td>
                                        <button class='btn btn-warning btn-sm' onclick='mostrarFormularioEditar({$row['id_mesa']})'>Modificar</button>
                                        <button class='btn btn-danger btn-sm' onclick='eliminarMesa({$row['id_mesa']})'>Eliminar</button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center'>No hay mesas disponibles</td></tr>";
                    }

                    $conn->close();
                    ?>
                </tbody>
            </table>

            <!-- Formulario Crear Mesa -->
            <div id="crearMesaForm" class="form-container">
                <h2>Crear Nueva Mesa</h2>
                <form id="formCrearMesa" action="crud_mesas.php" method="post">
                    <input type="hidden" name="action" value="create">
                    <div class="mb-3">
                        <label for="cantidad_asientos" class="form-label">Cantidad de Asientos</label>
                        <input type="number" class="form-control" id="cantidad_asientos" name="cantidad_asientos" required>
                    </div>
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupada">Ocupada</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Crear Mesa</button>
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('crearMesaForm').style.display='none'">Cancelar</button>
                </form>
            </div>

            <!-- Formulario Editar Mesa -->
            <div id="editarMesaForm" style="display:none;"></div>
        </div>
    </div>

    <script>
        function mostrarFormularioEditar(idMesa) {
            fetch('get_mesas.php?id=' + idMesa)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('editarMesaForm').innerHTML = html;
                    document.getElementById('editarMesaForm').style.display = 'block';
                });
        }

        function eliminarMesa(idMesa) {
            if (confirm('¿Estás seguro de que deseas eliminar esta mesa?')) {
                fetch('crud_mesas.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: 'delete', id_mesa: idMesa })
                })
                .then(response => response.text())
                .then(() => location.reload());
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>