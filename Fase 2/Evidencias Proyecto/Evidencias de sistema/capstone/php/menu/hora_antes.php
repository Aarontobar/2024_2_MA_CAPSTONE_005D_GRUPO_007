<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aún no abrimos</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .message {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .icon {
            font-size: 50px;
            margin-bottom: 20px;
            color: transparent;
            -webkit-text-stroke: 1px #ffffff;
        }

        .button {
            padding: 10px 20px;
            background-color: #ffffff;
            color: #121212;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
        }

        .button:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="message">Aún no abrimos, vuelva más tarde</div>
        <div class="icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="none" stroke="#ffffff" stroke-width="2" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="6" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12" y2="16"></line>
            </svg>
        </div>
        <a href="../../index.php" class="button">Volver al inicio</a>
    </div>
    <div id="countdown" style="position: absolute; top: 10px; left: 10px; font-size: 14px;">Redirigiendo al inicio en 10 segundos...</div>
    <script>
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        const interval = setInterval(() => {
            countdown--;
            countdownElement.textContent = `Redirigiendo al inicio en ${countdown} segundos...`;
            if (countdown === 0) {
                clearInterval(interval);
                window.location.href = '../../index.php';
            }
        }, 1000);
    </script>
</body>
</html>