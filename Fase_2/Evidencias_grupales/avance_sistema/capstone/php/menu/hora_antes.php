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
        <div class="icon">😔</div>
        <a href="../../index.php" class="button">Volver al inicio</a>
    </div>
</body>
</html>