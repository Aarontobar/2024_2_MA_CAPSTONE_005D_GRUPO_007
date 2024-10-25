document.addEventListener('DOMContentLoaded', function() {
    const starContainer = document.getElementById('stars');
    const calificacionInput = document.getElementById('calificacion');
    const maxStars = 5;
    let selectedRating = 0; // Variable para mantener el valor seleccionado

    // Crear las 5 estrellas
    for (let i = 1; i <= maxStars; i++) {
        let star = document.createElement('span');
        star.classList.add('star');
        star.dataset.value = i;
        star.innerHTML = '&#9733;'; // Estrella en Unicode
        starContainer.appendChild(star);

        // Evento para mover el mouse sobre las estrellas
        star.addEventListener('mousemove', function(e) {
            const boundingBox = e.target.getBoundingClientRect();
            const mouseX = e.clientX - boundingBox.left;
            const starWidth = boundingBox.width;

            // Determinar si marcar media estrella o estrella completa
            if (mouseX < starWidth / 2) {
                highlightStars(i - 0.5); // Media estrella
                calificacionInput.value = (i - 0.5).toFixed(1);
            } else {
                highlightStars(i); // Estrella completa
                calificacionInput.value = i.toFixed(1);
            }
        });

        // Evento para hacer clic y fijar la calificación
        star.addEventListener('click', function() {
            selectedRating = parseFloat(calificacionInput.value); // Fijar la calificación
            highlightStars(selectedRating); // Resaltar estrellas según la calificación seleccionada
        });
    }

    // Cuando el mouse sale del contenedor, resalta la calificación fijada
    starContainer.addEventListener('mouseleave', function() {
        highlightStars(selectedRating); // Mantener el valor seleccionado cuando el mouse sale
    });

    // Función para resaltar las estrellas seleccionadas
    function highlightStars(rating) {
        let stars = document.querySelectorAll('.star');
        stars.forEach((star, index) => {
            const starValue = index + 1;
            if (starValue <= rating) {
                star.classList.add('highlighted'); // Estrella completa dorada
                star.classList.remove('half-highlighted');
            } else if (starValue - 0.5 === parseFloat(rating)) {
                star.classList.add('half-highlighted'); // Media estrella dorada
                star.classList.remove('highlighted');
            } else {
                star.classList.remove('highlighted');
                star.classList.remove('half-highlighted');
            }
        });
    }
});