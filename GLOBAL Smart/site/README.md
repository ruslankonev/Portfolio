GLOBAL WASP
===========

Личный кабинет инвестров ([Preview](https://tokensale.globalwasp.com/))

Smart contract ([link](https://github.com/elephant-marketing/Global_smart))

Что сделано:
------------
* Регистрация в ЛК (E-mail, Facebook, Google+), подтверждение по E-mail
* KYC форма + Terms
* Возможность инвестирования в BTC и ETH
* Информация по транзакциям
* Экспорт транзакций в рамках пользователя
* Партнёрская программа

Системные требования:
---------------------
* Операционная система Unix (Linux, FreeBSD и пр.);
* Веб-сервер Apache 2.4 и выше или nginx 1.9;
* Поддержка файлов настройки сервера (.htaccess)
* PHP 7.1 и выше (может быть собран как модуль Apache или работать, как CGI-скрипт);
* СУБД MySQL 5.0 и выше

Расширения: Ctype, cURL, DOM, iconv, JSON, libxml, MBstring, SimpleXML
Модули: BC Math, Imagick

На сервер должен быть NodeJS (>= 6.0.0) и NPM (>= 3.0.0), а также Geth и Blockchain Wallet Service (>=0.26.1)

#### Geth

```
geth --rinkeby --rpc --rpcaddr "127.0.0.1" --rpcport "8545"
geth --rinkeby --rpc --rpcaddr "127.0.0.1" --rpcport "8545" --rpcapi db,eth,net,web3,personal,web3
```

#### Blockchain Wallet Service

```
blockchain-wallet-service start --port 3000
```

### Версия

v 1.0.4