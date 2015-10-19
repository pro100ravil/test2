<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>test</title>
		<link href="styles.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
function showsubmenu(a) {
	var element = document.getElementById("id_"+a);
	if (element.className!='active_submenu') {
		element.className = 'active_submenu';
	} else {
		element.className = 'submenu';
	}
	
}
function getXmlHttp(){
  var xmlhttp;
  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch (e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch (E) {
      xmlhttp = false;
    }
  }
  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }
  return xmlhttp;
}
//вывод описания
function getDistruction(id, level) {
	var req = getXmlHttp()  
	var cont = document.getElementById('main');
	req.onreadystatechange = function() {  
        // onreadystatechange активируется при получении ответа сервера
		if (req.readyState == 4) { 
            // если запрос закончил выполняться
			if(req.status == 200) { 
                 // если статус 200 (ОК) - выдать ответ пользователю
				cont.innerHTML = req.responseText;
			}
			// тут можно добавить else с обработкой ошибок запроса
		}
	}
       // (3) задать адрес подключения
	req.open('GET', '/show.php?id='+id+'&level='+level, true); 
	req.send(null);  // отослать запрос
}

</script>

</head>
<body>
<header>
	<div id="left_header">
		<h3 class="white-color"><i>главная страница</i></h3>
	</div>
	<div id="right_header">
		<a href="login.php"><input type="button" value="Панель администратора" id="login_button"></a>
	</div>

</header>
<div class="left-menu">
<?php 
include('DBclass.php');
$test = new DBedit();

if ($test->connectDB("localhost", "root", "", "orders")) {
	echo "<ul id=\"menu\" class=\"menu\">\n";
	$test->outputMenu(1, 1, NULL);
	echo "</ul>\n";
} else {
	echo "db connect error";
}
?>
</div>
<div id="center">
	<h2><i>Описание</i></h2><br>
	<div id="main">
	</div>
</div>
