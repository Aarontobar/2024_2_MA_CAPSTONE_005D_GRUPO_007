document.getElementById('applyFilters').addEventListener('click', loadData);

// Variables para almacenar las instancias de los gráficos
let charts = {};

async function loadData() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    let url = '../../php/administrador/data.php';
    if (startDate || endDate) {
        url += `?startDate=${startDate}&endDate=${endDate}`;
    }

    try {
        const response = await fetch(url);
        const data = await response.json();

        if (data.sales_by_time) {
            renderChart('salesByTimeChart', 'bar', data.sales_by_time.labels, data.sales_by_time.data, 'Ventas por Tiempo', [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ]);
        }

        if (data.earnings_by_dish) {
            renderChart('earningsByDishChart', 'pie', data.earnings_by_dish.labels, data.earnings_by_dish.data, 'Ganancias por Platillo', [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ]);
        }

        if (data.most_popular_dish) {
            document.getElementById('mostPopularDishText').innerText = `Platillo más vendido: ${data.most_popular_dish.nombre_platillo} con ${data.most_popular_dish.count} unidades vendidas.`;
        }

        if (data.order_type) {
            renderChart('orderTypeChart', 'doughnut', data.order_type.labels, data.order_type.data, 'Tipo de Pedido Más Usado', [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ]);
        }

        if (data.daily_sales) {
            renderChart('dailySalesChart', 'line', data.daily_sales.labels, data.daily_sales.data, 'Ventas Diarias', [
                'rgba(75, 192, 192, 0.2)'
            ]);
        }

        if (data.monthly_revenue) {
            renderChart('monthlyRevenueChart', 'bar', data.monthly_revenue.labels, data.monthly_revenue.data, 'Ingresos Mensuales', [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ]);
        }

    } catch (error) {
        console.error('Error al cargar los datos:', error);
    }
}

function renderChart(id, type, labels, data, title, colors) {
    const ctx = document.getElementById(id).getContext('2d');

    // Destruye el gráfico anterior si ya existe
    if (charts[id]) {
        charts[id].destroy();
    }

    // Crea un nuevo gráfico
    charts[id] = new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                label: title,
                data: data,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Llamada inicial para cargar los datos
loadData();