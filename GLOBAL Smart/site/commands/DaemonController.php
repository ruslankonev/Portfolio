<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Etherscan;
use app\models\Contract;
use app\models\Transactions;
use dektrium\user\models\Profile;
use dektrium\user\models\User;

class DaemonController extends Controller
{
    const APP_NAME = "Global Wasp";

    public function actionIndex()
    {
        echo "Cron service is running";
    }

    /**
    *   Проверка статуса транзакций
    *   Изменение баланса пользователя
    *   Начисление токенов при инвестировании в BTC
    *   Отправка писем инвестору об изменениях
    */
    public function actionTransactions()
    {
        $eth = new Etherscan();

        $transaction_ids = Transactions::find()
                                        ->select(['id'])
                                        ->where(['status' => 0])
                                        ->orderby(['createdate' => SORT_ASC])
                                        ->limit(10)
                                        ->asArray()
                                        ->all();

        foreach($transaction_ids as $trnsctn)
        {
            $transaction = Transactions::findOne((int) $trnsctn["id"]);

            switch($transaction->currency_id)
            {
                case 2:
                    // ETH

                    // статус транзакции
                    $answer = $eth->checkTransaction((string) $transaction->txhash);
                    
                    // Ответ получен, проверяем результат
                    if($answer->status == 1)
                    {  
                        // список транзакций в рамках кошелька
                        // тащить конкретную транзакцию нельзя :|
                        $trnss = $eth->getListTransactions((string) $transaction->to);

                        // Ищем текущую транзакцию
                        foreach($trnss->result as $tr)
                        {
                            // Если нашли транзакцию по hash
                            if($tr->hash == $transaction->txhash)
                            {
                                // Обновляем транзакцию в БД
                                // сумма инвестиций ETH
                                $transaction->investments = $eth->convertWeiToETH($tr->value);

                                // Дата обновления
                                $transaction->updatedate = $tr->timeStamp;
                                break;
                            }
                        }

                        // Если транзакция обработана
                        if(isset($answer->result->status))
                        {
                            // Если транзакция с ошибкой
                            // Note: status: 0 = Fail, 1 = Pass.
                            if($answer->result->status == 1)
                            {
                                // Success
                                $transaction->status = 1;
                            }
                            elseif($answer->result->status == 2)
                            {
                                //если с момента создания транзакции прошло 15 минут то Fail
                                if(time() > (int) $transaction->createdate + 900)
                                {
                                    $transaction->status = 2;
                                }
                            }
                        }

                        // если транзакции нет. В случае если транзакции произошли в разных сетях.
                        if($answer->result->status == null)
                        {
                            if(time() > (int) $transaction->createdate + 900)
                            {
                                $transaction->status = 2;
                            }
                        }
                    }

                    break;
            }

            // Если транзакция успешна
            if($transaction->status == 1)
            {
                // Пересчёт токенов и бонусов
                $tokens = \app\models\Currencies::tokensCount($transaction->investments, (int) $transaction->currency_id);
                $transaction->tokens = $tokens->tokens;
                $transaction->bonus = $tokens->bonus;

                // Пользователь транзакции
                $profile = Profile::findOne((int) $transaction->user_id);

                // Текущее кол-во токенов пользователя
                $balance = $profile->balance;

                // Текущее кол-во бонусов пользователя
                $bonus = $profile->bonus;

                // Новое кол-во токенов и бонусов
                $transaction->total_tokens = $balance + $tokens->tokens;
                $transaction->total_bonus = $bonus + $tokens->bonus;

                // Новый баланс пользователя
                $profile->balance = $transaction->total_tokens;
                $profile->bonus = $transaction->total_bonus;
                $profile->notify_alert = 1;

                // Сохраняем профиль
                $profile->save();
            }

            // Обновляем транзакцию в БД
            $transaction->save();

            // Пользователь транзакции
            $user = User::findOne((int) $transaction->user_id);

            //если транзакции выполнились и статус успешно или не успешно
            if($transaction->status != 0)
            {
                $mailer = new \dektrium\user\Mailer();
                $mailer->sendNotify($user, self::APP_NAME, (int) $transaction->status);
            }
        }
    }

    /**
    *   Проверяем транзакции на добавление адреса в WL смарт контракта
    *   Транзакции отпрвляются в сеть с помощь Contract::send()
    *   
    *   Если транзакция успешна, то меняем флаг is_whitelisted в БД user
    *   в противном случае, флаг = 0,
    *   отправляем пользователю письмо о том, что адрес не добавлен с описанием причины
    */
    public function actionIswhitelisted()
    {
        $users_id = User::find()
            ->select(['id'])
            ->where(['is_whitelisted' => 0])
            ->andWhere(['!=', 'eth_address', 'null'])
            ->limit(10)
            ->asArray()
            ->all();

        foreach($users_id as $usr)
        {
            $user = User::findOne((int) $usr["id"]);
            $mailer = new \dektrium\user\Mailer();

            // Проверяем наличие адреса в WL
            if(Contract::isWhitelisted((string) $user->eth_address))
            {
                // Меняем флаг
                $user->is_whitelisted = 1;
                
                //отправляем сообщение что аккаунт добавлен в вайтлист
                $mailer->sendSimpleMessageWithView($user->email, \Yii::t('app', 'Your account added to whitelist'), 'notify_whitelist', self::APP_NAME, 1);
            }
            //если не добавился в течении 15 минут от создания транзакции
            else
            {
                if($user->date_addwhitelist <= time() - 900)
                {
                    $user->is_whitelisted = 0;
                    $user->eth_address = null;
                    $mailer->sendSimpleMessageWithView($user->email, \Yii::t('app', 'Your account is not added to whitelist'), 'notify_whitelist', self::APP_NAME, 2);
                }
            }

            // Обновляем пользователя
            $user->save(false);
        }
    }
}