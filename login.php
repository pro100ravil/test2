<?php

session_start();
header("Content-Type: text/html; charset=utf-8");

include('AuthClass.php');
$test = new AuthUser();
if (isset($_POST['user_name']) and isset($_POST['user_pass'])) {
	$uname = $_POST['user_name'];
	$upass = $_POST['user_pass'];
	if ($test->connectDB("localhost", "root", "", "orders")) {
		if ($test->auth($uname, $upass)) {
			header('Location: /admin_panel.php');
		} else {
			echo "Неверный логин и/или пароль. Попробуйте еще раз";
				}
	} else {
		echo "Не удалось подключиться";
		}
} elseif ($_GET['logout'] == true) {
	$test->out();
	header('Location: http://test.test');
} else {
	if ($test->isAuth()) {
		header('Location: /admin_panel.php');
	} else {
		$test->printForm();
	}
}


?>
