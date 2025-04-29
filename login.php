<?php
     session_start();

     include "connect.php";
     $error_message = "";

     if($_SERVER["REQUEST_METHOD"]=="POST"){
        $email=$_POST['email'];
        $password=$_POST['password'];

        $stmt = $connect->prepare("select id_user, role FROM user WHERE email = ? and password = ?");
        $stmt->bind_param("ss",$email,$password);
        
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows > 0){

            $user = $result->fetch_assoc();
            $_SESSION['role'] = $user['role'];
            if ($_SESSION['role'] === 'Администратор') {
                $_SESSION['user_id'] = $user['id_user'];
                header('Location: admin.php'); 
            } elseif($_SESSION['role'] === 'Пациент') {
                $_SESSION['user_id'] = $user['id_user'];
                header('Location: lk.php'); 
            }
        }
        else{
            $error_message = "Неверная почта или пароль";
        }
     }
?>

<!DOCTYPE html>
<html lang="en">
    <link rel="stylesheet" href="css/style.css">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Авторизация</title>
    
</head>

<body>
    <div class="main">
        <h2>Авторизация</h2>
        <?php
        if (!empty($error_message)) {
        echo "<div style='color: red;'>$error_message</div>";}?>
        <form method="post" action="">

            <label for="email">Почта:</label>
            <input type="text" id="email" name="email" required />

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password"/>

            <button type="submit" id="register" name="register">Войти</button>
        </form>
        <label for="log"><a href="register.php" class="link">Еще нет аккаунта? Зарегистрироваться</a></label>
    </div>
</body>
</html>