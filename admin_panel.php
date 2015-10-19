<?php
	session_start();
	if (!$_SESSION["is_auth"]) header ('Location: http://test.test');
?>
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
//вывод области редактирования
function getEditor(id, level, name, type) {
	var http = getXmlHttp()  
	var cont = document.getElementById('editor');
	http.onreadystatechange = function() {  
        // onreadystatechange активируется при получении ответа сервера
		if (http.readyState == 4) { 
            // если запрос закончил выполняться
			if(http.status == 200) { 
                 // если статус 200 (ОК) - выдать ответ пользователю
				cont.innerHTML = http.responseText;
			}
			// тут можно добавить else с обработкой ошибок запроса
		}
	}
       // (3) задать адрес подключения
	http.open('GET', '/editor.php?id='+id+'&level='+level+'&type='+type+'&name='+name, true); 
	http.send(null);  // отослать запрос
}
</script>	

</head>
<body>
<header>
	<div id="left_header">
		<h3 class="white-color"><i>интерфейс администратора</i></h3>
	</div>
	<div id="right_header">
		<a href="login.php?logout=true"><input type="button" value="Выйти" id="exit_button"></a>
	</div>
</header>
	<div class="left-menu">
		<?php 
		include('DBclass.php');
		$test = new DBedit();
		if ($test->connectDB("localhost", "root", "", "orders")) {
			echo "<input type='image' name='add' src='images/add.png' alt='add' width='20' onClick=\"getEditor('', '1', 'new', 'add')\">";
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
		<div id="editor">
		</div>
	</div>
</body>
</html>
