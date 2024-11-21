<?php
include '../modulos/conexion.php';

// Obtener el ID del usuario desde la URL
$id_usuario = isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="../../css/chat.css">
</head>
<body>

<div id="chat-icon" onclick="toggleChatBox()">
    💬
</div>

<div id="chat-box" style="display: none;">
    <input type="text" id="search-user" placeholder="Buscar usuario" onkeyup="buscarUsuario()">
    <ul id="user-list">
        <!-- La lista de usuarios se cargará aquí -->
    </ul>
    <div id="chat-messages" style="display: none;">
        <div id="chat-header">
            <button onclick="volverALista()">←</button>
            <div class="user-profile">
                <div class="profile-pic" id="chat-profile-pic"></div>
                <span class="user-name" id="chat-user-name"></span>
            </div>
        </div>
        <div id="messages-list"></div>
        <div id="message-container">
            <input type="text" id="message-input" placeholder="Escribe un mensaje">
            <button id="send-message"><img src="../../imagenes/enviar.png" alt="Enviar" width="20" style="filter: grayscale(100%);"></button>
        </div>
    </div>
</div>

<input type="hidden" id="selected-user-id" value="<?php echo $id_usuario; ?>">

<script src="../../js/chat.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        cargarUsuarios();
    });

    function cargarUsuarios() {
    fetch('../modulos/cargar_usuarios_2.php?id_usuario=<?php echo $id_usuario; ?>')
        .then(response => response.json())
        .then(usuarios => {
            const userList = document.getElementById('user-list');
            userList.innerHTML = ''; // Limpiar la lista de usuarios
            usuarios.forEach(usuario => {
                // Evitar incluir al usuario conectado
                if (usuario.id_usuario !== <?php echo $id_usuario; ?>) {
                    userList.innerHTML += `
                        <li onclick="cargarChat(${usuario.id_usuario}, '${usuario.nombre_usuario}')">
                            <div class="user-profile">
                                <div class="profile-pic"></div>
                                <div class="user-info">
                                    <span class="user-name">${usuario.nombre_usuario}</span>
                                    ${usuario.ultimo_mensaje ? `<span class="last-message">${usuario.ultimo_mensaje}</span>` : ''}
                                </div>
                            </div>
                        </li>`;
                }
            });
        });
}

    function cargarChat(id_destinatario, nombre_destinatario) {
        // Verifica que el ID del destinatario sea diferente del usuario actual
        if (id_destinatario === <?php echo $id_usuario; ?>) {
            alert("No puedes chatear contigo mismo."); // Alerta si se intenta seleccionar el propio usuario
            return;
        }

        document.getElementById('chat-messages').style.display = 'block';
        document.getElementById('selected-user-id').value = id_destinatario; // Guardar el ID del destinatario
        document.getElementById('messages-list').innerHTML = ''; // Limpiar los mensajes previos
        document.getElementById('user-list').style.display = 'none'; // Ocultar la lista de usuarios

        // Establecer la imagen de perfil y el nombre del usuario seleccionado
        document.getElementById('chat-user-name').textContent = nombre_destinatario;
        document.getElementById('chat-profile-pic').style.backgroundImage = `url('ruta/a/la/imagen/de/perfil/${id_destinatario}.jpg')`;

        cargarMensajes(id_destinatario);
    }

    function cargarMensajes(id_destinatario) {
        const id_usuario = <?php echo isset($_GET['id_usuario']) ? intval($_GET['id_usuario']) : 0; ?>;

        fetch(`../modulos/cargar_mensajes.php?id_usuario=${id_usuario}&id_destinatario=${id_destinatario}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(mensajes => {
                const messagesList = document.getElementById('messages-list');
                messagesList.innerHTML = ''; // Limpiar mensajes previos
                
                mensajes.forEach(mensaje => {
                    const messageElement = document.createElement('p');
                    messageElement.innerHTML = `${mensaje.mensaje}<br><em>${mensaje.fecha_hora}</em>`;
                    
                    if (mensaje.id_usuario_envia == id_usuario) {
                        messageElement.classList.add('sent');
                    } else {
                        messageElement.classList.add('received');
                    }
                    
                    messagesList.appendChild(messageElement);
                });

                // Desplazar al último mensaje
                messagesList.scrollTop = messagesList.scrollHeight;
            })
            .catch(error => console.error('Error al cargar mensajes:', error));
    }

    function volverALista() {
        document.getElementById('chat-messages').style.display = 'none';
        document.getElementById('user-list').style.display = 'block'; // Mostrar la lista de usuarios
    }

    // Obtener el ID del usuario que envía el mensaje desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const id_usuario_envia = urlParams.get('id_usuario'); // Obtiene el ID del mesero

    document.getElementById('send-message').onclick = function() {
        const id_destinatario = document.getElementById('selected-user-id').value; // Asigna el ID del usuario seleccionado
        const mensaje = document.getElementById('message-input').value;

        if (mensaje.trim() === "") return; // Evitar enviar mensajes vacíos

        fetch('../modulos/enviar_mensaje.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_destinatario=${id_destinatario}&mensaje=${mensaje}&id_usuario_envia=${id_usuario_envia}`
        }).then(() => {
            document.getElementById('message-input').value = ''; // Limpiar el campo de entrada
            cargarMensajes(id_destinatario); // Recargar mensajes
        });
    };

    function toggleChatBox() {
        const chatBox = document.getElementById('chat-box');
        chatBox.style.display = chatBox.style.display === 'none' ? 'block' : 'none';
    }

    function buscarUsuario() {
        const filter = document.getElementById('search-user').value.toLowerCase();
        const userList = document.getElementById('user-list');
        const users = userList.getElementsByTagName('li');

        for (let i = 0; i < users.length; i++) {
            const txtValue = users[i].textContent || users[i].innerText;
            users[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }

    // Llamada inicial para cargar los mensajes si hay un destinatario seleccionado
    const id_destinatario = document.getElementById('selected-user-id').value;
    if (id_destinatario) {
        cargarMensajes(id_destinatario);
    }

    // Configura el polling para cargar nuevos mensajes cada 3 segundos
    setInterval(() => {
        const id_destinatario = document.getElementById('selected-user-id').value; // Obtener el ID del destinatario seleccionado
        if (id_destinatario) {
            cargarMensajes(id_destinatario);
        }
    }, 3000); // Cambia el tiempo según sea necesario (en milisegundos)
</script>
</body>
</html>