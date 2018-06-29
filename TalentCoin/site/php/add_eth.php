<?php

ini_set('display_errors', 'On');

include("www/blocks/bd.php");
require_once 'www/blocks/testField.php';

if (isset($_GET['api'])) {
	$api = $_GET['api'];
} else {
	$api = false;
}
if (isset($_POST['id'])) {
	$id = $_POST['id'];
}
if (isset($_POST['ethereum'])) {
	$ethereum = $_POST['ethereum'];
}

/*echo "<pre>";
echo print_r($_POST);
echo "</pre>";*/


$test = new testField();

$united = [];
$agree = isset($agree) && (bool) $agree ? 1 : 0;
$errors = [];


if ($test->forEther($ethereum)) {
	array_push($united, $ethereum);
} else {
	unset($ethereum);
	array_push($errors, array('field' => 'ethereum', 'message' => 'Wrong ETH address'));
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


if ($cont == 1) {

	$datenow = date('YmdH:i:s');
	$sth  = $pdo->prepare("UPDATE records SET etherad = :ethereum WHERE id = :id");
	$result = $sth->execute(array('ethereum' => $ethereum, 'id' => $id));
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
