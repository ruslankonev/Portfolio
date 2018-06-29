<?php

namespace app\models;

use yii\db\ActiveRecord;

use Web3\Web3;
use Web3\Providers\HttpProvider;
use Web3\RequestManagers\HttpRequestManager;

use app\models\Etherscan;

class Contract extends ActiveRecord
{
    private $MyContract; // Объект контракта, с которым работаем
    private $web3; // Объект Web3 класса
    private $web3Contract; // Объект контракта с ф-циями контракта для работы с web3

    const TIMEOUT = 5; // Таймаут подключения geth
    const CACHE_CONTROL = 15; // (minutes) Время жизни кэша объекта контрака в сессии и БД
    const UNLOCK_DURATION = 86400; // (sec) Время анлока пользователя Geth

    public function __construct($address = null)
    {
        if($this->MyContract === null)
        {
            $this->MyContract = new \stdClass();
            $this->MyContract->network = \Yii::$app->params["ethNetwork"];

            if(isset($address))
            {
                $this->MyContract->address = (string) $address;
                self::initContract($address);
            }
        }
        else
        {
            return $this->MyContract;
        }
    }

    /**
    *   Инициализация смарт контракта по адресу
    *   Данные, если актуальны - из сессии или из БД
    *   Не актуальны - из API + обновление в сессии и БД
    *
    *   @param string $address
    *   
    *   @set obj $web3Contract
    *   @return obj $MyContract
    */
    public function initContract($address)
    {
        if(!isset($address))
        {
            return "Address is empty";
        }

        $this->MyContract->resume = false;

        // Смотрим в БД
        $contract = self::getContract($address);

        // Если записи о контракте нет - создаём
        if(!$contract)
        {
            $contract = new Contract();
        }

        // Если версия не актуальная - обновляем
        if($contract->cache_control < time())
        {
            $contract->address = $address;
            $contract->abi = self::getContractAbi($address);

            // Адрес backendOperator смарт котракта
            $contract->backend = \Yii::$app->params["backendOperator"];

            // Время жизни контракта в БД
            $contract->cache_control = strtotime("+".self::CACHE_CONTROL." minutes ", time());

            $contract->save();

            $this->MyContract->resume = true;
        }

        // ABI не нужен нам в сессии
        // поэтому добавляем его после сохранения
        $this->MyContract->abi = $contract->abi;
        
        if(self::iniWeb3())
        {
            $this->web3Contract = new \Web3\Contract($this->web3->provider, $this->MyContract->abi);
            return $this->MyContract;
        }
        else
        {
            return print_r("Web3 is undefined");
        }
    }

    /**
    *   Инициализация Web3 на geth
    *
    *   @return obj $web3
    */
    private function iniWeb3()
    {
        $localhost = (string) \Yii::$app->params["ethDefaultServer"];

        // Создаём подключение
        $this->web3 = new Web3(new HttpProvider(new HttpRequestManager($localhost, self::TIMEOUT)));

        if($this->MyContract->resume)
        {
            return $this->web3;
        }

        // Проверяем, есть ли подключение
        $version = "";
        $this->web3->clientVersion(function($err, $res) use (&$version)
        {
            if($err !== null)
            {
                print_r($err->getMessage());
                return false;
            }

            $version = $res;
        });

        if(isset($version))
        {
            return $this->web3;
        }
    }

    /**
    *   Вызов методов контракта
    *
    *   @param string $method - метод/переменная контракта
    *   @param array $params - параметры запуска метода контракта
    *   @param array $format - форматирование результата
    *
    *   Example $format
    *
    *   "unit" => "milliether", // Во что переводить
    *   "round" => 0.001 // Округление
    *   "period" => true // Рассчёт текущего этапа ICO (сейчас хардкод)
    *
    *   @return string $result|$err
    */
    public function callMethod($method, $params = null, $format = null)
    {
        if(!self::checkWeb3())
        {
            return false;
        }

        $result = new \stdClass();
        $result->result = null;
        $undefined = self::checkMethod($method);

        if(!$undefined)
        {
            $this->web3Contract->at($this->MyContract->address)->call($method, $params, function($err, $res) use (&$result, $format)
            {
                $result = self::callParser($err, $res, $format);
            });
        }
        else
        {
            $result->error = $undefined;
        }

        return $result;
    }

    /**
    *   Изменение статуса ф-ции контракта
    *
    *   @param string $method - метод/переменная контракта
    *   @param array $params - параметры запуска метода контракта и адрес отправителя
    *   @param array $format - форматирование результата
    *
    *   Example $format
    *
    *   "unit" => "milliether", // Во что переводить
    *   "round" => 0.001 // Округление
    *   "period" => true // Рассчёт текущего этапа ICO (сейчас хардкод)
    *
    *   @return string $result|$err
    */
    public function sendMethod($method, $params = null, $format = null)
    {
        if(!self::checkWeb3())
        {
            return false;
        }

        $result = new \stdClass();
        $result->result = null;
        $undefined = self::checkMethod($method);

        if(!$undefined)
        {
            // Проверяем, есть ли у нас акк
            // Если нет, то создаём и делаем unlock
            if(self::checkDefaultAccount())
            {
                $params["from"] = (string) \Yii::$app->params["backendOperator"];

                $this->web3Contract->at($this->MyContract->address)->send($method, $params, function($err, $res) use (&$result, $format)
                {
                    $result = self::callParser($err, $res, $format);
                });
            }
            else
            {
                $result->error = "backendOperator does not found";
            }
        }
        else
        {
            $result->error = $undefined;
        }

        return $result;
    }

    /**
    *   Проверка наличия web3Contract
    *
    *   @param string $method
    *
    *   @return bool $exists
    */
    private function checkWeb3()
    {
        if(!isset($this->web3Contract))
        {
            return false;
        }

        return true;
    }

    /**
    *   Проверка метода контракта
    *   на наличие. Вернёт строку с ошибкой, если она есть
    *
    *   @param string $method
    *
    *   @return string $error
    */
    private function checkMethod($method)
    {
        if(!isset($this->MyContract->abi))
        {
            return "ABI is undefined";
        }

        if(!preg_match("/".$method."/i", json_encode($this->MyContract->abi)))
        {
            return "Method '".$method."' not found in ABI";
        }
    }

    /**
    *   Вернёт ABI контракта из API по адресу
    *
    *   @param string $address
    *
    *   @return json $abi
    */
    private function getContractAbi($address)
    {
        if(!isset($address))
        {
            return "Address is empty";
        }

        $Etherscan = new Etherscan();
        $abi = (string) $Etherscan->getContractAbi((string) $address);

        if($abi)
        {
            return $abi;
        }
    }

    /**
    *   Парсер callback`а контракта
    *
    *   @param obj $error
    *   @param obj $response
    *   @param array $format
    *
    *   @return object $parse error|result
    */
    private function callParser($error, $response, $format = null)
    {
        $parse = new \stdClass();
        $parse->result = null;
        $parse->error = null;

        if($error == null && isset($response[""]))
        {
            $response = $response[""];

            switch(gettype($response))
            {
                case "boolean":
                    $result = (boolean) $response;
                    break;

                case "string":
                    $result = (string) $response;
                    break;
                
                default:
                    $result = $response->toString();
                    break;
            }

            if($format)
            {
                foreach($format as $key => $param)
                {
                    switch($key)
                    {
                        // Конвертирует из Wei в указанную единицу
                        case "unit":
                            $temp = \Web3\Utils::fromWei($result, (string) $param);
                            $result = $temp[0]->toString();
                            break;

                        // Добавляет знаки после запятой
                        case "denary":
                            $result = $result * (float) $param;
                            break;

                        // Добавляет знаки после запятой
                        case "round":
                            $result = round($result, (int) $param);
                            break;

                        // Обработка периода
                        case "period":
                            $period = new \stdClass();

                            $period->current = (int) $result;
                            /*
                                TODO

                                Значение должно или расчитываться или браться из контракта
                            */
                            $period->end = 5;

                            $result = $period;
                            break;
                    }
                }
            }

            $parse->result = $result;
        }
        elseif($error !== null)
        {
            $parse->error = $error->getMessage();
        }

        return $parse;
    }

    /**
    *   Расчёт текущего % хода ICO
    *
    *   @param number $current
    *   @param number $total
    *
    *   @return int $progress
    */
    private function getProgress($current, $total)
    {
        if(!$current || !$total)
        {
            return 0;
        }

        //сейчас % очень маленький, поэтому * 10000000
        $progress = round((($current / $total) * 100) * 10000000);

        return $progress;
    }

    /**
    *   Проверит наличие аккаунтов на локальном сервере geth
    *   Совпадает ли он с backendOperator из config
    *   Создаст акк, если его нет
    *
    *   @return obj $defaultAccount
    **/
    private function checkDefaultAccount()
    {
        $defaultAccount = "";
        $backend = (string) \Yii::$app->params["backendOperator"];
        $password = \Yii::$app->params["ethAccountPass"];

        $this->web3->eth->accounts(function ($err, $accounts) use (&$defaultAccount, $backend)
        {
            if($err !== null)
            {
                var_dump($err->getMessage());
                return false;
            }

            // Совпадает ли account с BackendOperator
            foreach($accounts as $account)
            {
                if($account == $backend)
                {
                    $defaultAccount = $account;
                    break;
                }
            }

            if(!$defaultAccount)
            {
                // Создаём
                print_r("defaultAccount in Geth dosn't found");
                return false; 
            }
        });

        if(!$this->MyContract->resume)
        {
            // Делаем unlock
            if(!self::unlockAccount($defaultAccount, $password))
            {
                print_r("Unlock account failed");
                return false;
            }
        }

        return $defaultAccount;
    }

    /**
    *   Создаст аккаунт на локальном сервере geth
    *
    *   @param string $password
    *
    *   @return obj $newAccount
    **/
    private function createAccount($password)
    {
        $newAccount = "";

        $this->web3->personal->newAccount($password, function($err, $account) use (&$newAccount)
        {
            if($err !== null)
            {
                var_dump($err->getMessage());
                return;
            }
            
            $newAccount = $account;
        });

        return $newAccount;
    }

    /**
    *   Разблокирует аккаунт на локальном сервере geth
    *
    *   @param string $account - адрес аккаунта
    *   @param string $password - пароль от аккаунта
    *   @param int $duration - время анлока в сек.
    *
    *   @return bool $unlocked
    **/
    private function unlockAccount($account, $password, $duration = null)
    {
        $unlocked = false;

        $this->web3->personal->unlockAccount($account, $password, function ($err, $res) use (&$unlocked)
        {
            if($err !== null)
            {
                echo 'Error: ' . $err->getMessage();
                return;
            }

            $unlocked = true;
        });

        return $unlocked;
    }

    /**
    *   Проверка ETH адрес в WL
    *
    *   @param string $address
    *   @param bool $add - если true, то запускаем addToWhitelist
    *
    *   @return bool $result
    */
    public static function isWhitelisted($address, $add = false)
    {
        if(!$address)
        {
            return false;
        }

        $sale = \Yii::$app->params["saleContractAddress"];
        $saleContract = new Contract($sale);

        // Проверяем, нет ли пользователя в вайт листе (whitelist)
        $isWhitelisted = $saleContract->callMethod("whitelist", (string) $address);

        // Если адреса нет - добавляем
        if(!$isWhitelisted->error)
        {
            if($add)
            {
                return self::addToWhitelist((string) $address);
            }

            return (bool) $isWhitelisted->result;
        }

        return false;
    }

    /**
    *   Добавление ETH адрес в WL
    *
    *   @param string $address
    *
    *   @return bool $result
    */
    private function addToWhitelist($address)
    {
        if(!$address)
        {
            return false;
        }

        $sale = \Yii::$app->params["saleContractAddress"];
        $saleContract = new Contract($sale);

        // Добавляем в вайт лист (authorize)
        $params = [(string) $address];
        $authorize = $saleContract->sendMethod("authorize", $params);

        if($authorize->error)
        {
            var_dump($authorize->error);
            die();
            return false;
        }

        return true;
    }

    /**
    *   Вернёт контракт из БД по адресу
    */
    public static function getContract($address)
    {
        if(!$address)
        {
            return false;
        }

        return self::find()->where(['address' => $address])->one();
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%contracts}}';
    }
}