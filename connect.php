<?php
	$connect = new mysqli("127.0.0.1:3303", "root", "root", "med_zentr");
	$connect->set_charset("utf8");

	if($connect->connect_error)
		die("Ошибка подключения: ". $connect->connect_error);
?>