# FBway_smart

Токен

https://rinkeby.etherscan.io/address/0x7710de679e4bf5828883c330f2ebf0ff567bc5cb

Публичные поля:

`name` - название токена

`symbol` - краткое название токена (3-4 символа)

`decimals` - количество знаков после запятой

`owner` - адрес владельца смарт-контракта токена

`initialSupply` - начальное количество токенов

`totalSupply` - общее количество токенов

`allowed` - список вида "адрес обладателя токенов -> адрес распоряжающегося токенами -> дозволенная для распоряжения сумма"

`balances` - хранит баланс в токенах

`finishMinting` - возвращает возможен ли выпуск токенов

`crowdsaleAddress` - адреса смарт-контрактов PreICO и ICO, обладающими правами на дистрибьюцию

Методы:

`balanceOf` - список обладателей токенов и их количества токенов

`transfer` - отправляет токены с адреса отправителя на указанный адрес получателя

`totalSupply` - возвращает количество выпущенных токенов

`approve` - наделяет указанный адрес правами на распоряжение указанной суммой токенов отправителя

`allowance` - возвращает количество токенов обладателя, которым может распоряжаться отправитель

`increaseApproval` - увеличивает количество токенов обладателя, которым может распоряжаться отправитель

`decreaseApproval` - снижает количество токенов обладателя, которым может распоряжаться отправитель

`transferFrom` - отправляет токены с указанного адреса обладателя на указанный адрес получателя при условии наличия соответствующих прав

`burn` - сжигает указанное количество токенов отправителя

`mintingFinished` - завершает выпуск токенов навсегда

`pause` - ограничивает операции с токенами

`unpause` - разрешает операции с токенами

`mintFromICO`  - выпускает токены для контракта Sale

`setSaleAddress` - устанавливает адрес Sale для токена

---


CloseICO

https://rinkeby.etherscan.io/address/0xfcc8d9300d6ddb21d1e264b4b3137f49e075f79a

Публичные поля:

`owner` - адрес владельца смарт-контракта PreICO/ICO

`token` - адрес смарт-контракта продаваемого токена

`backEndOperator`  - адрес оператора BackEnd

`weisRaised` - количество привлеченных средств в ICO в wei

`dollarPrice` -  курс эфира к доллару

`startCloseSale` - дата начала CloseSale

`endCloseSale` - дата окончания CloseSale

`investedEther` - количество эфира, который инвестировал инвестор

`whitelist` - список  адресов, допущенных для участия

`investors` - список авторизованных инвесторов

`buyPrice` - устанавливает цену токена в wei

`soldTokensCloseSale` - количество токенов, проданных на CloseSale

Методы:

`setBackEndAddress` - устанавливает адрес backEndOperator

`setStartCloseSale` - устанавливает начало CloseSale

`setEndCloseSale` - устанавливает окончание CloseSale

`setBuyPrice` - устанавливает курс эфира к доллару

`authorize` - добавляет адрес в список допущенных для участия в CloseSale

`revoke` - удаляет адрес из списка допущенных для участия в CloseSale

`fallback` - продает токены при переводе эфира

`isCloseSale` - проверяет, истек ли этап CloseSale

`mintManual` - ручной выпуск токенов для выбранных адресов

`transferEthFromContract` - отправка эфира с контракта CloseSale



PreICO

https://rinkeby.etherscan.io/address/0xcd40282d61830e80344e84fa9d138440730e41f8


Публичные поля:

`owner` - адрес владельца смарт-контракта PreSale

`token` - адрес смарт-контракта продаваемого токена

`backEndOperator` - адрес оператора BackEnd

`weisRaised` - количество привлеченных средств в ICO в wei

`dollarPrice` -  курс эфира к доллару

`startPreSale` - дата начала PreSale

`endPreSale` - дата окончания PreSale

`softCapPreSale` - нижний порог для сбора средств

`hardCapPreSale` - верхний порог для сбора средств

`investedEther` - количество эфира, который инвестировал инвестор

`whitelist` - список  адресов, допущенных для участия

`investors` - список авторизованных инвесторов

`buyPrice` - устанавливает цену токена в wei

`soldTokensPreSale` - количество токенов, проданных на PreSale


Методы:

`setBackEndAddress` - устанавливает адрес backEndOperator

`setStartPreSale` - устанавливает начало PreSale

`setEndPreSale` - устанавливает окончание PreSale

`setBuyPrice` - устанавливает курс эфира к доллару

`authorize` - добавляет адрес в список допущенных для участия в PreSale

`revoke` - удаляет адрес из списка допущенных для участия в PreSale

`fallback` - продает токены при переводе эфира

`isPreSale` - проверяет, истек ли этап PreSale

`mintManual` - ручной выпуск токенов для выбранных адресов

`transferEthFromContract` - отправка эфира с контракта PreSale

`refundPreICO` - возвращает средства инвесторам, если не был достигнут preICOsoftcap


MainSale

https://rinkeby.etherscan.io/address/0x1100587d28d579e1a5a159346417040fdc6c3928

Публичные поля:

`owner` - адрес владельца смарт-контракта MainSale

`token` - адрес смарт-контракта продаваемого токена

`backEndOperator` - адрес оператора BackEnd

`weisRaised` - количество привлеченных средств в ICO в wei

`dollarPrice` -  курс эфира к доллару

`startMainSale` - дата начала MainSale

`endMainSale` - дата окончания MainSale

`softCapMainSale` - нижний порог для сбора средств

`hardCapMainSale` - верхний порог для сбора средств

`investedEther` - количество эфира, который инвестировал инвестор

`whitelist` - список  адресов, допущенных для участия

`investors` - список авторизованных инвесторов

`buyPrice` - устанавливает цену токена в wei

`soldTokensMainSale` - количество токенов, проданных на MainSale


Методы:

`setBackEndAddress` - устанавливает адрес backEndOperator

`setStartMainSale` - устанавливает начало MainSale

`setEndMainSale` - устанавливает окончание MainSale

`setBuyPrice` - устанавливает курс эфира к доллару

`authorize` - добавляет адрес в список допущенных для участия в MainSale

`revoke` - удаляет адрес из списка допущенных для участия в MainSale

`fallback` - продает токены при переводе эфира

`isMainSale` - проверяет, истек ли этап MainSale

`mintManual` - ручной выпуск токенов для выбранных адресов

`transferEthFromContract` - отправка эфира с контракта MainSale

`refundICO` - возвращает средства инвесторам, если не был достигнут softCapMainSale

