<?php

ini_set('display_errors', 'On');

include("www/blocks/bd.php");
require_once 'www/blocks/testField.php';

if (isset($_GET['api'])) {
	$api = $_GET['api'];
} else {
	$api = false;
}
if (isset($_POST['firstname'])) {
	$name = $_POST['firstname'];
}
if (isset($_POST['agree'])) {
	$agree = $_POST['agree'];
}
if (isset($_POST['lastname'])) {
	$lastname = $_POST['lastname'];
}
if (isset($_POST['country'])) {
	$country = $_POST['country'];
}
if (isset($_POST['ptoken'])) {
	$ptoken = $_POST['ptoken'];
}
if (isset($_POST['ga_clientid'])) {
	$gaClientId = $_POST['ga_clientid'];
}
if (isset($_POST['email'])) {
	$email = $_POST['email'];
}

/*echo "<pre>";
echo print_r($_POST);
echo "</pre>";*/


$test = new testField();

$united = [];
$agree = isset($agree) && (bool) $agree ? 1 : 0;
$errors = [];

if ($test->forName($name)) {
	array_push($united, $name);
} else {
	unset($name);
	array_push($errors, array('field' => 'firstname', 'message' => 'Wrong first name'));
}
if ($test->forAgree($agree)) {
	array_push($united, $agree);
} else {
	unset($agree);
	array_push($errors, array('field' => 'agree', 'message' => 'Wrong agree Privacy Policy'));
}
if ($test->forName($lastname)) {
	array_push($united, $lastname);
} else {
	unset($lastname);
	array_push($errors, array('field' => 'lastname', 'message' => 'Wrong last name'));
}
if ($test->forName($country)) {
	array_push($united, $country);
} else {
	unset($country);
	array_push($errors, array('field' => 'country', 'message' => 'Wrong country'));
}
if ($test->forEmail($email)) {
	array_push($united, $email);
} else {
	unset($email);
	array_push($errors, array('field' => 'email', 'message' => 'Wrong E-Mail'));
}


if ($test->forToken($ptoken)) {
	$new_ptoken = $test->forToken($ptoken);
	array_push($united, $new_ptoken);
} else {
	unset($ptoken);
	array_push($errors, array('field' => 'ptoken', 'message' => 'Wrong token count'));
}


/*echo "<pre>";
echo print_r($united);
echo "</pre>";*/

/*echo "<pre>";
echo print_r($_POST);
echo "</pre>";*/


$cont = count($united);


/*if(preg_match('/^[a-zа-яё]{1}[a-zа-яё]*[a-zа-яё\d]{1}$/i', $name)){

	echo $name;
}else{
	echo "Absent";
}*/


if ($cont == 6) {

	$datenow = date('YmdH:i:s');
	$sth  = $pdo->prepare("INSERT INTO records (name, lastname, email, country, token, ga_clientid, agree, date)
    VALUES (:name, :lastname, :email, :country, :new_ptoken, :gaClientId, :agree, :datenow) ");
	$result = $sth->execute(array('name' => $name, 'lastname' => $lastname, 'email' => $email, 'country' => $country, 'new_ptoken' => $new_ptoken, 'gaClientId' => $gaClientId, 'agree' => $agree, 'datenow' => $datenow));
	if ($result) {

		 /*Отправка в Unisender*/

    $api_key="667d4cg4bstqgr3bthbifg7z7gu7tz5u5u4s3pqo"; //API-ключ к вашему кабинету


             // Данные о новом подписчике
                $user_email = $email;
                $user_name = iconv('cp1251', 'utf-8', "John");
                $user_lists = "14417033";
                $user_tag = urlencode("Added using API");

                // Создаём POST-запрос
                $POST = array (
                  'api_key' => $api_key,
                  'list_ids' => $user_lists,
                  'double_optin' => 3,
                  'fields[email]' => $user_email,
                  'fields[Name]' => $user_name,
                  'tags' => $user_tag
                );

                // Устанавливаем соединение
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $POST);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_URL, 
                            'https://api.unisender.com/ru/api/subscribe?format=json');
                $result = curl_exec($ch);

		
		echo $api ? '{"message": "Success", "userId": "'.$pdo->lastInsertId().'"}' : '<p><h3 style="color:#05ee52; text-align: center;">Success</h3></p>';
	} else {
		array_push($errors, array('field' => '', 'message' => 'DB error'));
		echo $api ? '{"message": "Fail", "errors"' . json_encode($errors) . '}' : '<p><h3 style="color:#f85606; text-align: center;">Something wrong</h3></p>';
	}
} else {
	echo $api ? '{"message": "Fail", "errors":' . json_encode($errors) . '}' : '<p><h3 style="color:#f85606; text-align: center;">Fail</h3></p>';

}


?>
