<?php
session_start();
if ($_SESSION['is_auth'] == False) exit();

header("Content-Type: text/html; charset=utf-8");

include('DBclass.php');

$editor = new DBedit();

if ($editor->connectDB("localhost", "root", "", "orders")) {
	//если "добавление" - добавляем новую запись функцией addRecord
	if (isset($_POST['type']) and $_POST['type'] == 'add') {
		if (isset($_POST['name']) and isset($_POST['text']) and isset($_POST['id']) and isset($_POST['level'])) {
			$name = $_POST['name'];
			$text = $_POST['text'];
			$id = $_POST['id'];
			$level = $_POST['level'];
			if ($editor->addRecord($level, $id, $name, $text))
				echo "Запись успешно добавлена";
			else
				echo "Ошибка, попробуйте еще раз";
		}
	}
	//если "редактирование" - сохраняем изменения функцией alterRecord
	if (isset($_POST['type']) and $_POST['type'] == 'edit') {
		if (isset($_POST['name']) and isset($_POST['text']) and isset($_POST['id']) and isset($_POST['level'])) {
			$name = $_POST['name'];
			$text = $_POST['text'];
			$id = $_POST['id'];
			$level = $_POST['level'];
			if ($editor->alterRecord($level, $id, $name, $text))
				echo "Изменения успешно сохранены";
			else
				echo "Ошибка, попробуйте еще раз";
		}
	}
	//если "удаление" - удаляем функцией delRecord
	if (isset($_GET['del']) and  isset($_GET['id']) and isset($_GET['level']) and $_GET['del'] == True) {
		$id = $_GET['id'];
		$level = $_GET['level'];
		if ($editor->delRecord($level, $id))
			echo "Запись успешно удалена";
		else
			echo "Ошибка удаления, попробуйте еще раз";
	}
} else {
	echo "db connect error";
}

?>
