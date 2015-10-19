<?php
session_start();
if ($_SESSION['is_auth'] == False) exit();

header("Content-Type: text/html; charset=utf-8");

include('DBclass.php');
$test = new DBedit();

if ($test->connectDB("localhost", "root", "", "orders")) {
	$level = $_GET['level'];
	$id = $_GET['id'];
	$name = $_GET['name'];
	if ($_GET['type'] == 'edit') $test->getEditorParams($level, $id);
	if ($_GET['type'] == 'add') $test->getEditMenu('Добавление', $level, $id, $name, 'add');
} else {
	echo "db connect error";
}
?>
