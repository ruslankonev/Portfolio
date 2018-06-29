<?php

namespace app\models;

use yii\db\ActiveRecord;

class Blockchain extends ActiveRecord
{
    private $infohost = "https://blockchain.info/";
    private $apihost = "https://api.blockchain.info/v2/receive";
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
    *   Получить баланс по адресу
    *
    *   https://blockchain.info/balance?active=$address
    *   Multiple Addresses Allowed separated by "|"
    *
    *   @param string $address
    **/
    public function getBalance($address)
    {
        if(!$address)
        {
            return fasle;
        }

        $network = "info";
        $module["balance"] = "";
        $params["active"] = (string) $address;

        $url = $this->buildUrl($network, $module, $params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получить адрес
    *
    *   https://blockchain.info/ru/rawaddr/$bitcoin_address
    *   Address can be base58 or hash160
    *
    *   @param string $address
    **/
    public function getSingleAddress($address)
    {
        if(!$address)
        {
            return fasle;
        }

        $network = "info";
        $module["rawaddr"] = (string) $address;

        $url = $this->buildUrl($network, $module, null);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Получить транзакцию по хэшу транзакции
    *
    *   https://blockchain.info/rawtx/$tx_hash
    *   You can also request the block to return in binary form (Hex encoded) using ?format=hex
    *
    *   @param string $txhash
    *   @param string $format
    **/
    public function getTransactionByHash($txhash, $format = null)
    {
        if(!$txhash)
        {
            return fasle;
        }

        $network = "info";
        $module["rawtx"] = (string) $txhash;

        if($format)
        {
            $params["format"] = $format;
        }

        $url = $this->buildUrl($network, $module, $params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Конвертируем satoshi в биткойн
    *
    *   @param float value - (1 Satoshi = 0.00000001 BTC)
    *
    *   @return float value - кол-во BTC
    **/
    public function convertSatoshiToBTC($value)
    {
        if(!$value)
        {
            return false;
        }

        return (float) $value / 100000000;
    }

    /**
    *   Получить курсы обмена валют
    *
    *   На самомо деле все валюты нам и не нужны, ведь можно конвертировать сразу в нужную валюту
    *   По идеи можно хранить в сессии и обновлять раз в 15 минут. 
    *
    *   @param string currency - какая то конкретная валюта
    **/
    public function getExchangeRates($currency = null)
    {
        $network = "info";
        $module["ticker"] = "";

        $url = $this->buildUrl($network, $module, null);
        $response = $this->curlQuery($url);

        if($currency)
        {
            $response = $response->$currency;
        }

        return $response;
    }

    /**
    *   Конвертор валют. Вернёт кол-во биткойнов на указанное кол-во валюты
    *   https://blockchain.info/tobtc?currency=USD&value=500
    *
    *   @param string currency - код валюты
    *   @param float value - кол-во валюты
    *
    *   @return float response - вернёт кол-во биткойнов на указанное кол-во валюты
    **/
    public function convertCurrencyToBTC($currency, $value)
    {
        if(!$currency || !$value)
        {
            return false;
        }

        $network = "info";
        $module["tobtc"] = "";

        $params["currency"] = (string) $currency;
        $params["value"] = (float) $value;

        $url = $this->buildUrl($network, $module, $params);
        $response = $this->curlQuery($url);

        return $response;
    }

    /**
    *   Curl получение данные по url
    *
    *   @param string $url
    **/
    /*
        TODO
    
        добавить параметр парсить или нет
    */
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
    *   Билдер ссылки для запроса через Curl
    *
    *   @param string $network - тип сети (info / api) записит от запроса
    *   @param array $module - тип запроса => параметр, например "rawblock"
    *   @param array $params - параметры запроса, например limit, format, offset
    **/
    private function buildUrl($network = "info", $module, $params)
    {
        /*
            Для info обязателен $module
            Для api обязателен $params
        */
        if(!$module)
        {
            return false;
        }

        if($network == "info")
        {
            $url = $this->infohost;
            $url .= key($module);
            $url .= (current($module)) ? "/".current($module) : "";
        }
        elseif($network == "api")
        {
            $url = $this->apihost;
        }

        /*
            Добавляем параметры к запросу, если они есть
        */
        if($params)
        {
            $url .= "?";

            $i = 0;
            $len = count($params);

            foreach($params as $key => $param)
            {
                if($param)
                {
                    if($i > 0)
                    {
                        $url .= "&";
                    }

                    $url .= $key."=".$param;

                    $i++;
                }
            }
        }

        if($network == "api")
        {
            /*
                Ключ нужен только для API
            */
            $url .= "&key=".\Yii::$app->params['blockchainAPIkey'];
        }

        return $url;
    }

    /**
    *   Парсер ответа Curl
    *
    *   @param mixed $result
    **/
    private function parseResult($result)
    {
        if(!$result)
        {
            return false;
        }

        return json_decode($result);
    }
}