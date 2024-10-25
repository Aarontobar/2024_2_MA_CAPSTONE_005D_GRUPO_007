function searchReservations() {
    let name = $('#searchReservationName').val();
    
    $.ajax({
        url: '../../php/asignar_mesa/search.php',
        type: 'GET',
        data: { action: 'searchReservations', name: name },
        success: function(response) {
            $('#reservationTableBody').html(response); // Actualiza el contenido de la tabla
        },
        error: function() {
            alert('Error al realizar la búsqueda.');
        }
    });
}

function searchTables() {
    let seats = $('#seatCount').val();
    
    $.ajax({
        url: '../../php/asignar_mesa/search.php',
        type: 'GET',
        data: { action: 'searchTables', seats: seats },
        success: function(response) {
            $('#mesaTableBody').html(response); // Actualiza el contenido de la tabla
        },
        error: function() {
            alert('Error al realizar la búsqueda.');
        }
    });
}
