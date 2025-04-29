<?php
     include "connect.php";
     session_start();
     $error_message = "";

     if($_SERVER["REQUEST_METHOD"]=="POST"){
        $last_name=$_POST['last_name'];
        $name=$_POST['name'];
        $father_name=$_POST['father_name'];
        $date_of_birth=$_POST['date_of_birth'];
        $number=$_POST['number'];
        $polis=$_POST['polis'];
        $email=$_POST['email'];
        $password=$_POST['password'];

        $checkEmail=$connect->prepare("select count(*) from user where email = ?");
        $checkEmail->bind_param("s", $email);
        $checkEmail->execute();
        $checkEmail->bind_result($emailCount);
        $checkEmail->fetch();
        $checkEmail->close();
        if($emailCount > 0){
            $error_message = "Данный email уже существует";
        }
        else{
        $stmt=$connect->prepare("insert into user(last_name,name,father_name,date_of_birth,number,number_polis,email,password) 
        values (?,?,?,?,?,?,?,?)");
        $stmt->bind_param("ssssssss",$last_name,$name,$father_name,$date_of_birth,$number,$polis,$email,$password);
        if($stmt->execute()){
            $_SESSION['user_id'] = $user['id_user'];
            header ("Location: login.php");
        }
        else{
            echo "Ошибка: " . $stmt->error;
        }
    }
     }
?>

<!DOCTYPE html>
<html lang="en">
    <link rel="stylesheet" href="css/style.css">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Регистрация</title>
    
</head>

<body>
    <div class="main">
        <h2>Регистрация</h2>
        <?php
        if (!empty($error_message)) {
        echo "<div style='color: red;'>$error_message</div>";}?>

        <form method="post" action="">
            <label for="last_name">Фамилия</label>
            <input type="text" id="last_name" name="last_name" required />

            <label for="name">Имя</label>
            <input type="text" id="name" name="name" required />

            <label for="father_name">Отчество</label>
            <input type="text" id="father_name" name="father_name" required />

            <label for="date_of_birth">Дата рождения</label>
            <input type="date" id="date_of_birth" name="date_of_birth" required />

            <label for="number">Номер телефона</label>
            <input type="text" id="number" name="number" pattern="[0-9]{11}" required title="Номер телефона должен содержать 11 цифр"/>

            <label for="polis">Номер страхового полиса</label>
            <input type="text" id="polis" name="polis" pattern="[0-9]{16}" required title="Номер страхового полиса должен содержать 16 цифр" />

            <label for="email">Почта:</label>
            <input type="email" id="email" name="email" required title="Формат почты: email@email.com/ru"/>

            <label for="password">Пароль:</label>
            <input type="password" id="password" name="password" pattern=".{6,}" required title="Пароль должен содержать 6 символов"/>

            <button type="submit" id="register" name="register">Зарегистрироваться</button>
        </form>
        <label for="log"><a href="login.php" class="link">Уже зарегистрированы? Войти в личный кабинет</a></label>
        
    </div>
</body>
</html>