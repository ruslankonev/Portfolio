<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
*	This is the model class for table "referrers_referrals" - владелец_пользователь
*
*	@property int $referrer_id - владелец
*	@property int $referral_id - пользователь
*/
class Referrers extends ActiveRecord
{


    /**
     *  Устанавливет связь для ref ссылки
     *
     * @param string $ref
     * @return bool
     */
    public static function setRefRelation($ref){
            //Проверим, что текущей пользователь не перешёл по своей же ссылке
            if($ref != Yii::$app->user->identity->ref_link)
            {
                //Проверим, что текущий пользователь не привязан к кому нибудь (нет у него владельца)
                $referrer = \app\models\Referrers::getReferrer((int) Yii::$app->user->identity->id);

                if(!$referrer)
                {
                    // Владелец ссылки
                    $referrer = \dektrium\user\models\User::findOne(['ref_link' => $ref]);
                    //pre("Владелец ".$referrer->username);

                    // Привязываем пользователя к владельцу ссылки
                    $referral = new \app\models\Referrers();

                    $referral->referrer_id = (int) $referrer->id;
                    $referral->referral_id = (int) Yii::$app->user->identity->id;

                    return $referral->save();
                    //pre("Пользователь ".Yii::$app->user->identity->username." успешно привязан к ".$referrer->username);
                }
                else
                {
                    return true;
                    //pre("Пользователь ".Yii::$app->user->identity->username." уже привязан к ".$referrer->username);
                }
            }
            else
            {
                return true;
                //pre("Перешёл по свой же ref ссылке");
            }
    }
	/**
	*	Получить пару: ID владельца = ID пользователя
	*	
	* 	@property int $referral_id
	*
	* 	@return object $referrer
	*/
	public function getReferrer($referral_id)
	{
		return Referrers::findOne(['referral_id' => (int) $referral_id]);
	}

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%referrers_referrals}}';
    }
}