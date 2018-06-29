<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

use app\models\Transactions;

class AjaxController extends Controller
{
    /*
        actionIndex вызывается всегда, когда action не указан явно
    */
    public function actionIndex()
    {
        return $this->redirect("/site/index");
    }

    /**
    *   Расчёт кол-во токенов и бонусов
    *   Принятие данных через ajax метод tokensCount()
    *
    *   @return json tokens, bonus, total, token name
    */ 
    public function actionTokenscount()
    {
        if(Yii::$app->request->isAjax)
        {
            $currency_id = (int) Yii::$app->request->get('currency_id');
            $investments = (float) Yii::$app->request->get('investments');

            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = \app\models\Currencies::tokensCount($investments, $currency_id, 4);

            return $response;
        }

        Yii::$app->end();
    } 

    /**
    *   Создаём транзакцию
    *   Принятие данных через ajax метод addTransaction()
    *
    *   TODO
    *
    *   Добавить проверку CSRF
    */

   
    public function actionAddtransaction()
    {
        if(Yii::$app->request->isAjax)
        {
            $transaction = Yii::$app->request->post();
            $result = \app\models\Transactions::addTransaction($transaction);

            if(!$result->error)
            {
                if($transaction["currency_id"] == 1)
                {
                    $user = \dektrium\user\models\User::findIdentity((int) Yii::$app->user->identity->id);
                    $msg = "The payment transaction is added. To complete the operation, please top-up: <br/><br/><strong>".$user->btc_address."</strong>";
                }
                else
                {
                    $msg = "The payment transaction is added to the block. Please wait 10-15 minutes to confirm the transaction.";
                }

                Yii::$app->getSession()->setFlash('sweet-success', Yii::t("app", $msg));
                
                return $this->redirect("/");
            }
            else
            {
                $response = Yii::$app->response;
                $response->format = \yii\web\Response::FORMAT_JSON;
                $response->data = $result;

                return $response;
            }
        }

        Yii::$app->end();
    }

    /**
    *   Экспорт таблицы транзакций в CSV файл
    *   с последующим удалением
    *
    *   TODO
    *
    *   Перенести выборку транзакций в соот. модуль
    *   SELECT должен быть по полям
    *   Добавить фильтр и форматирование вывода
    *
    *   @return file filename.csv
    */
    public function actionCsvexport()
    {
        if(Yii::$app->request->get('action') == "export")
        {
            $tmpdir = Yii::getAlias('@runtime');
            $tmpfile = 'export-'.date('d.m.Y').'.csv';

            $query = (new \yii\db\Query())
                    ->select('`transactions`.`id`,
                                `user`.`username` AS username,
                                `currencies`.`name` AS currency_name,
                                `transactions`.`investments`,
                                `transactions`.`tokens`,
                                `transactions`.`bonus`,
                                `transactions`.`total_tokens`,
                                `transactions`.`total_bonus`,
                                `transactions`.`txhash`,
                                `transactions`.`from`,
                                `transactions`.`to`,
                                `transactions`.`createdate`,
                                `transactions`.`updatedate`,
                                `transactions`.`status`,
                                `transactions`.`label`')
                        ->from('transactions')
                        ->leftJoin('user', '`user`.`id` = `transactions`.`user_id`')
                        ->leftJoin('currencies', '`currencies`.`id` = `transactions`.`currency_id`')
                        ->where(["`transactions`.`user_id`" => Yii::$app->user->identity->id]);

            $exporter = new \yii2tech\csvgrid\CsvGrid([
                'query' => $query,
                'csvFileConfig' => [
                    'cellDelimiter' => ";",
                    'writeBom' => true,
                ],
            ]);

            $exporter->export()->saveAs($tmpdir.$tmpfile);

            if(Yii::$app->response->sendFile($tmpdir.$tmpfile, $tmpfile))
            {
                return unlink($tmpdir.$tmpfile);
            }
        }

        Yii::$app->end();
    }

    /**
    *   Вернёт адрес отправителя
    *
    *   @return string address_from
    */ 
    public function actionGetaddressfrom()
    {
        if(Yii::$app->request->isAjax)
        {
            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = Yii::$app->user->identity->eth_address;

            return $response;
        }

        Yii::$app->end();
    }

    /**
    *   Проверит ETH адрес в WL смарт контракта
    *
    *   @return bool true|false   
    */
    public function actionIswhitelisted()
    {
        if(Yii::$app->request->isAjax)
        {
            $address = (string) Yii::$app->request->get("address");
            $isWhitelisted = (bool) \app\models\Contract::isWhitelisted($address);

            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = $isWhitelisted;
            
            return $response;
        }

        Yii::$app->end();
    }

    /**
    *   Вернёт ETH адрес получателя для MetaMask
    *   и дефолтную сеть ETH
    *
    *   @return object data
    */ 
    public function actionGetaddressto()
    {
        if(Yii::$app->request->isAjax)
        {
            $data = new \stdClass();
            $data->address_to = Yii::$app->params['saleContractAddress'];
            $data->network = Yii::$app->params['ethNetwork'];

            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = $data;

            return $response;
        }

        Yii::$app->end();
    }

    /**
    *   Получит смарт контракт по парамертрам
    *   Сохраняю ответ в сессии
    *
    *   @return object contract
    */ 
    public function actionGetsmartcontract()
    {
        if(Yii::$app->request->isAjax)
        {
            $contract = (Yii::$app->session->has("contract") ? Yii::$app->session->get("contract") : new \stdClass());

            if(!isset($contract->timestamp) || $contract->timestamp <= time())
            {
                $eth = new \app\models\Etherscan();
                $soldTokens = $eth->getTotalSupply(Yii::$app->params["tokenContractAddress"]);

                if($soldTokens->status == 1 && isset($soldTokens->result))
                {
                    $contract->soldTokens = round($soldTokens->result * 0.000001, 2);
                }

                // Контракт сейла
                $sale = Yii::$app->params["saleContractAddress"];
                $saleContract = new \app\models\Contract($sale);

                if($saleContract)
                {
                    $weisRaised = $saleContract->callMethod("weisRaised", null, ["unit" => "milliether", "denary" => 0.001, "round" => 2]);

                    if(isset($weisRaised->result))
                    {
                        $contract->weisRaised = $weisRaised->result;
                    }

                    $isPreSale = $saleContract->callMethod("isPreSale");

                    if(!$isPreSale->error && $isPreSale->result == true)
                    {
                        $contract->totalSupply = (int) 3200000;
                        $contract->presale = true;
                    }
                    else
                    {
                        $isMainSale = $saleContract->callMethod("isMainSale");

                        if(!$isMainSale->error && $isMainSale->result == true)
                        {
                            $contract->totalSupply = (int) 46800000;
                        }
                    }
                }

                // For example
                if(isset($contract->totalSupply))
                {
                    $contract->timestamp = strtotime('+15 minutes ', time());
                    Yii::$app->session->set('contract', $contract);
                }
                else
                {
                    $contract = null;
                }
            }

            $response = Yii::$app->response;
            $response->format = \yii\web\Response::FORMAT_JSON;
            $response->data = $contract;

            return $response;
        }

        Yii::$app->end();
    }

    /* Web3 PHP Debug */
    public function actionWeb3js()
    {
        // Контракт сейла
        $sale = Yii::$app->params["saleContractAddress"];
        $saleContract = new \app\models\Contract($sale);

        // 1 303 260 000

        //$soldTokens = $saleContract->callMethod("soldTokensPreSale",  null, ["unit" => "milliether", "denary" => 0.001, "round" => 2]);
        $soldTokensPreSale = $saleContract->callMethod("soldTokensPreSale");
        pre($soldTokensPreSale);

        $soldTokensSale = $saleContract->callMethod("soldTokensSale");
        pre($soldTokensSale);

        die();

        //Yii::$app->session->destroy();
        //pre($_SESSION);
        unset($_SESSION["contract"]);
        unset($_SESSION[Yii::$app->params["saleContractAddress"]]);
        unset($_SESSION[Yii::$app->params["tokenContractAddress"]]);
        die();

        $sale = Yii::$app->params["saleContractAddress"];

        $contract = new \app\models\Contract();
        $contract->initContract($sale);
    }
}