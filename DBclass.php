<?php
class DBedit 
{
  protected $link;

  function connectDB($hostname, $username, $password, $dbName) {
      $this->link = mysqli_connect($hostname, $username, $password, $dbName);
      if (mysqli_connect_errno()) {
        return False;
      } else {
        mysqli_query($this->link, "set CHARACTER SET UTF8");
        return True;
      }
    }

    //вывод кнопок редактирования в интерфейсе администратора
    protected function getAdminMenu($level, $id, $name=NULL) {
      return"<input type='image' name='edit' src='images/edit.png' alt='edit' width='20' onClick=\"getEditor('$id', '$level', '$name', 'edit')\">
      <input type='image' name='add' src='images/add.png' alt='add' width='20' onClick=\"getEditor('$id', '$level', '$name', 'add')\">
      <a href=\"edit.php?del=true&level=$level&id=$id\" onClick=\"if(confirm('Точно хочешь удалить?')) return true; else return false;\"><input type='image' name='del' src='images/delete.png' alt='del' width='20'></a>";
    }

    //вывод области редактирования в интерфейсе администратора
    function getEditMenu($str, $level=NULL, $id=NULL, $name=NULL, $type=NULL, $text=NULL) {
      if (is_numeric($level)) $subtable = $level + 1;
      echo ($str == 'Добавление') ? (($name == 'new') ? "Добавление нового элемента в первый уровень
      ": "Добавление потомка для \"$name\" в подуровень = $subtable<br>") : "<p>$str<br>";
      echo "<form id='edit_form' action='edit.php' method='POST'>";
      echo ($str == 'Добавление') ? "<pre>Название: <input type='text' name='name'></pre><br>" : "<pre>Название: <input type='text' name='name' value='".$name."'></pre><br>";
      echo ($str == 'Добавление') ? "<pre>Описание: </pre><textarea cols='65' rows='10' name='text'></textarea>" : "<pre>Описание: </pre><textarea cols='65' rows='10' name='text'>".$text."</textarea>";
      echo "<br><br><input type='submit' value='Сохранить'>";
      echo "<input type='hidden' name='id' value='".$id."'>";
      echo "<input type='hidden' name='level' value='".$level."'>";
      echo "<input type='hidden' name='type' value='".$type."'>";
      echo "</form>";
    }

    //получения параметров (id, name, text) и вызов getEditMenu с этими параметрами
    function getEditorParams($level, $id) {
      $id = intval($id);
      $level = intval($level);
      if ($level!=1) {
        $query = "select id, name, text from `table".$level."` where id = ".$id;
      } else {
        $query = "select id, name, text from `table1` where id = ".$id;
      }
      $result = mysqli_query($this->link, $query);
      if ($result) {
        if($arr = mysqli_fetch_array($result)) {
          $this->getEditMenu('Редактирование', $level, $arr['id'],  $arr['name'], 'edit' , $arr['text']);
          return true;
        } else {
          return false;
        }
      }
    }

    //вывод "дерева"
    function outputMenu($mod=0, $level=1, $id=NULL) {
      $id = intval($id);
      $level = intval($level); 
      if ($level!=1) {
        $query = "select id, name from `table".$level."` where other_id = ".$id;
      } else {
        $query = "select id, name from table1";
      }
      $result = mysqli_query($this->link, $query);
      if ($result) {
        while($arr = mysqli_fetch_array($result)) {
          //проверяем существование связанных записей
          $i = $level+1;
          $sub_query = "select id from `table".$i."` where other_id = ".$arr['id'];
          if ($chek = mysqli_query($this->link, $sub_query))
            $count = mysqli_num_rows($chek);
          //если существуют связанные записи выводим плюсик
          if ($count) {
            if ($_SESSION['is_auth'] == True) {
              echo "\t\t<li>\t<span class=\"menu-item\" onClick=\"getDistruction('".$arr[id]."', '".$level."')\">".$arr['name']."</span><input type=\"button\" name=\"show\" value=\"+\" onClick=\"showsubmenu('".$arr[name].$level.$arr[id]."')\">";
              echo $this->getAdminMenu($level, $arr['id'], $arr['name']);
            } else {
              echo "\t\t<li><span class=\"menu-item\" onClick=\"getDistruction('".$arr[id]."', '".$level."')\">".$arr['name']."</span><input type=\"button\" name=\"show\" value=\"+\" onClick=\"showsubmenu('".$arr[name].$level.$arr[id]."')\">"; 
            }
            echo "</li>\n";
            $mod = 1;
          //иначе без плюсика
          } else {
            if ($_SESSION['is_auth'] == True) {
              echo "\t\t<li>\t<span class=\"menu-item\" onClick=\"getDistruction('".$arr[id]."', '".$level."')\">".$arr['name']."</span>";
              echo $this->getAdminMenu($level, $arr['id'], $arr['name']);
            } else {
              echo "\t\t<li><span class=\"menu-item\" onClick=\"getDistruction('".$arr[id]."', '".$level."')\">".$arr['name']."</span>";
            }
            echo "</li>\n";
            $mod = 0;
          }
          //рекурсия
          if ($mod!=0) {
            echo "\n<ul id=\"id_".$arr['name'].$level.$arr['id']."\" class=\"submenu\">\n";
            if ($admin) 
              $this->outputMenu(1, $level+1, $arr['id'], True);
            else 
              $this->outputMenu(1, $level+1, $arr['id'], False);
            echo "</ul>\n";
          }
        }
      } else {
        return false;
      }
    }

    function getQueryAddNewTable($subtable){
      $level = $subtable-1;
      return $query = "create table `table".$subtable."` (
        id integer not null auto_increment primary key,
        name varchar(50),
        text text,
        other_id integer not null,
        FOREIGN KEY (other_id) REFERENCES table".$level."(id) ON UPDATE CASCADE ON DELETE CASCADE
        )";
    }

    //добавление записи
    function addRecord($level, $id, $name, $text) {
      if ($name == '' or $text == '') {
          echo "Пожалуйста, введите корректные данные";
          return false;
      }

      $level = intval($level);
      if ($id != '') $id = intval($id);
      $name = mysql_escape_string($name);
      $text = mysql_escape_string($text);

      if ($level == 1 and $id == '') {
          $query = "insert into `table1` (id, name, text) values (NULL, '$name', '$text')";
      } else {
          $subtable = $level + 1;
          //проверяем существование таблицы, добавляем новую подчиненную таблицу, если надо
          $query_chek_table = "select * from `table".$subtable."`";
          if (!mysqli_query($this->link, $query_chek_table)) {
              $query_add_table = $this->getQueryAddNewTable($subtable);
              mysqli_query($this->link, $query_add_table);
          }
          $query = "insert into `table".$subtable."` (id, name, text, other_id) values (NULL, '$name', '$text', $id)";
      }
      $result = mysqli_query($this->link, $query);
      if ($result) { 
          return true;
      } else {
          return false;
      }
      
    }

    function delRecord($level, $id) {
      $level = intval($level);
      $id = intval($id);
      $query = "delete from `table".$level."` where id=".$id;
      if (mysqli_query($this->link, $query))
          return true;
      else 
          return false;
    }

    function alterRecord($level, $id, $name, $text) {
      $level = intval($level);
      $id = intval($id);
      $name = mysql_escape_string($name);
      $text = mysql_escape_string($text);
      $query = "update `table".$level."` set name = '$name', text = '$text' where id=".$id;
       if (mysqli_query($this->link, $query))
          return true;
      else 
          return false;
    }

    function showRecord($level, $id) {
      $level = intval($level);
      $id = intval($id);
      $query = "select text from `table".$level."` where id = ".$id;
      $result = mysqli_query($this->link, $query);
      if ($result) {
        if ($arr = mysqli_fetch_array($result)) {
          echo $arr['text'];
        }
        return true;
      } else {
          return false;
        }
    }

  //закрываем соединение при уничтожении объекта
  function __destruct() {
    if (isset($this->link))
      mysqli_close($this->link);
  }

}
?>
