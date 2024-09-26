document.addEventListener('DOMContentLoaded', () => {
    async function fetchData() {
        try {
            const response = await fetch('administrador_datos.php'); // Cambia esto a la ruta correcta de tu archivo PHP
            const data = await response.json();

            if (data.error) {
                console.error('Error:', data.error);
                return;
            }

            // Actualizar estadísticas
            document.getElementById('ventas-totales').textContent = `$${parseFloat(data.total_ventas).toFixed(2)}`;
            document.getElementById('ordenes-completadas').textContent = data.ordenes_completadas;
            document.getElementById('pedidos-realizados').textContent = data.pedidos_realizados;
            document.getElementById('platillos-vendidos').textContent = data.platillos_vendidos;

            // Cambios porcentuales
            document.getElementById('ventas-cambio').textContent = `${data.ventas_cambio.toFixed(2)}% respecto ayer`;
            document.getElementById('ordenes-cambio').textContent = `${data.ordenes_cambio.toFixed(2)}% respecto ayer`;
            document.getElementById('pedidos-cambio').textContent = `${data.pedidos_cambio.toFixed(2)}% respecto ayer`;
            document.getElementById('platillos-cambio').textContent = `${data.platillos_cambio.toFixed(2)}% respecto ayer`;

            // Mostrar mesas
            const tablesContainer = document.getElementById('tables-container');
            let tablesHtml = '<table class="table table-striped"><thead><tr><th>ID Mesa</th><th>Asientos</th><th>Estado</th></tr></thead><tbody>';
            data.mesas.forEach(mesa => {
                tablesHtml += `<tr>
                    <td>${mesa.id_mesa}</td>
                    <td>${mesa.asientos}</td>
                    <td>${mesa.estado}</td>
                </tr>`;
            });
            tablesHtml += '</tbody></table>';
            tablesContainer.innerHTML = tablesHtml;

            // Mostrar gráfico de ventas diarias
            const ctx = document.getElementById('salesChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.dias,
                    datasets: [{
                        label: 'Ventas Diarias',
                        data: data.ventasDiarias,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Fecha'
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Ventas ($)'
                            }
                        }
                    }
                }
            });

        } catch (error) {
            console.error('Error:', error);
        }
    }

    fetchData();
});