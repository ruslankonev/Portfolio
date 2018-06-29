<?php

namespace app\models;

use yii\db\ActiveRecord;

class Etherscan extends ActiveRecord
{
    private $cURL;

    public function __construct()
    {
        $this->cURL = curl_init();
        curl_setopt($this->cURL, CURLOPT_FAILONERROR, 1);
        curl_setopt($this->cURL, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->cURL, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->cURL, CURLOPT_TIMEOUT, 3);
    }

    public function __destruct()
    {
        curl_close($this->cURL);
    }

    /**
    *   Получение баланса по адресу
    *
    *   @param string $address
    **/
    public function getBalance($address)
    {
        if(!$address)
        {
            return fasle;
        }

        $params = array();
        $params["module"] = "account";
        $params["action"] = "balance";
        $params["address"] = (string) $address;
        $params["tag"] = "latest";

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получение баланса токенов по адресу смарт контракта
    *   и адресу кошелька
    *
    *   @param string $contract
    *   @param string $address
    **/
    public function getTokenBalance($contract, $address)
    {
        if(!$contract || !$address)
        {
            return fasle;
        }

        $params = array();
        $params["module"] = "account";
        $params["action"] = "tokenbalance";
        $params["contractaddress"] = (string) $contract;
        $params["address"] = (string) $address;
        $params["tag"] = "latest";

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получение TotalSupply по адресу смарт контракта
    *
    *   @param string $contract
    **/
    public function getTotalSupply($contract)
    {
        if(!$contract)
        {
            return fasle;
        }

        $params = array();
        $params["module"] = "stats";
        $params["action"] = "tokensupply";
        $params["contractaddress"] = (string) $contract;

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получение списка транзакций по адресу
    *
    *   @param string $address
    *   @param string $type
    **/
    public function getListTransactions($address, $type = null)
    {
        if(!$address)
        {
            return fasle;
        }

        $params = array();
        $params["module"] = "account";

        switch($type)
        {
            case "normal":
                $action = "txlist";
                break;

            case "internal":
                $action = "txlistinternal";
                break;
            
            default:
                $action = "txlist";
                break;
        }
        
        $params["action"] = $action;
        $params["address"] = (string) $address;
        $params["startblock"] = 0;
        $params["endblock"] = 99999999;
        //$params["sort"] = "asc";
        $params["sort"] = "desc"; // Чтобы последние транзакции были сверху

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получить статус выполнения контракта транзакции
    *
    *   Note: isError":"0" = Pass , isError":"1" = Error during Contract Execution 
    *
    *   @param string $txhash
    **/
    public function checkContract($txhash)
    {
        if(!$txhash)
        {
            return fasle;
        }

        $params = array();
        $params["module"] = "transaction";
        $params["action"] = "getstatus";
        $params["txhash"] = (string) $txhash;

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получить статус транзакции по хэшу транзакции
    *
    *   Note: status: 0 = Fail, 1 = Pass. Will return null/empty value for pre-byzantium fork 
    *
    *   @param string $txhash
    **/
    public function checkTransaction($txhash)
    {
        if(!$txhash)
        {
            return fasle;
        }

        $params = array();
        $params["module"] = "transaction";
        $params["action"] = "gettxreceiptstatus";
        $params["txhash"] = (string) $txhash;

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получить общее кол-во Эфира
    **/
    public function getTotalEther()
    {
        $params = array();
        $params["module"] = "stats";
        $params["action"] = "ethsupply";

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получить цену Эфира
    **/
    public function getEtherPrice()
    {
        $params = array();
        $params["module"] = "stats";
        $params["action"] = "ethprice";

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получить ABI контракта
    *
    *   @param string $address
    **/
    public function getContractAbi($address)
    {
        if(!$address)
        {
            return false;
        }

        $params = array();
        $params["module"] = "contract";
        $params["action"] = "getabi";
        $params["address"] = (string) $address;

        $url = $this->buildUrl($params);
        $response = $this->curlQuery($url);

        if($response->result)
        {
            return $response->result;
        }

        return $response;
    }

    /**
    *   Конвертируем Wei в эфир
    *
    *   @param int value - (1 Wei = 10 в -18 степени ETH)
    *
    *   @return float value - кол-во ETH
    **/
    public static function convertWeiToETH($value)
    {
        if(!$value)
        {
            return false;
        }

        return (float) $value * pow(10, -18);
    }

    /**
    *   Curl получение данные по url
    *
    *   @param string $url
    **/
    private function curlQuery($url)
    {
        if(!$url)
        {
            return false;
        }

        curl_setopt($this->cURL, CURLOPT_URL, $url);
        $result = curl_exec($this->cURL);

        return $this->parseResult($result);
    }

    /**
    *   Пернёт сеть по конфугу (ethNetwork)
    *
    *   @return string $apihost
    */
    private function getAPIHost()
    {
        $network = \Yii::$app->params['ethNetwork'];

        switch(strtolower($network))
        {
            case "mainnet":
                $apihost = "https://api.etherscan.io";
                break;

            case "ropsten":
                $apihost = "https://api-ropsten.etherscan.io";
                break;

            case "rinkeby":
                $apihost = "https://api-rinkeby.etherscan.io";
                break;

            case "kovan":
                $apihost = "https://kovan.etherscan.io";
                break;
            
            default:
                $apihost = "https://api.etherscan.io";
                break;
        }

        return $apihost;
    }

    /**
    *   Билдер ссылки для запроса через Curl
    *
    *   @param array $params
    *
    *   @return string $url
    **/
    private function buildUrl($params)
    {
        if(!$params)
        {
            return false;
        }

        $apihost = self::getAPIHost();
        $apikey = \Yii::$app->params['etherscanAPIkey'];

        $url = $apihost."/api?";

        foreach($params as $key => $p)
        {
            if($p)
            {
                $url .= $key."=".$p."&";
            }
        }

        $url .= "apikey=".$apikey;

        return $url;
    }

    /**
    *   Парсер ответа Curl
    *
    *   @param mixed $result
    **/
    /*
        TODO

        Парсить надо сам ответ, смотреть, какой статус, какое сообщение и тд
    */
    private function parseResult($result)
    {
        if(!$result || (gettype($result) == "object" && $result->status != 1))
        {
            return false;
        }

        return json_decode($result);
    }

    /*public static function tableName()
    {
        return '{{%contracts}}';
    }*/
}