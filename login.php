<?php

require 'includes/app.php';

$errores = [];

if($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = mysqli_real_escape_string($db, $_POST['email']);
    $password = $_POST['password'];

    if(!$email || !$password) {
        $errores[] = "Todos los campos son obligatorios";
    }

    if(empty($errores)) {

        $query = "SELECT * FROM usuarios WHERE email = '$email'";
        $resultado = mysqli_query($db, $query);

        if($resultado->num_rows) {

            $usuario = mysqli_fetch_assoc($resultado);

            if(password_verify($password, $usuario['password'])) {

                session_start();
                $_SESSION['login'] = true;

                header('Location: /admin/');
            } else {
                $errores[] = "Password incorrecto";
            }

        } else {
            $errores[] = "Usuario no existe";
        }
    }
}

incluirTemplate('header');
?>

<main class="login-container">

    <div class="login-box">

        <h1>Iniciar Sesión</h1>

        <?php foreach($errores as $error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endforeach; ?>

        <form method="POST">

            <label>Email:</label>
            <input type="email" name="email">

            <label>Password:</label>
            <input type="password" name="password">

            <input type="submit" value="Iniciar Sesión" class="btn-login">
        </form>

    </div>

</main>

<?php
incluirTemplate('footer');