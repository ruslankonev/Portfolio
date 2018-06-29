<?php

//ini_set('display_errors', 'On');

include("www/blocks/bd.php");
require_once 'www/blocks/testField.php';

if (isset($_GET['api'])) {
	$api = $_GET['api'];
} else {
	$api = false;
}

if (isset($_POST['email'])) {
	$email = $_POST['email'];
}


if (isset($_POST['agree'])) {
	$agree = $_POST['agree'];
}






$test = new testField();

$united = [];
$agree = isset($agree) && (bool) $agree ? 1 : 0;
$errors = [];


if ($test->forAgree($agree)) {
	array_push($united, $agree);
} else {
	unset($agree);
	array_push($errors, array('field' => 'agree', 'message' => 'Wrong agree Privacy Policy'));
}

if ($test->forEmail($email)) {
	array_push($united, $email);
} else {
	unset($email);
	array_push($errors, array('field' => 'email', 'message' => 'Wrong E-Mail'));
}






$cont = count($united);




if ($cont == 2) {

	$datenow = date('YmdH:i:s');
    $sth  = $pdo->prepare("INSERT INTO subscribes (email, agree)
    VALUES (:email, :agree) ");
    $result = $sth->execute(array('email' => $email, 'agree' => $agree));
	if ($result) {

	

		
		echo $api ? '{"message": "Success"}' : '<p><h3 style="color:#05ee52; text-align: center;">Success</h3></p>';
	} else {
		array_push($errors, array('field' => '', 'message' => 'DB error'));
		echo $api ? '{"message": "Fail", "errors"' . json_encode($errors) . '}' : '<p><h3 style="color:#f85606; text-align: center;">Something wrong</h3></p>';
	}
} else {
	echo $api ? '{"message": "Fail", "errors":' . json_encode($errors) . '}' : '<p><h3 style="color:#f85606; text-align: center;">Fail</h3></p>';

}


?>
