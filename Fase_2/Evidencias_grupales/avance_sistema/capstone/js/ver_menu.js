function addToCart(id) {
    var form = document.getElementById('order-form-' + id);
    var formData = new FormData(form);
    var params = new URLSearchParams(formData).toString();

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "", true); // Enviar la solicitud al archivo PHP actual
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            var cartCount = parseInt(document.querySelector('.cart').innerText.match(/\d+/)[0]);
            cartCount++;
            document.querySelector('.cart').innerText = 'Carrito (' + cartCount + ')';

            // Mostrar el botón de eliminar
            var deleteButton = document.getElementById('delete-button-' + id);
            if (deleteButton) {
                deleteButton.style.display = 'inline-block';
            }
        }
    };

    xhr.send(params);
}

function removeFromCart(id) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "remove.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    var params = "id=" + encodeURIComponent(id);

    xhr.onload = function() {
        if (xhr.status === 200) {
            // Después de eliminar el producto, verificar si aún queda algún producto con ese id
            var xhrCheck = new XMLHttpRequest();
            xhrCheck.open("POST", "check.php", true);
            xhrCheck.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhrCheck.onload = function() {
                if (xhrCheck.status === 200) {
                    var count = parseInt(xhrCheck.responseText.trim());
                    var deleteButton = document.getElementById('delete-button-' + id);
                    if (deleteButton) {
                        // Si ya no hay más productos, ocultar el botón
                        if (count === 0) {
                            deleteButton.style.display = 'none';
                        }
                    }

                    // Actualizar el conteo del carrito
                    var cartCount = parseInt(document.querySelector('.cart').innerText.match(/\d+/)[0]);
                    cartCount--;
                    document.querySelector('.cart').innerText = 'Carrito (' + cartCount + ')';
                }
            };

            xhrCheck.send(params);
        }
    };

    xhr.send(params);
}