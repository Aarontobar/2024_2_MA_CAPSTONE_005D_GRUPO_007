document.querySelectorAll('.table-item').forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('active'); // Cambia el estado activo
    });
});

let shownNotifications = new Set(); // Almacena las notificaciones ya mostradas

function obtenerDatos() {
    // Obtener el parámetro id_usuario de la URL
    const params = new URLSearchParams(window.location.search);
    const id_usuario = params.get('id_usuario'); // id_usuario viene del enlace

    if (!id_usuario) {
        console.error('ID de mesero no encontrado en la URL');
        return;
    }

    // Aquí puedes continuar con la lógica de la función
    console.log('ID de mesero:', id_usuario);
    fetch(`get_mesas.php?id_usuario=${id_usuario}`)
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const tableList = document.querySelector('.table-list');
        tableList.innerHTML = ''; // Limpiar la lista actual

        // Llenar la lista con los nuevos datos
        data.mesas.forEach(mesa => {
            const estado_mesa = mesa.estado;
            const pedido_activo = data.pedidos[mesa.id_mesa];
        
            const listItem = document.createElement('li');
            listItem.classList.add('table-item', estado_mesa.toLowerCase().replace(' ', '-')); // Agregar clase según el estado
        
            const statusText = document.createElement('span');
            statusText.innerText = `Mesa ID: ${mesa.id_mesa}`;
            
            const statusSpan = document.createElement('span');
            statusSpan.classList.add('table-status');
        
            // Div para los detalles
            const detailsDiv = document.createElement('div');
            detailsDiv.classList.add('details');
        
            // Determinar el estado de la mesa y el pedido
            if (estado_mesa === 'Disponible') {
                statusSpan.innerText = "Mesa disponible";
                agregarNotificacion(mesa.id_mesa, "la mesa ha sido asignada y está disponible."); // Notificación
            } else if (estado_mesa === 'Ocupada' && !pedido_activo) {
                statusSpan.innerText = "Mesa ocupada. Debe tomar el pedido.";
                detailsDiv.innerHTML = `<p>La mesa está ocupada. Debe tomar el pedido.</p>
                                        <a href="../menu/ver_menu.php?mesa_id=${mesa.id_mesa}&id_usuario=${id_usuario}" class="action-button">Tomar Pedido</a>`;
            } else if (estado_mesa === 'Reservada') {
                statusSpan.innerText = "Mesa reservada";
                agregarNotificacion(mesa.id_mesa, "la mesa está reservada."); // Notificación
            } else if (estado_mesa === 'En Espera') {
                statusSpan.innerText = "Mesa en espera. Debe tomar el pedido.";
                detailsDiv.innerHTML = `<p>La mesa está en espera. Debe tomar el pedido.</p>`;
            } else if (estado_mesa === 'Para Limpiar') {
                statusSpan.innerText = "Mesa necesita limpieza";
                detailsDiv.innerHTML = `<p>La mesa necesita ser limpiada.</p>
                                        <button class="action-button" onclick="marcarMesaComoLimpia(${mesa.id_mesa})">Marcar como limpia</button>`;
            }
        
            // Asignar clase según el estado de la mesa
            switch (estado_mesa) {
                case 'Disponible':
                    listItem.classList.add('mesa-disponible');
                    break;
                case 'Ocupada':
                    listItem.classList.add('mesa-ocupada');
                    break;
                case 'Reservada':
                    listItem.classList.add('mesa-reservada');
                    break;
                case 'En Espera':
                    listItem.classList.add('mesa-en-espera');
                    break;
                case 'Para Limpiar':
                    listItem.classList.add('mesa-para-limpiar');
                    break;
            }
        
            // Mostrar estado del pedido si hay uno
            if (pedido_activo) {
                if (pedido_activo.estado === 'completado') {
                    statusSpan.innerText += " | Pedido completado";
                    detailsDiv.innerHTML = `<p>El pedido ha sido completado. La mesa debe ser limpiada.</p>
                                            <button class="action-button" onclick="marcarMesaComoLimpia(${mesa.id_mesa})">Marcar como limpia</button>`;
                    listItem.classList.add('pedido-completado'); // Clase adicional
                } else if (pedido_activo.estado === 'preparado') {
                    detailsDiv.innerHTML = `<p>El pedido está listo para ser llevado a la mesa.</p>
                                            <button class="action-button" data-pedido-id="${pedido_activo.id_pedido}">Pedido llevado</button>`;
                    
                    // Asignar el evento justo después de agregar el botón
                    const actionButton = detailsDiv.querySelector('.action-button');
                    actionButton.addEventListener('click', function() {
                        const pedidoId = this.getAttribute('data-pedido-id');
                        if (pedidoId) {
                            marcarPedidoComoLlevado(pedidoId); // Llama a la función para actualizar el estado
                        }
                    });
                } else {
                    detailsDiv.innerHTML = `<p><strong>Pedido ID:</strong> ${pedido_activo.id_pedido}</p>
                                            <p><strong>Total:</strong> $${pedido_activo.total_cuenta}</p>
                                            <p><strong>Hora del pedido:</strong> ${pedido_activo.hora}</p>`;
                }
            }
        
            // Agregar detalles a la lista
            listItem.appendChild(statusText);
            listItem.appendChild(statusSpan);
            listItem.appendChild(detailsDiv);
            tableList.appendChild(listItem);
        });
        

        // Reasignar el event listener después de agregar los nuevos elementos
        document.querySelectorAll('.table-item').forEach(item => {
            item.addEventListener('click', () => {
                item.classList.toggle('active'); // Cambia el estado activo
            });
        });
    })
    .catch(error => {
        console.error('Hubo un problema con la solicitud Fetch:', error);
    });
}

// Manejo de notificaciones
const notificationButton = document.getElementById('notification-button');
const notificationDropdown = document.getElementById('notification-dropdown');
const notificationList = document.getElementById('notification-list');

// Función para mostrar u ocultar el menú de notificaciones
notificationButton.addEventListener('click', () => {
    notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' : 'block';
});

// Función para agregar notificaciones
function agregarNotificacion(mesaId, mensaje) {
    const notificacionesDiv = document.getElementById('notificaciones'); // Asegúrate de que este div exista
    const notificacionItem = document.createElement('div');
    notificacionItem.classList.add('notificacion');
    notificacionItem.innerText = `Mesa ID ${mesaId}: ${mensaje}`;
    
    notificacionesDiv.appendChild(notificacionItem);
    
    // Opcional: Puedes agregar un temporizador para eliminar la notificación después de unos segundos
    setTimeout(() => {
        notificacionesDiv.removeChild(notificacionItem);
    }, 5000);
}

// Llamar a la función para obtener datos al cargar la página
obtenerDatos();

// Luego configurar el intervalo para obtener datos cada 5 segundos
setInterval(obtenerDatos, 35000);

const chatButton = document.getElementById('chat-button');
const chatDropdown = document.getElementById('chat-dropdown');
const activeChats = document.getElementById('active-chats');
const chatMessages = document.getElementById('chat-messages');
const messagesList = document.getElementById('messages-list');
const messageInput = document.getElementById('message-input');
const sendMessageButton = document.getElementById('send-message');

let selectedChatId = null; // Variable para almacenar el chat seleccionado

// Función para mostrar/ocultar el menú de chat
chatButton.addEventListener('click', () => {
    chatDropdown.style.display = chatDropdown.style.display === 'block' ? 'none' : 'block';
});

// Ejemplo de chats activos
const chats = [
    { id: 1, nombre: 'Usuario 1' },
    { id: 2, nombre: 'Usuario 2' },
];

// Agregar chats activos a la lista
chats.forEach(chat => {
    const li = document.createElement('li');
    li.textContent = chat.nombre;
    li.addEventListener('click', () => {
        selectedChatId = chat.id;
        mostrarMensajes(chat.id); // Mostrar mensajes al seleccionar un chat
    });
    activeChats.appendChild(li);
});

// Función para mostrar los mensajes de un chat seleccionado
function mostrarMensajes(chatId) {
    messagesList.innerHTML = ''; // Limpiar mensajes previos
    chatMessages.style.display = 'block'; // Mostrar el área de mensajes

    // Ejemplo de mensajes
    const mensajes = [
        { chatId: 1, texto: 'Hola, ¿cómo estás?' },
        { chatId: 1, texto: 'Todo bien, gracias.' },
        { chatId: 2, texto: '¿Qué tal el trabajo?' },
    ];

    // Filtrar y mostrar solo los mensajes del chat seleccionado
    mensajes.forEach(mensaje => {
        if (mensaje.chatId === chatId) {
            const p = document.createElement('p');
            p.textContent = mensaje.texto;
            messagesList.appendChild(p);
        }
    });
}

// Enviar un nuevo mensaje
sendMessageButton.addEventListener('click', () => {
    if (selectedChatId && messageInput.value.trim()) {
        const nuevoMensaje = messageInput.value.trim();
        // Aquí podrías enviar el mensaje al servidor, si es necesario
        const p = document.createElement('p');
        p.textContent = nuevoMensaje;
        messagesList.appendChild(p);
        messageInput.value = ''; // Limpiar el input
    }
});

// Asegúrate de agregar este código después de que el contenido de `detailsDiv` se actualice.
document.querySelectorAll('.action-button').forEach(button => {
    button.addEventListener('click', function() {
        const pedidoId = this.getAttribute('data-pedido-id');
        
        // Verifica que el botón tiene un ID de pedido válido
        if (pedidoId) {
            marcarPedidoComoLlevado(pedidoId); // Llama a la función que cambiará el estado del pedido
        }
    });
});

function marcarPedidoComoLlevado(pedidoId) {
    // Realiza una petición AJAX al servidor
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../mesero/estado_pedido.php', true); // Archivo PHP que manejará el cambio de estado
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('El pedido ha sido marcado como servido.'); // Mensaje de éxito
            location.reload(); // Recargar la página para reflejar los cambios
        } else {
            alert('Error al actualizar el pedido: ' + xhr.responseText);
        }
    };

    // Enviar el ID del pedido y el nuevo estado al servidor
    xhr.send('id_pedido=' + pedidoId + '&nuevo_estado=servido');
}

function marcarMesaComoLimpia(id_mesa) {
    const data = new FormData();
    data.append('id_mesa', id_mesa);

    fetch('marcar_mesa_limpia.php', {
        method: 'POST',
        body: data,
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            agregarNotificacion(id_mesa, data.message);
            // Aquí puedes actualizar la UI para reflejar que la mesa está disponible
            // Por ejemplo, puedes recargar la información de la mesa o actualizar su estado visualmente
        } else {
            agregarNotificacion(id_mesa, data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        agregarNotificacion(id_mesa, "Error al marcar la mesa como limpia.");
    });
}