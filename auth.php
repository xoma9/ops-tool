<?php
$user ="";
function generateSalt()
	{
		$salt = '';
		$saltLength = 16; //длина соли
		for($i=0; $i<$saltLength; $i++) {
			$salt .= chr(mt_rand(33,126)); //символ из ASCII-table
		}
		return $salt;
	}
        
function check_cookie(){
    if (isset($_COOKIE['login'])  && isset($_COOKIE['key'])){
        $login = $_COOKIE['login']; 
        $key = sha1($_COOKIE['key']);
    }
    else{
        $login ="";
        $key = "";
    }
    $query = 'SELECT * FROM users WHERE login="'.$login.'" AND cookie="'.$key.'"';
    $user = simple_query($query);
    if (!empty($user)) {
        $result = $user;
    }
    else{
        $result = false;
    }
    return($result);
}


header('Content-Type: text/html; charset=utf-8');
session_start();
//if (!check_cookie()){
//        if (!empty($_SESSION['auth']) and $_SESSION['auth']) {
//        session_start();
//	session_destroy(); //разрушаем сессию для пользователя
//	//Удаляем куки авторизации путем установления времени их жизни на текущий момент:
//	setcookie('login', '', time()); //удаляем логин
//	setcookie('key', '', time()); //удаляем ключ
//        update_page();
//    }
//}
if (empty($_SESSION['auth']) or $_SESSION['auth'] == false) {
    //Проверяем, не пустые ли нужные нам куки...
    if ( !empty($_COOKIE['login']) and !empty($_COOKIE['key']) ) {
    //Пишем логин и ключ из КУК в переменные (для удобства работы):
    $login = $_COOKIE['login']; 
    $key = sha1($_COOKIE['key']); //ключ из кук (аналог пароля, в базе поле cookie)
    /*
        Формируем и отсылаем SQL запрос:
        ВЫБРАТЬ ИЗ таблицы_users ГДЕ поле_логин = $login.
    */
    $query = 'SELECT * FROM users WHERE login="'.$login.'" AND cookie="'.$key.'"';
    //Ответ базы запишем в переменную $result:
    $user = simple_query($query);
    //Если база данных вернула не пустой ответ - значит пара логин-ключ_к_кукам подошла...
    if (!empty($user)) {
        //Пишем в сессию информацию о том, что мы авторизовались:
        $_SESSION['auth'] = true; 
        /*
            Пишем в сессию логин и id пользователя
            (их мы берем из переменной $user!):
        */
	$_SESSION['id'] = $user['id']; 
	$_SESSION['login'] = $user['login'];
	}
    }               
}


if (isset($_GET['logout'])){
    if (!empty($_SESSION['auth']) and $_SESSION['auth']) {
	session_start();
	session_destroy(); //разрушаем сессию для пользователя
	//Удаляем куки авторизации путем установления времени их жизни на текущий момент:
	setcookie('login', '', time()); //удаляем логин
	setcookie('key', '', time()); //удаляем ключ
        update_page();
    }
}



if (isset($_POST['password']) && empty($_POST['password'])) {
    echo '<script>alert("Введите пароль!");</script>';
}
elseif (isset($_POST['login']) && empty($_POST['login'])) {
    echo '<script>alert("Введите логин!");</script>';
}
else{
    if (isset($_POST['password']) && isset($_POST['login'])){
        $password = sha1($_POST['password']);
        $login = vf($_POST['login']);
        $query = 'select login,password,id from users where login="'.$login.'" and password="'.$password.'"';
        $user = simple_query($query);
    }
    if (!empty($user['login']) && $user['login'] == $login) {
	//Стартуем сессию:
        $login =  $user['login'];
        //Пишем в сессию информацию о том, что мы авторизовались:
	$_SESSION['auth'] = true;

	/*
		Пишем в сессию логин и id пользователя
		(их мы берем из переменной $user!):
	*/
	$_SESSION['id'] = $user['id']; 
	$_SESSION['login'] = $user['login']; 
        $_SESSION['auth'] = true;
        //Сформируем случайную строку для куки (используем функцию generateSalt):
	$key = generateSalt(); //назовем ее $key
        //Пишем куки (имя куки, значение, время жизни - сейчас+месяц)
	setcookie('login', $user['login'], time()+60*60*24*30); //логин
	setcookie('key', $key, time()+60*60*24*30); //случайная строка

	/*
		Пишем эту же куку в базу данных для данного юзера.
		Формируем и отсылаем SQL запрос:
		ОБНОВИТЬ  таблицу_users УСТАНОВИТЬ cookie = $key ГДЕ login=$login.
	*/
	$query = 'UPDATE users SET cookie="'.sha1($key).'" WHERE login="'.$login.'"';
        nr_query($query);
    }
}
if (isset($_SESSION['login'])){
    $login = $_SESSION['login'];
}