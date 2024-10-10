<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Mesero</title>
    <style>
        /* Agrega estilos básicos para la lista de meseros */
        .mesero-list {
            list-style-type: none;
            padding: 0;
        }
        .mesero-item {
            margin: 1rem 0;
        }
        .mesero-button {
            padding: 0.5rem 1rem;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .mesero-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Seleccionar Mesero</h1>
    <ul id="mesero-list" class="mesero-list">
        <!-- La lista de meseros se llenará aquí -->
    </ul>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function loadMeseros() {
                fetch('get_mesero.php') // Asume que tienes un archivo get_meseros.php que devuelve todos los meseros
                    .then(response => response.json())
                    .then(data => {
                        const list = document.getElementById('mesero-list');
                        list.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos meseros

                        data.forEach(mesero => {
                            const listItem = document.createElement('li');
                            listItem.className = 'mesero-item';
                            listItem.innerHTML = `
                                <span>Mesero: ${mesero.nombre}</span>
                                <a class="mesero-button" href="mesero.php?id_usuario=${mesero.id_usuario}">Ver Mesas</a>
                            `;
                            list.appendChild(listItem);
                        });
                    })
                    .catch(error => {
                        console.error('Error al cargar los meseros:', error);
                    });
            }

            loadMeseros();
        });
    </script>
</body>
</html>
