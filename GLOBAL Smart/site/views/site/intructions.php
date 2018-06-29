<?php
    $this->title = \Yii::t("app", 'Instructions');
    $this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper-content instruction-content">
    <div class="row">
        <div class="col-xs-12">
                <div class="box box-solid">
                    <div class="box-body box-instruction">
                        <? if(Yii::$app->language == "ru"): ?>

                            <h4 class="header-title">Metamask на русском.</h4>
                            <p>Metamask – это плагин для браузера Chrome для быстрых платежей и переводов в сети Ethereum, а также для работы с криптовалютными биржами.</p>
                            <div class="bs-shortcode-alert alert alert-success2">Это полноценный, лёгкий кошелек, который находится в вашем браузере и явно выигрывает у других онлайн кошельков (как минимум – приватные ключи всегда у вас).</div>
                            <p>Блокчейн позволяет создавать новый тип приложений – Dapps, чтобы с ними безопасно взаимодействовать нужен посредник. Его роль и выполняет плагин Метамаск. Если работать без него, то вам придётся каждый раз светить своим базовым кошельком (а не все сайты заслуживают такое доверие).</p>
                            <h2>Зачем нужен Metamask?</h2>
                            <p>Плагин выполняет роль промежуточного звена между вашим основным кошельком и обычными сайтами. Вы переводите на него эфир и токены, которые хотите активно использовать, а все остальные ваши активы спокойно и незаметно хранятся на базовом кошельке (я рекомендую MyEtherWallet).</p>
                            <p>Это делается, чтобы защитить данные ваших криптовалютных кошельков, а также, чтобы сделать работу с эфиром и другими токенами удобной и быстрой.</p>
                            <p>Так вы можете завести несколько кошельков под разные цели и держать их на разных компьютерах. Аналог – несколько дебетовых пластиковых карт, с которых выполняются разные задачи (детские карманные расходы, карта для туристической поездки, подарочные карты и так далее). Вы пополняете их с одного банковского счета, но если вдруг данные какой-либо карты попадут к мошенникам, они не смогут добраться к вашим сбережениям на основном счете.</p>
                            <p>Так и с МетаМаск: установили плагин для Chrome, ввели данные, Metamask их зашифровал, пополнили новый кошелек, а дальше вы просто заходите в плагин для проверки баланса или создания транзакции (а не открываете базовый криптовалютный кошелек, из-за всякой мелочи не вводите пароль, файл-ключ).</p>
                            <h2>Как установить Metamask.</h2>
                            <p>Запустите <mark class="bs-highlight bs-highlight-default">браузер Chrome</mark> (ещё есть Firefox и, возможно, запустят плагин для Opera), откройте <a href="https://metamask.io/" target="_blank" rel="noopener">https://metamask.io/</a>, нажмите на ссылку Get Chrome Plugin.</p>
                            <p><img class="aligncenter size-full wp-image-373    b-loaded" alt="Установка плагина metamask." width="823" height="464" srcset="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install.png 823w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-300x169.png 300w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-768x433.png 768w" sizes="(max-width: 823px) 100vw, 823px" src="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install.png"></p>
                            <p>Далее нажмите кнопку “Установить”.</p>
                            <p><img class="aligncenter size-full wp-image-375    b-loaded" alt="Как установить плагин Metamask в Chrome" style="max-width: 990px;width:100%" srcset="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-chrome.png 990w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-chrome-300x35.png 300w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-chrome-768x90.png 768w" sizes="(max-width: 990px) 100vw, 990px" src="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-chrome.png"></p>
                            <p>Вы получите запрос на разрешение просматривать ваши данные, передавать их в рамках сети Ethereum и другие условия работы Metamask. Выбираете кнопку “Установить расширение”.</p>
                            <p>Плагин установили, теперь нужно его активировать.</p>
                            <p>Для этого нажмите на его пиктограмму в правой части строки браузера:</p>
                            <p><img class="aligncenter size-full wp-image-376    b-loaded" alt="Скачать плагин metamask." width="400" height="79" srcset="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-chrome-2.png 400w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-chrome-2-300x59.png 300w" sizes="(max-width: 400px) 100vw, 400px" src="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-chrome-2.png"></p>
                            <p>Принимаем условия политики конфиденциальности (Accept), затем прокрутите вниз все положения и снова нажмите Accept.</p>
                            <h2>Metamask: инструкция к плагину.</h2>
                            <p>Теперь нужно зарегистрировать свой профиль в Metamask.</p>
                            <p>Придумайте надёжный пароль <mark class="bs-highlight bs-highlight-default">из 10 символов</mark> (большие и маленькие буквы английского алфавита, цифры, специальные символы). Пароль запишите в безопасном месте либо добавьте в надёжный менеджер паролей. Нажмите Create.</p>
                            <div class="bs-shortcode-alert alert alert-info2">Если у вас уже есть профиль в Metamask, то при повторной установке плагина (либо при установке в другой браузер на новом компьютере) вы можете его импортировать (ссылка для этого – Import Existing DEN).</div>
                            <p>Дальше вы увидите список из 12 слов, которые нужны для восстановления хранилища с вашими данными.</p>
                            <p>Metamask шифрует все доверяемые ему данные и складывает их в специальный файл. Доступ к нему есть только у вас, он не посылает его в сеть. Чтобы расшифровать этот файл, нужно знать все 12 слов (мнемонический пароль из фраз), которые отобразятся во время установки. Запишите их и храните очень аккуратно.</p>
                            <p><img class="aligncenter size-full wp-image-377    b-loaded" alt="Metamask: 12 слов для восстановления кошелька." width="359" height="453" srcset="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-secret.png 359w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-secret-238x300.png 238w" sizes="(max-width: 359px) 100vw, 359px" src="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-install-secret.png"></p>
                            <p>Для подстраховки нажмите кнопку Save Seed Words As File – плагин скачает список фраз в отдельный файл на ваш компьютер.</p>
                            <p>Когда фразы записаны, нажимайте I’ve copied it somewhere safe.</p>
                            <h2>Настройка Metamask.</h2>
                            <p>Настроим ваш профиль в Metamask.</p>
                            <p>Выберите название для вашего профиля (редактируется по ссылке edit над названием по умолчанию).</p>
                            <p><img class="aligncenter size-full wp-image-379    b-loaded" alt="Metamask на русском." width="361" height="278" srcset="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-settings.png 361w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-settings-300x231.png 300w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-settings-260x200.png 260w" sizes="(max-width: 361px) 100vw, 361px" src="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-plugin-settings.png"></p>
                            <p>Правее есть меню, в котором доступны функции:</p>
                            <ul>
                                <li>Просмотр профиля через <a href="https://etherscan.io/address/" target="_blank" rel="noopener">Etherscan</a> (состояние вашего нового счета и отчет по всем сделкам).</li>
                                <li>Show QR Code – показать код для смартфона.</li>
                                <li>Скопировать адрес в буфер обмена.</li>
                                <li>Экспортировать приватные ключи.</li>
                            </ul>
                            <div class="bs-shortcode-alert alert alert-danger2">Обязательно экспортируйте приватный ключ и сохраните его в безопасном месте.</div>
                            <p>Все перечисленные данные относятся к вашему новому счету в блокчейне Ethereum. Сюда можно перевести Eth и токены ERC-20.</p>
                            <p>Если вы хотите использовать возможности других сетей, то следует переключить Metamask на них:</p>
                            <p><img class="aligncenter size-full wp-image-381" data-src="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-etc-eth.png" alt="Metamask и Ethereum Classic." width="369" height="390" srcset="https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-etc-eth.png 369w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-etc-eth-284x300.png 284w, https://bitcoinfox.ru/wp-content/uploads/2017/10/metamask-etc-eth-368x390.png 368w" sizes="(max-width: 369px) 100vw, 369px"></p>
                            <h2>Как пользоваться Metamask.</h2>
                            <p>Плагин предназначен, чтобы взять удар на себя: сеть видит только его статус и историю. Его вы можете использовать, как промежуточный кошелек, чтобы не показывать посторонним ваш основной MyEtherWallet.</p>
                            <p>Для этого нужно пополнить баланс:</p>
                            <ul>
                                <li>Можно купить ETH на бирже с последующим переводом на новый eth-адрес,</li>
                                <li>Напрямую с карты (но выгоднее – через депозит),</li>
                                <li>Сделать пополнение или обмен прямо в плагине (следите за итоговыми комиссиями),</li>
                                <li>Пополнить кошелек через обменники,</li>
                                <li>Либо перевести с вашего базового MyEtherWallet.</li>
                            </ul>
                            <p>В последнем случае вы получаете также возможность передачи на баланс большинства токенов.</p>
                            <p>Если у вас есть приватные ключи от других кошельков сети Ethereum, вы можете импортировать их в Metamask (учитывая, что MyEtherWallet никак себя не скомпрометировал и представляет более надёжное решение, не знаю, зачем делать такой импорт).</p>
                            <p>Справедливо и обратное: возможен перенос приватных ключей из Metamask в новый (!) кошелек MyEtherWallet.</p>
                            <div class="bs-shortcode-alert alert alert-danger2">Внимание: когда делаете импорт приватного ключа, всегда используйте новый, чистый кошелек-донор с пустым балансом.</div>

                        <? else: ?>

                            <h4 class="header-title">Metamask</h4>
                            <p>MetaMask is an Ethereum extension for your browser. It connects you to Ethereum applications (called dApps) easily and securely. MetaMask is also a digital wallet: you can store digital currencies in it and use it to buy…an album for instance!</p>
                            <p>To get started, all you need to do is install a Chrome extension. This three minute guide
                                will show you how to do that.</p>
                            <p  class="header-title"><b>Step 1: Click here to go to the Chrome Webstore and add the MetaMask plugin.</b></p>
                            <img src="/assets/images/1step.jpeg" alt="" style="width: 100%">
                            <p  class="header-title"><b> Step 2: Click “Add Extension” to confirm and MetaMask will be added.</b></p>
                            <img src="/assets/images/2step.jpeg" alt="" style="width: 100%">
                            <p  class="header-title"><b>  Step 3: Read the privacy notice and click ‘Accept’.</b></p>
                            <img src="/assets/images/3step.jpeg" alt="" style="width: 100%">
                            <p  class="header-title"><b>  Step 4: Scroll down the Terms of Use and click ‘Accept’ (the “Accept” button will only activate if you scroll the contract all the way down).</b></p>
                            <img src="/assets/images/4step.jpeg" alt="" style="width: 100%">
                            <p  class="header-title"><b>  Step 5: Create your password. Make sure it’s secure!</b></p>
                            <img src="/assets/images/5step.jpeg" alt="" style="width: 100%">
                            <p  class="header-title"><b>   Step 6: MetaMask will now give you a ‘seed phrase’ that we advise you to keep somewhere safe where you are sure to find it (and no one else).</b></p>
                            <img src="/assets/images/6step.jpeg" alt="" style="width: 100%">
                            <p  class="header-title"><b> Step 7: Congratulations! You now have your first Ethereum account. </b></p>
                            <img src="/assets/images/7step.jpeg" alt="" style="width: 100%">
                            
                        <? endif; ?>
                    </div>
                </div>
        </div>
    </div>
</div>