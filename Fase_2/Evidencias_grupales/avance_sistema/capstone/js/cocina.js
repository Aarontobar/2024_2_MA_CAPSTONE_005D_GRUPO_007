document.addEventListener('DOMContentLoaded', function() {
    let lastUpdate = 0; // Variable para rastrear el último tiempo de actualización

    function playSound() {
        const audio = new Audio('../sonidos/level-up-191997.mp3'); // Ruta al archivo de sonido
        audio.play().catch(error => console.error('Error al reproducir el sonido:', error));
    }

    function loadPedidos() {
        fetch('get_pedidos.php')
            .then(response => response.json())
            .then(data => {
                // Ordenar los pedidos por fecha y hora en orden descendente (más reciente primero)
                data.sort((a, b) => {
                    const fechaHoraA = new Date(`${a.fecha} ${a.hora}`);
                    const fechaHoraB = new Date(`${b.fecha} ${b.hora}`);
                    return fechaHoraB - fechaHoraA; // Orden descendente
                });

                const container = document.querySelector('.orders-container');
                const currentTime = Date.now(); // Obtener el tiempo actual
                let newPedidos = false;

                // Comprobar si hay nuevos pedidos comparado con la última actualización
                data.forEach(pedido => {
                    const fechaPedido = new Date(`${pedido.fecha} ${pedido.hora}`);
                    if (fechaPedido.getTime() > lastUpdate) {
                        newPedidos = true;
                    }
                });

                // Si hay nuevos pedidos, reproducir el sonido
                if (newPedidos) {
                    playSound();
                }

                container.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos pedidos

                data.forEach(pedido => {
                    const card = document.createElement('div');
                    card.className = 'order-card';

                    // Calcular el tiempo de espera en minutos
                    const fechaPedido = new Date(`${pedido.fecha} ${pedido.hora}`);
                    const ahora = new Date();
                    const diferenciaMinutos = Math.floor((ahora - fechaPedido) / (1000 * 60));

                    // Asignar clase según el tiempo de espera
                    if (diferenciaMinutos > 35) {
                        card.classList.add('urgent'); // Clase CSS para pedidos atrasados
                    } else {
                        card.classList.add('normal'); // Clase CSS para pedidos normales
                    }

                    // Asignar clase a los botones según el tiempo de espera
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

                lastUpdate = Date.now(); // Actualizar el tiempo de la última actualización
            })
            .catch(error => {
                console.error('Error al cargar los pedidos:', error);
            });
    }

    // Cargar pedidos al iniciar la página
    loadPedidos();

    // Actualizar cada 10 segundos (10000 ms)
    setInterval(loadPedidos, 10000);

    // Agregar event listeners para los filtros
    document.getElementById('order-filter').addEventListener('change', loadPedidos);
    document.getElementById('type-filter').addEventListener('change', loadPedidos);
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