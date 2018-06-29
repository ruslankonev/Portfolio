<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
*	This is the model class for table "wallets"
*
*	@property int $id
*	@property int $user_id
*	@property int $currency_id
*	@property varchar $guid
*	@property varchar $address
*	@property varchar $password
*	@property varchar $label
*	@property bool $enabled
*/
class Wallets extends ActiveRecord
{
	private const LENGTH = 15; // Длинна пароля
	private const SALT = "dc001349e15463ec67ed406c9ff854de"; // Соль
    private const SECRET = "e6cb39085357dbf1491df46bdf8120b1"; // Секретное слово

    /**
    *   Генератор пароля
    *
    *   @param int $length
    *
    *   @return obj $result
    */
    public static function generatePassword($length = null)
    {
    	if(!isset($length))
    	{
    		$length = self::LENGTH;
    	}

    	$password = "";
    	$chars = "abcdefghijklmnoprstuvxyzABCDEFGHIJKLMNOPRSTUVXYZ1234567890.()[]!?&^%@*$<>/|+-{}`~";

    	while($length--)
    	{
    		$password .= $chars[rand(0, strlen($chars) -1)];
    	}

    	$result = new \stdClass();
    	$result->password = $password;
    	$result->hash = base64_encode(self::makeHash($password));
        
        return $result;
    }

    /**
    *   Генератор хеша пароля кошелька
    *
    *   @param string $str
    *
    *   @return string $hash
    */
    private function makeHash($str)
    {
        $gamma = "";
        $l = strlen($str);
        $n = $l > 100 ? 8 : 2;

        while(strlen($gamma) < $l)
        {
            $gamma .= substr(pack("H*", sha1(self::SECRET.$gamma.self::SALT)), 0, $n);
        }

        return $str ^ $gamma;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wallets}}';
    }
}