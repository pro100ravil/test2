<?php 
include('DBclass.php');
$test = new DBedit();

if ($test->connectDB("localhost", "root", "", "orders")) {
	$test->showRecord($_GET['level'], $_GET['id']);
} else {
	echo "db connect error";
}
?>
