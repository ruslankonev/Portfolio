<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
*	This is the model class for table "referrals".
*
*	@property int $id
*	@property int $referrer_id
*	@property int $referral_id
*	@property float $referrer_bonus
*	@property float $referral_bonus
*	@property string $createdate
*/
class Referrals extends ActiveRecord
{
	/**
	*	Рассчёт суммы бонусов по реферальной программе, зная, ID транзакции
	*	
	* 	@property int $transaction_id
	*
	* 	@return object $bonus
	*/
	public function sumReferralBonus($transaction_id)
	{
		if($transaction = \app\models\Transactions::findOne(['id' => (int) $transaction_id]))
		{
            $result = \app\models\Currencies::tokensCount($transaction->investments, $transaction->currency_id);

            //pre("Invet sum: ".$transaction->investments." = "."Tokens: ".$result->tokens);

            /*
				TODO

				Размер процента надо бы вынести в конфиг
            */

            $bonus = new \stdClass();
			$bonus->referrer = $result->tokens / 100 * 3; // 3%
			$bonus->referral = $result->tokens / 100 * 2; // 2%

			//pre("Referrer bonus (3%): ".$bonus->referrer);
			//pre("Referral bonus (2%): ".$bonus->referral);

			return $bonus;
		}

		return false;
	}

	/**
	*	Добавление записи в реферальную статистику
	* 	Зная владельца ссылки, её пользователя и ID транзакции
	*	
	* 	@property int $referrer_id
	* 	@property int $referral_id
	* 	@property int $transaction_id
	*
	* 	@return bool true|false
	*/
	public function addReferalStat($referrer_id, $referral_id, $transaction_id)
	{
		$referrals = new Referrals();

		$referrals->referrer_id = (int) $referrer_id;
		$referrals->referral_id = (int) $referral_id;
		$referrals->transaction_id = (int) $transaction_id;

		// получим сумму бонусов
		$bonus = Referrals::sumReferralBonus($transaction_id);

		$referrals->referrer_bonus = $bonus->referrer;
		$referrals->referral_bonus = $bonus->referral;

		// Дата создания записи
		$referrals->createdate = date("Y-m-d H:i:s");

		// Записываем статистику в БД
        return $referrals->save(false);
	}
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%referrals}}';
    }
}