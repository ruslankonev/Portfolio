<?php

class testField
{

	function forName($props)
	{
		return true;

		/*$len = strlen($props);

		if ($len <= 20) {

			if (preg_match('/^[a-zA-ZабвгдеёжзиклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ\d]+$/', $props)) {
				return True;
			} else {
				echo "<p><h3 style='text-align: center;'>Wrong name</h3></p>";
				return False;
			}

		} else {
			return False;
		}*/
	}

	function forText($props)
	{

		$len = strlen($props);

		if ($len <= 20) {

			if (preg_match('/^[a-zA-ZабвгдеёжзиклмнопрстуфхцчшщъыьэюяАБВГДЕЁЖЗИКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ\d]+$/', $props)) {
				return True;
			} else {
				//echo "<p><h3 style='text-align: center;'>Wrong name</h3></p>";
				return False;
			}

		} else {
			return False;
		}
	}


	function forAgree($props)
	{
		if ($props === 1) {
			return True;
		} else {
			//echo "<p><h3 style='text-align: center;'>Wrong agree</h3></p>";
			return False;
		}
	}


	function forEmail($props)
	{

		if (filter_var($props, FILTER_VALIDATE_EMAIL)) {
			return True;
		} else {
			//echo "<p><h3 style='text-align: center;'>Wrong email</h3></p>";
			return False;
		}
	}

	function forToken($props)
	{

		$new_value = round($props, 2);

		if (is_float($new_value)) {
			return $new_value;
		} else {
			//echo "<p><h3 style='text-align: center;'>Wrong token</h3></p>";
			return False;
		}
	}

	function forEther($props)
	{

		if (preg_match('/^(0x)?[0-9a-f]{40,44}$/i', $props)) {
			return True;
		} else {
			//echo "<p><h3 style='text-align: center;'>Wrong ether</h3></p>";
			return False;
		}
	}


}

?>
