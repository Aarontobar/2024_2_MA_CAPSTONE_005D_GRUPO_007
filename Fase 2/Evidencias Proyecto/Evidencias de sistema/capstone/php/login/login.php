<!-- login.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - Restaurante Elegante</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: url('../../imagenes/inicio.jpg') no-repeat center center fixed;
            background-size: cover;
            backdrop-filter: blur(8px);
            font-family: Arial, sans-serif;
        }
        .login-container {
            display: flex;
            width: 700px;
            height: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
        }
        .login-image {
            flex: 1;
            background: url('../../imagenes/nosotros2.jpg') no-repeat center center;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-image img {
            width: 100px;
            height: 100px;
        }
        .login-form {
            flex: 1;
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-form h2 {
            margin: 0 0 20px;
            font-size: 24px;
            color: #333;
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group input {
            width: 80%;
            padding: 10px 40px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .input-group .fa {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            color: #999;
        }
        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
            font-size: 14px;
            color: #999;
        }
        .login-button {
            background: #8bc34a;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-image">
            <img src="../../imagenes/logo.png" alt="Logo with leaves and circular design">
        </div>
        <div class="login-form">
            <h2>Bienvenido!</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="error-message"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <form action="procesar_login.php" method="POST">
                <div class="input-group">
                    <i class="fa fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="@username" required>
                </div>
                <div class="input-group">
                    <i class="fa fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="forgot-password">
                    <a href="#">¿Olvidaste tu contraseña?</a>
                </div>
                <button type="submit" class="login-button">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</body>
</html>