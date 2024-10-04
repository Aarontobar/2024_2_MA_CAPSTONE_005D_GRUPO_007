document.querySelectorAll('.table-item').forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('active'); // Cambia el estado activo
    });
});

let shownNotifications = new Set(); // Almacena las notificaciones ya mostradas

function obtenerDatos() {
    const id_usuario = 4; // El ID del usuario
    fetch(`get_mesas.php?id_mesero=${id_usuario}`)
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

                // Determinar el estado de la mesa y el pedido
                if (estado_mesa === 'Disponible') {
                    statusSpan.innerText = "Mesa disponible";
                    agregarNotificacion(mesa.id_mesa, "la mesa ha sido asignada y está disponible."); // Notificación
                } else if (estado_mesa === 'Ocupada' && !pedido_activo) {
                    statusSpan.innerText = "Mesa ocupada. Debe tomar el pedido.";
                    agregarNotificacion(mesa.id_mesa, "la mesa está ocupada, debe tomar el pedido."); // Notificación
                } else if (estado_mesa === 'Reservada') {
                    statusSpan.innerText = "Mesa reservada";
                    agregarNotificacion(mesa.id_mesa, "la mesa está reservada."); // Notificación
                } else if (estado_mesa === 'En Espera') {
                    statusSpan.innerText = "Mesa en espera. Debe tomar el pedido.";
                    agregarNotificacion(mesa.id_mesa, "la mesa está en espera, debe tomar el pedido."); // Notificación
                } else if (estado_mesa === 'Para Limpiar') {
                    statusSpan.innerText = "Mesa necesita limpieza";
                    agregarNotificacion(mesa.id_mesa, "la mesa necesita limpieza"); // Notificación
                }

                // Asignar clase según el estado de la mesa
                if (estado_mesa === 'Disponible') {
                    listItem.classList.add('mesa-disponible');
                } else if (estado_mesa === 'Ocupada') {
                    listItem.classList.add('mesa-ocupada');
                } else if (estado_mesa === 'Reservada') {
                    listItem.classList.add('mesa-reservada');
                } else if (estado_mesa === 'En Espera') {
                    listItem.classList.add('mesa-en-espera');
                } else if (estado_mesa === 'Para Limpiar') {
                    listItem.classList.add('mesa-para-limpiar');
                }

                // Mostrar estado del pedido si hay uno
                if (pedido_activo) {
                    if (pedido_activo.estado === 'preparado') {
                        statusSpan.innerText += " | Pedido preparado";
                        agregarNotificacion(mesa.id_mesa, "el pedido está listo para ser llevado."); // Notificación
                    } else if (pedido_activo.estado === 'en preparación') {
                        statusSpan.innerText += " | Pedido en preparación";
                    } else if (pedido_activo.estado === 'recibido') {
                        statusSpan.innerText += " | Pedido recibido";
                    } else if (pedido_activo.estado === 'servido') {
                        statusSpan.innerText += " | Pedido servido";
                    } else if (pedido_activo.estado === 'completado') {
                        statusSpan.innerText += " | Pedido completado";
                    } else if (pedido_activo.estado === 'cancelado') {
                        statusSpan.innerText += " | Pedido cancelado";
                    }
                }

                listItem.appendChild(statusText);
                listItem.appendChild(statusSpan);

                // Detalles
                const detailsDiv = document.createElement('div');
                detailsDiv.classList.add('details');

                if (estado_mesa === 'Ocupada' && !pedido_activo) {
                    detailsDiv.innerHTML = `<p>La mesa está ocupada. Debe tomar el pedido.</p>
                                            <a href="../menu/ver_menu.php?id_mesa=${mesa.id_mesa}&id_mesero=${id_usuario}" class="action-button">Tomar Pedido</a>`;
                } else if (estado_mesa === 'Para Limpiar') {
                    detailsDiv.innerHTML = `<p>La mesa necesita ser limpiada.</p>
                                            <button class="action-button">Marcar como limpia</button>`;
                } else if (pedido_activo) {
                    if (pedido_activo.estado === 'preparado') {
                        detailsDiv.innerHTML = `<p>El pedido está listo para ser llevado a la mesa.</p>
                                                <button class="action-button">Llevar Pedido</button>`;
                    } else {
                        detailsDiv.innerHTML = `<p><strong>Pedido ID:</strong> ${pedido_activo.id_pedido}</p>
                                                <p><strong>Total:</strong> $${pedido_activo.total_cuenta}</p>
                                                <p><strong>Hora del pedido:</strong> ${pedido_activo.hora}</p>`;
                    }
                }

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
    const notificacionKey = `Mesa(${mesaId}): ${mensaje}`; // Clave única para la notificación
    if (!shownNotifications.has(notificacionKey)) { // Verificar si ya se mostró
        const li = document.createElement('li');
        li.textContent = notificacionKey;
        notificationList.appendChild(li);
        shownNotifications.add(notificacionKey); // Marca la notificación como mostrada
    }
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