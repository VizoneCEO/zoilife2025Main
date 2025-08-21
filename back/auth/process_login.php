<?php
session_start();
include '../../back/db/connection.php'; // Archivo de conexión a la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Capturar datos del formulario
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Verificar que los campos no estén vacíos
    if (empty($email) || empty($password)) {
        header('Location: ../../front/auth/login.php?error=Por favor, completa todos los campos.');
        exit();
    }

    try {
        // Preparar la consulta para verificar el usuario con PDO
        $query = "SELECT id_usuario, nombre, email, password, rol FROM usuarios WHERE email = :email";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Obtener el usuario
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Verificar la contraseña
            if (password_verify($password, $user['password'])) {
                // Guardar datos en la sesión
                $_SESSION['user_id'] = $user['id_usuario'];
                $_SESSION['user_name'] = $user['nombre'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['rol'];

                // Redirigir según el rol
                switch ($user['rol']) {
                    case 'admin':
                        header('Location: ../../front/admin/dashboard.php');
                        break;
                    case 'produccion':
                        header('Location: ../../front/produccion/dashboard.php');
                        break;
                    case 'almacen':
                        header('Location: ../../front/almacen/dashboard.php');
                        break;
                    case 'ventas':
                        header('Location: ../../front/ventas/dashboard.php');
                        break;
                    case 'manager':
                        header('Location: ../../front/manager/dashboard.php');
                        break;
                    case 'deliver':
                        header('Location: ../../front/deliver/dashboard.php');
                        break;
                    default:
                        $_SESSION['error'] = "Rol no válido.";
                        header('Location: ../../front/auth/login.php');
                        break;
                }
                exit();
            } else {
                // Contraseña incorrecta
                header('Location: ../../front/auth/login.php?error=Contraseña incorrecta.');
                exit();
            }
        } else {
            // Correo no registrado
            header('Location: ../../front/auth/login.php?error=El correo electrónico no está registrado.');
            exit();
        }
    } catch (PDOException $e) {
        // Capturar errores de la base de datos
        error_log("Error en el login: " . $e->getMessage());
        $_SESSION['error'] = "Error en el servidor. Inténtelo más tarde.";
        header('Location: ../../front/auth/login.php');
        exit();
    }
} else {
    // Método de solicitud no permitido
    $_SESSION['error'] = "Método no permitido.";
    header('Location: ../../front/auth/login.php');
    exit();
}
?>
