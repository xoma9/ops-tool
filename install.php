<?php
header('Content-Type: text/html; charset=utf-8');
?>
<html> 
<head> 
<title>Install ops-tool</title> 
</head> 
<body> 
<form action="" method="post"> 
<h1>Установка : Шаг 1</h1> 
Укажите данные для подключения к бд
<fieldset> 
<legend>MySQL</legend> 
<table>
    <tr>
        <td>
            <label for='host'>Сервер</label>
        </td>
        <td>
            <input type='text' name='host' value='localhost:3306' />
        </td>
    </tr>
    <tr>
        <td>
            <label for='db_user'>Пользователь БД</label>
        </td>
        <td>
            <input type='text' name='db_user' value='root' />
        </td>
    </tr>
    <tr>
        <td>
            <label for='password'>Пароль</label>
        </td>
        <td>
            <input type='text' name='password' />
        </td>
    </tr>
    <tr>
        <td>    
            
            <label for='database'>Имя базы данных</label>
        </td>
        <td>
            <input type='text' name='database' value='' />
        </td>
    </tr>
</table>
</fieldset> 

<input type="hidden" name="step" value="2" /><br /> 
<input type="submit" name="action" value="Далее >>" /><br /> 
</form> 
</body> 
</html> 

<?php
print_r($_SESSION);
// Establish Database Connection - the '2' is not a typo 
if($_REQUEST['step'] == 2) { 
    //Save the data to the Session 
    if(isset($_REQUEST['host'])) $_SESSION['host'] = $_REQUEST['host']; 
    if(isset($_REQUEST['db_user'])) $_SESSION['db_user'] = $_REQUEST['db_user']; 
    if(isset($_REQUEST['password'])) $_SESSION['password'] = $_REQUEST['password']; 
    if(isset($_REQUEST['database'])) $_SESSION['database'] = $_REQUEST['database']; 
    if(isset($_REQUEST['url'])) $_SESSION['url'] = $_REQUEST['url']; 

    if(mysql_connect($_SESSION['host'],$_SESSION['db_user'],$_SESSION['password'])) { //Try to connect to the DB. 
        $conresult['success'][] = 'Успешное подключение к серверу'; 

        if(mysql_select_db($_SESSION['database'])) {//Select the provided database. 
            $conresult['success'][] = "База данных '$_SESSION[database]' выбрана"; 
            create_all_tables();
            create_config();
        } else { 
            $conresult['error'][] = 'База данных('.$_SESSION['database'].') не существует. Укажите корректное имя базы данных.'; 
            $_REQUEST['step'] = 1; 
        } 
    } else { 
        $conresult['error'][] = 'Не удалось подключиться к базе данных. Проверьте учетные данные'; 
        $_REQUEST['step'] = 1; 
    } 
} 

//print_r($QUERY['success']);
$con = mysql_connect($_SESSION['host'],$_SESSION['db_user'],$_SESSION['password']);
    if (!$con) { 
      die('Не удалось подключиться: ' . mysql_error()); 
      }
    elseif(isset($conresult)){
        if (isset($conresult['success'])){
           foreach ($conresult['success'] as $result){
               print($result.'<br>');
           } 
        }
        if(isset($conresult['error'])){
           foreach ($conresult['error'] as $result){
               print($result.'<br>');
           }
       }
    }

function create_all_tables(){
    global $counter;
    $con = mysql_connect($_SESSION['host'],$_SESSION['db_user'],$_SESSION['password'],$_SESSION['database']);
$db = mysql_select_db($_SESSION['database'], $con);
if ($db){
    $counter = 0 ;
    $query = "CREATE TABLE IF NOT EXISTS modules_admins_rights (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, userid INT(11) NOT NULL DEFAULT '0', user_read_right TINYINT(1) NOT NULL DEFAULT '0', user_write_right TINYINT(1) NOT NULL DEFAULT '0')";
    $result = mysql_query($query,$con);
    if(!$result){
        print(mysql_error());
    }
    else{
        $counter++;
    }
    $query = "CREATE TABLE IF NOT EXISTS modules_modules_rights (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, userid INT(11) NOT NULL DEFAULT '0', user_read_right TINYINT(1) NOT NULL DEFAULT '0', user_write_right TINYINT(1) NOT NULL DEFAULT '0')";
    $result = mysql_query($query,$con);
        if(!$result){
        print(mysql_error());
    }
    else{
        $counter++;
    }
    $query = "CREATE TABLE IF NOT EXISTS modules_scripts_rights (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, userid INT(11) NOT NULL DEFAULT '0', user_read_right TINYINT(1) NOT NULL DEFAULT '0', user_write_right TINYINT(1) NOT NULL DEFAULT '0')";
    $result = mysql_query($query,$con);
        if(!$result){
        print(mysql_error());
    }
    else{
        $counter++;
    }
    $query = "CREATE TABLE IF NOT EXISTS modules_index_rights (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, userid INT(11) NOT NULL DEFAULT '0', user_read_right TINYINT(1) NOT NULL DEFAULT '0', user_write_right TINYINT(1) NOT NULL DEFAULT '0')";
    $result = mysql_query($query,$con);
        if(!$result){
        print(mysql_error());
    }
    else{
        $counter++;
    }
    $query = "CREATE TABLE IF NOT EXISTS users (id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, login VARCHAR(255), password VARCHAR(255), cookie VARCHAR(255), superadmin TINYINT(1) NOT NULL DEFAULT '0')";
    $result = mysql_query($query,$con);
        if(!$result){
        print(mysql_error());
    }
    else{
        $counter++;
    }
    $query = "SELECT * FROM users WHERE login='admin'";
    $entry = mysql_query($query,$con);
    $entry = mysql_fetch_array($entry);
    if (empty($entry)){
        $query = "INSERT INTO users (login,password,superadmin) VALUES ('admin','".sha1('admin')."','1')";
        $result = mysql_query($query,$con);
            if(!$result){
        print(mysql_error());
    }
    else{
        $counter++;
    }
    }
}
if ($counter == 6){
    print('База данных успешно проинициализирована<br>');
}
else{
    print('База данных не была корректно проинициализирована!<br>');
}
}
function create_config(){
    global $counter;
    $hostport = explode(':',$_SESSION['host']);
    $port = $hostport[1];
    $host = $hostport[0];
    $user = $_SESSION['db_user'];
    $password = $_SESSION['password'];
    $database = $_SESSION['database'];
    $configcontent=";database host".PHP_EOL
                    . "server = \"".$host."\"".PHP_EOL
                    . ";database port".PHP_EOL
                    . "port = \"".$port."\"".PHP_EOL
                    . ";user login".PHP_EOL
                    . "username = \"".$user."\"".PHP_EOL
                    . ";user password".PHP_EOL
                    . "password = \"".$password."\"".PHP_EOL
                    . ";database name to use".PHP_EOL
                    . "db = \"".$database."\"".PHP_EOL
                    . "character = \"UTF8\"".PHP_EOL
                    . "prefix = \"\"";
    if ($counter == 6){
        if (file_put_contents("./config/mysql.ini", $configcontent)){
            print("Конфигурация успешно создана. Сейчас вы будете перенаправлены.<br> Логин и пароль по-умолчанию: admin/admin <script type=\"text/javascript\">  alert('Конфигурация успешно создана. Сейчас вы будете перенаправлены. Логин и пароль по-умолчанию: admin/admin'); window.location = 'index.php'</script>");
        }
        else{
            print('Не удалось создать файл кофигурации. Проверьте права доступа к папке config<br>');
        }
    }
}