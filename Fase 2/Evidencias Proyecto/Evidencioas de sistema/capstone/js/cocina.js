document.addEventListener('DOMContentLoaded', function() {
    let lastUpdate = 0; // Variable para rastrear el último tiempo de actualización
    let pedidosData = []; // Almacenar los pedidos cargados

    function playSound() {
        const audio = new Audio('../sonidos/level-up-191997.mp3'); // Ruta al archivo de sonido
        audio.play().catch(error => console.error('Error al reproducir el sonido:', error));
    }

    function loadPedidos() {
        fetch('get_pedidos.php')
            .then(response => response.json())
            .then(data => {
                pedidosData = data; // Guardar pedidos para aplicar filtros
                filterAndDisplayPedidos();
                lastUpdate = Date.now(); // Actualizar el tiempo de la última actualización
            })
            .catch(error => {
                console.error('Error al cargar los pedidos:', error);
            });
    }

    function filterAndDisplayPedidos() {
        const orderFilter = document.getElementById('order-filter').value;
        const typeFilter = document.getElementById('type-filter').value;
        
        const container = document.querySelector('.orders-container');
        container.innerHTML = ''; // Limpiar el contenedor
    
        // Filtrar pedidos según los filtros seleccionados
        const filteredPedidos = pedidosData.filter(pedido => {
            let orderMatch = true;
            let typeMatch = true;
    
            // Filtrar por estado del pedido
            if (orderFilter === 'new') {
                orderMatch = pedido.estado === 'recibido';
            } else if (orderFilter === 'waiting') {
                orderMatch = pedido.estado === 'en preparación';
            } else if (orderFilter === 'delayed') {
                const fechaPedido = new Date(`${pedido.fecha} ${pedido.hora}`);
                const ahora = new Date();
                const diferenciaMinutos = Math.floor((ahora - fechaPedido) / (1000 * 60));
                orderMatch = diferenciaMinutos > 35;
            }
    
            if (typeFilter === 'pickup') {
                typeMatch = pedido.tipo === 'Para Llevar';
            } else if (typeFilter === 'dine-in') {
                typeMatch = pedido.tipo === 'Para Servir';
            }
    
            return orderMatch && typeMatch;
        });
    
        // Separar pedidos prioritarios y no prioritarios
        const prioritarios = filteredPedidos.filter(pedido => pedido.prioridad === 'prioritario');
        const normales = filteredPedidos.filter(pedido => pedido.prioridad !== 'prioritario');
    
        // Combinar pedidos prioritarios y normales (prioritarios primero)
        const pedidosCombinados = [...prioritarios, ...normales];
    
        // Mostrar los pedidos combinados
        pedidosCombinados.forEach(pedido => {
            console.log(`Pedido #${pedido.id_pedido} tiene prioridad: ${pedido.prioridad}`); // Mostrar la prioridad en la consola
        
            const card = document.createElement('div');
            card.className = 'order-card';
        
            // Asignar clase a los pedidos prioritarios
            if (pedido.prioridad === 'prioritario') {
                card.classList.add('prioritario'); // Añadir clase para prioridad
            }
        
            // Calcular el tiempo de espera en minutos solo para pedidos no prioritarios
            const fechaPedido = new Date(`${pedido.fecha} ${pedido.hora}`);
            const ahora = new Date();
            const diferenciaMinutos = Math.floor((ahora - fechaPedido) / (1000 * 60));
        
            // Asignar clase según el tiempo de espera para pedidos normales
            if (pedido.prioridad !== 'prioritario') {
                if (diferenciaMinutos > 35) {
                    card.classList.add('urgent'); // Clase CSS para pedidos atrasados
                } else {
                    card.classList.add('normal'); // Clase CSS para pedidos normales
                }
            }
        
            // Asignar clases a los botones según el tiempo de espera
            let botonEstadoClass;
            let botonDetallesClass;
        
            if (diferenciaMinutos <= 20) {
                botonEstadoClass = 'button-recién-pedido';
                botonDetallesClass = 'button-recién-pedido';
            } else if (diferenciaMinutos <= 35) {
                botonEstadoClass = 'button-en-espera';
                botonDetallesClass = 'button-en-espera';
            } else {
                botonEstadoClass = 'button-atrasado';
                botonDetallesClass = 'button-atrasado';
            }
        
            // Asegurarse de que platillos y bebidas sean arrays
            const platillos = Array.isArray(pedido.platillos) ? pedido.platillos : [];
            const bebidas = Array.isArray(pedido.bebidas) ? pedido.bebidas : [];
        
            const detailsHTML = platillos.map(p => 
                `<p>${p.nombre_platillo} - Cantidad: ${p.cantidad}</p>`
            ).join('') + bebidas.map(b => 
                `<p>${b.nombre_bebida} - Cantidad: ${b.cantidad}</p>`
            ).join('');
        
            card.innerHTML = `
                <h2>Pedido #${pedido.id_pedido}</h2>
                <p>Total: $${pedido.total_cuenta} - ${platillos.length + bebidas.length} items</p>
                <div class="buttons">
                    <button class="${botonEstadoClass}" onclick="nextStatus(${pedido.id_pedido})">Cambiar Estado</button>
                    <button class="${botonDetallesClass}" onclick="toggleDetails(${pedido.id_pedido})">Expandir</button>
                </div>
                <div id="details-${pedido.id_pedido}" class="detalles" style="display: none;">
                    <h4>Detalles del Pedido:</h4>
                    ${detailsHTML}
                </div>
            `;
        
            container.appendChild(card);
        });
    }

    // Cargar pedidos al iniciar la página
    loadPedidos();

    // Actualizar cada 10 segundos (10000 ms)
    setInterval(loadPedidos, 10000);

    // Agregar event listeners para los filtros
    document.getElementById('order-filter').addEventListener('change', filterAndDisplayPedidos);
    document.getElementById('type-filter').addEventListener('change', filterAndDisplayPedidos);
});

function toggleDetails(id) {
    const details = document.getElementById('details-' + id);
    if (details.style.display === 'none' || details.style.display === '') {
        details.style.display = 'block';
    } else {
        details.style.display = 'none';
    }
}

function nextStatus(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'cambiar_estado.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function() {
        if (xhr.status === 200) {
            alert('Estado actualizado');
            location.reload();
        } else {
            alert('Error al actualizar el estado');
        }
    };
    xhr.send('id_pedido=' + id);
}