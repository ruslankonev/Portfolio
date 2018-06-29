<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
*   This is the model class for table "currencies".
*
*   @property integer $id
*   @property string $name
*   @property float $currency_price
*   @property float $token_price
*   @property string $code
*   @property string $platform
*   @property intager $cache_control
*   @property integer $position
*   @property integer $enabled
*/

class Currencies extends ActiveRecord
{
    const CACHE_CONTROL = 15; // (minutes) Время жизни данных о стоимости валют в БД 

    /**
    *   Вернёт валюту по ID
    **/
    public static function findCurrency($id)
    {
        return static::findOne($id);
    }

    /**
    *   Рассчитает кол-во токенов и бонусов для определенной валюты
    *   а также общую сумму и название токена
    *
    *   @param int investments - сумма инвестиций
    *   @param int currency_id - ID валюты
    *   @param int round
    *
    *   @return obj result
    **/
    public function tokensCount($investments, $currency_id, $round = null)
    {
        if(!$investments && !$currency_id)
        {
            return false;
        }

        if(!$currency = self::findCurrency((int) $currency_id))
        {
            return false;
        }

        switch($currency->code)
        {
            case 'ETH':
                $token_price = $currency->token_price;

                $sale = \Yii::$app->params["saleContractAddress"];
                $saleContract = new \app\models\Contract($sale);

                $isPreSale = $saleContract->callMethod("isPreSale");

                if(!$isPreSale->error && $isPreSale->result == true)
                {
                    $preSalePrice = $saleContract->callMethod("preSalePrice");

                    if(!$preSalePrice->error && $preSalePrice->result > 0)
                    {
                        $token_price = \app\models\Etherscan::convertWeiToETH($preSalePrice->result);
                    }
                }
                else
                {
                    $isMainSale = $saleContract->callMethod("isMainSale");

                    if(!$isMainSale->error && $isMainSale->result == true)
                    {
                        $MainSalePrice = $saleContract->callMethod("MainSalePrice");

                        if(!$MainSalePrice->error && $MainSalePrice->result > 0)
                        {
                            $token_price = \app\models\Etherscan::convertWeiToETH($MainSalePrice->result);
                        }
                    }
                }

                if(isset($token_price) && $currency->token_price != $token_price)
                {
                    $currency->token_price = $token_price;

                    $dollarPrice = $saleContract->callMethod("dollarPrice");

                    if(isset($dollarPrice->result))
                    {
                        $currency->currency_price = $dollarPrice->result;
                    }

                    // Обновим валюту
                    $currency->cache_control = strtotime('+'.self::CACHE_CONTROL.' minutes ', time());
                    $currency->save();
                }

                $tokens = $investments / $currency->token_price;
                break;
        }

        // Бонусов у клиента - нет
        $bonus = 0;

        $result = new \stdClass();
        $result->tokens = ($round) ? round($tokens, (int) $round) : $tokens;
        $result->bonus = ($round) ? round($bonus, (int) $round) : $bonus;
        $result->total = ($round) ? round($result->tokens + $result->bonus, (int) $round) : $result->tokens + $result->bonus;
        $result->name = Yii::$app->params["tokenName"];

        return $result;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%currencies}}';
    }
}