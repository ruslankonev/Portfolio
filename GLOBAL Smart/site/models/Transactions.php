<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
*   This is the model class for table "transactions".
*
*   @property integer $id
*   @property integer $user_id
*   @property integer $currency_id
*   @property float $investments
*   @property float $tokens
*   @property float $bonus
*   @property float $total_tokens (balance)
*   @property float $total_bonus
*   @property string $txhash
*   @property string $from
*   @property string $to
*   @property string $createdate
*   @property string $updatedate
*   @property integer $status
*   @property string $label
*/

class Transactions extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'currency_id', 'investments', 'tokens', 'total_tokens', 'total_bonus', 'from', 'to', 'label'], 'required'],
            [['user_id', 'currency_id', 'status'], 'integer'],
            [['investments','tokens', 'bonus', 'total_tokens', 'total_bonus'], 'number'],
            [['createdate', 'updatedate'], 'safe'],
            [['txhash', 'from', 'to', 'label'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User'),
            'currency_id' => Yii::t('app', 'Currency'),
            'investments' => Yii::t('app', 'Investments sum'),
            'tokens' => Yii::t('app', 'Tokens sum'),
            'bonus' => Yii::t('app', 'Bonus tokens'),
            'total_tokens' => Yii::t('app', 'Total user balance'),
            'total_bonus' => Yii::t('app', 'Total user bonus'),
            'txhash' => Yii::t('app', 'Txhash'),
            'from' => Yii::t('app', 'From'),
            'to' => Yii::t('app', 'To'),
            'createdate' => Yii::t('app', 'Createdate'),
            'updatedate' => Yii::t('app', 'Updatedate'),
            'status' => Yii::t('app', 'Status'),
            'label' => Yii::t('app', 'Label')
        ];
    }

    /*
        Вернёт текстовое представление статуса транзакции
    */
    public static function getStatusView($id_status)
    {
        switch ($id_status)
        {
            case 0:
                return '<span class="label label-warning">'.Yii::t("app", "In processing").'</span>';
                break;
            case 1:
                return '<span class="label label-success">'.Yii::t("app", "Success").'</span>';
                break;
            case 2:
                return '<span class="label label-danger">'.Yii::t("app", "Error").'</span>';
                break;
        }
    }

    /**
    *    Вернёт транзакции по фильтрку
    *
    *   TODO
    *   Надо дописать ф-цию, чтобы она доставала полную инфу со всеми JOIN запросами
    *   к пользователям и валюте
    *
    *   @param array $filter
    *   
    *   @param array $filter['user_id']
    *   @param array $filter['order']
    *   @param array $filter['group']
    *   @param array $filter['limit']
    *
    *   @return object $query;
    */
    public function getTransactions($filter = null)
    {
        $query = Transactions::find()
                            ->select(['transactions.*'])
                            ->where('1')
                            ->orderBy('transactions.id DESC');

        if(isset($filter["user_id"]))
        {
            //$query->select(['transactions.*', 'user.username']);
            $query->leftJoin('user', 'user.id = transactions.user_id');
            $query->where(['transactions.user_id' => (int) $filter["user_id"]]);
        }

        if(isset($filter["order"]))
        {
            $query->orderBy((string) $filter["order"]);
        }

        if(isset($filter["group"]))
        {
            $query->groupBy((string) $filter["group"]);
        }

        if(isset($filter["limit"]))
        {
            $query->limit((int) $filter["limit"]);
        }

        return $query;
    }

    /**
    *   Добавит новую транзакцию в БД
    *
    *   @param array $data
    *
    *   @return string $status
    */
    public static function addTransaction($data)
    {
        $response = new \stdClass();

        if(!$data)
        {
            $response->error = true;
            $response->msg = "Data is empty";
            return $response;
        }

        $transaction = new Transactions();
        $transaction->user_id = (int) Yii::$app->user->identity->id;
        $transaction->currency_id = (int) $data["currency_id"];
        $transaction->investments = $data["investments"];

        // Пересчёт токенов и бонусов
        $tokens = \app\models\Currencies::tokensCount($transaction->investments, $transaction->currency_id);

        $transaction->tokens = $tokens->tokens;
        $transaction->bonus = $tokens->bonus;

        // Текущее кол-во токенов пользователя
        $balance = Yii::$app->user->identity->profile->balance;

        // Текущее кол-во бонусов пользователя
        $bonus = Yii::$app->user->identity->profile->bonus;

        // Новое кол-во токенов и бонусов
        $transaction->total_tokens = $balance + $tokens->tokens;
        $transaction->total_bonus = $bonus + $tokens->bonus;

        if(!isset($data["txhash"]))
        {
            // Инвестирует в BTC
            if(!Yii::$app->user->identity->btc_address)
            {
                // Создаём кошелёк
                require_once(dirname(__DIR__).'/vendor/Blockchain/vendor/autoload.php');

                $walletServer = (string) Yii::$app->params["btcWalletServer"];
                $apikey = (string) Yii::$app->params['blockchainAPIkey'];

                $Blockchain = new \Blockchain\Blockchain($walletServer, $apikey);
                $Blockchain->setServiceUrl($walletServer);

                if(!$Blockchain->service_url)
                {
                    // Сервер не запущен
                    $response->error = true;
                    $response->msg = "Blockchain Wallet Server not running";
                    return $response;
                }

                $wallet = new \app\models\Wallets();

                $wallet->user_id = Yii::$app->user->identity->id;
                $wallet->currency_id = $transaction->currency_id;

                // Пароль для кошелька
                $pass = $wallet->generatePassword();
                $wallet->password = $pass->password;
                $email = Yii::$app->params["btcWalletEmail"];

                // Создаём кошелёк
                $new_wallet = $Blockchain->Create->create($wallet->password, $email, $label = null);

                if(!$new_wallet->address)
                {
                    $response->error = true;
                    $response->msg = "Blockchain Wallet not created";
                    return $response;
                }

                // Сохраняем кошелёк
                $wallet->guid = $new_wallet->guid;
                $wallet->address = $new_wallet->address;
                $wallet->save();

                // Обновляем пользователя
                $user = \dektrium\user\models\User::findIdentity((int) Yii::$app->user->identity->id);
                $user->btc_address = $new_wallet->address;
                $user->btc_password = $pass->hash;
                $user->save();

                $transaction->from = $wallet->address;
            }
            else
            {
                $transaction->from = Yii::$app->user->identity->btc_address;
            }

            $transaction->to = Yii::$app->params['btcAddressToInvest'];
        }
        else
        {
            // Инвестирует в ETH
            $transaction->txhash = $data["txhash"];
            $transaction->from = Yii::$app->user->identity->eth_address;
            $transaction->to = Yii::$app->params['saleContractAddress'];
        }

        $transaction->createdate = time();
        $transaction->updatedate = time();
        $transaction->label = uniqid();

        // Записываем транзакцию в БД
        if($transaction->save())
        {
            // Смотрим, является ли текущий пользователь - пользователем реферальной программы. Т.е есть ли владелец (Реферер)
            $referrer = \app\models\Referrers::getReferrer((int) Yii::$app->user->identity->id);

            if($referrer)
            {
                // Если является - добавленим запись о сумме бонусов по реферальной программе
                $referral_id = Yii::$app->user->identity->id; // пользователь
                $transaction_id = Yii::$app->db->getLastInsertID(); // транзакция

                \app\models\Referrals::addReferalStat($referrer->referrer_id, $referral_id, $transaction_id);
            }

            $response->error = false;
            $response->msg = "Transaction success add";

            self::buyUniSender();
            return $response;
        }
    }



    /**
    *   Интеграция UniSender
    *
    */
    private function buyUniSender()
    {
        $user = \dektrium\user\models\User::findIdentity((int) Yii::$app->user->identity->id);
        $profile = \dektrium\user\models\Profile::findOne((int) $user->id);
        
        $api_key = "6undm5omcst758p3fh3ggcg37xw8tfmy4b6nixse"; //API-ключ к вашему кабинету

        // Данные о новом подписчике
        $user_email = $user->email; 
        $user_name = (string) $profile->name;
        $user_lists = "14303369";
        $user_tag = urlencode("Added in buy");

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
        curl_setopt($ch, CURLOPT_URL, 'https://api.unisender.com/ru/api/subscribe?format=json');
        $result = curl_exec($ch);

        return true;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%transactions}}';
    }
}