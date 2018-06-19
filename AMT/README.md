# [AMT_token - официальный сайт проекта](http://gledchain.io)  

`name` - название токена
`symbol` - краткое название токена (3-4 символа)
`decimals` - количество знаков после запятой
`owner` - адрес владельца смарт-контракта токена
`initialSupply` - начальное количество токенов
`totalSupply` - общее количество токенов
`allowed` - список вида "адрес обладателя токенов -> адрес распоряжающегося токенами -> дозволенная для распоряжения сумма"
`balanceOf` - список обладателей токенов и их количества токенов
`transfer` - отправляет токены с адреса отправителя на указанный адрес получателя
`transferFrom` - отправляет токены с указанного адреса обладателя на указанный адрес получателя при условии наличия соответствующих прав
`approve` - наделяет указанный адрес правами на распоряжение указанной суммой токенов отправителя
`allowance` - возвращает количество токенов обладателя, которым может распоряжаться отправитель
`increaseApproval` - увеличивает количество токенов обладателя, которым может распоряжаться отправитель
`decreaseApproval` - снижает количество токенов обладателя, которым может распоряжаться отправитель
`burn` - сжигает указанное количество токенов отправителя
---

Crowdsale
https://rinkeby.etherscan.io/address/0xe85e9d1d23a5d2c9702b2d54da0ce6694a58518f

`owner` - адрес владельца смарт-контракта PreICO/ICO
`investedEther` - инвестировано эфиров аккаунтом
`startSale` - дата начала распродажи
`stage1-stage4` - переменные стадий распродажи;
`buyPrice` - цена токена
`softCapPreSale` - минимальный порог сбора presale
`hardCapPreSale` - максимальный порог сбора presale
`sofrCapMainSale` - минимальный порог сбора mainSale
`hardCapMainSale` - максимальный порог сбора mainSale
`soldTokens` - продано токенов
`weiRaised` - количество привлеченных средств в wei
`isFinalized` - окончен ли PreICO/ICO
`setStartSale` - устанавливает курс токена к эфиру
`setStage1PS - setEndSatage4MS` - сеттеры для изменений дат порогов распродажи
`setHardcap` - установить верхний порог привлечения средств на PreICO/ICO
`setSoftcap` - устанавливает нижний порог привлечения средств на PreICO/ICO
`setPrices` - изменяет цену токена
`setBackEndOperator` - меняет адрес оператора backEnd
`discountSum` - расчет бонусных токенов от суммы
`discountDate` - расчет бонусных токенов от даты
`transferEthFromContract` - трансфер эфира с контракта
`transferTokensFromContract` - трансфер токенов с контракта
`refundPreSale` - возврат средств инвестора в случае недостижения softCap
`refundMainSale` - возврат средств инвестора в случае недостижения softCap
`viewEtherCollect` - вывод количества собранного эфира
`viewSoldTokens` - вывод кодичества проданных токенов
`viewTotalTokens` - вывов общего количества токенов
`finalize` - завершает ICO, если он уже истек

Инструкция по внесению смарт-контракта в блокчейн через Remix.

1. Вставить код `fullcontract.sol` в редактор кода (remix.ethereum.org)
2. Нажать кнопку deploy, расположенную в интерфейсе справа во вкладке Run
3. В консоли снизу после успешного внесения смарт-контракта токена в блокчейн, появится запись о выполнении. Необходимо кликнуть на Details и в списке найти хэш транзакции
4. По этому хэшу найти транзакцию на http://etherscan.io/