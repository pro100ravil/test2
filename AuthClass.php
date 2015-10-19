<?php

class AuthUser {
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

  //авторизация
  function auth($uname, $upass) {
    $uname = mysql_escape_string($uname);
    $upass = mysql_escape_string($upass);
    $upass = md5($upass);
    $sqlQuery = "SELECT * FROM users WHERE uname='$uname' and upass='$upass'";
    $result = mysqli_query($this->link, $sqlQuery); 
    if ($user = mysqli_fetch_array($result)) {
      $_SESSION["is_auth"] = True;
      $_SESSION["login"] = $uname;
      return True;
    } else {
      $_SESSION["is_auth"] = False;
      return False;
    }
  }

  //выход
  public function out() {
    $_SESSION = array();
    session_destroy();
  }

  //проверка авторизован или нет
  public function isAuth() {
    if ($_SESSION["is_auth"] == True)
      return True;
    else 
      return False;
  }

  function printForm() {
    echo "<h2>Пожалуйста, авторизуйтесь</h2>\n";
    echo "<form action='login.php' method='POST'>\n";
    echo "<pre>Login: <input type='text' name='user_name'></pre>\n";
    echo "<pre>Pass:  <input type='password' name='user_pass'></pre><br>\n";
    echo "<input type='submit' value='Отправить'>\n";
    echo "</form>\n";
  }

  function printLogout() {
    echo "<a href='http://test.test/login.php?logout=true'>Exit</a>";
  }
  //закрываем соединение при уничтожении объекта
  function __destruct() {
    if (isset($this->link))
      mysqli_close($this->link);
  }

}

?>
