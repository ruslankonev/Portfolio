yii2-languages
=================

Пакет для создания мультиязычного сайта или WEB-приложения на php-фреймворке Yii-2. Текущий язык отображается в URL. Есть возможность 
убрать основной язык из отображаемых. 
Пример (русский использован в качестве основного языка и выбрана опция не выводить основной язык):

* http://site.com
* http://site.com/en
* http://site.com/uk

* http://site.com/contact
* http://site.com/en/contact
* http://site.com/uk/contact


Смена языка осуществляется при нажатии на соответствующие ссылки которые выводятся виджетом. Так же, язык можно менять прямо в адресной строке. 
Не используются сессии, куки и база данных для работы расширения. Код рассчитан на максимальное быстродействие. 
Использование данного модуля мультиязычности не требует внесения изменений в правила маршрутизации компонента urlManager.

Расширение устанавливает текущую локализацию приложения в зависимости от выбранного языка. 


  
Установка
------------------
* Установка расширения с помощью Composer.

```
composer require klisl/yii2-languages 
```


* Внести изменения в файл **frontend\config\main.php** (для версии advanced) или в 
файл **config/web.php** (для версии basic):


(1)  в массив "return" вставить:
```php
'sourceLanguage' => 'ru', // использовать в качестве ключей переводов
```

(2)  ниже, так же в массив "return" вставить регистрацию и параметры модуля:
```php
'modules' => [
    'languages' => [
        'class' => 'klisl\languages\Module',
        //Языки используемые в приложении
        'languages' => [
            'English' => 'en',
            'Русский' => 'ru',
            'Українська' => 'uk',
        ],
        'default_language' => 'ru', //основной язык (по-умолчанию)
        'show_default' => false, //true - показывать в URL основной язык, false - нет
    ],
],
```
По-умолчанию модуль использует английский, русский и украинский языки. Удалить или добавить нужные в параметрах модуля.


(3) в массиве "components" есть вложенный массив "request", вставить в него:
```php
'baseUrl' => '', //убрать frontend/web
'class' => 'klisl\languages\Request'
```

(4) в компоненте приложения "urlManager" включаем ЧПУ для ссылок, подключаем класс UrlManager переопределенный данным расширением:
```php
'urlManager' => [
    'enablePrettyUrl' => true,
    'showScriptName' => false,
    'enableStrictParsing' => true,
    'class' => 'klisl\languages\UrlManager',
    'rules' => [
        'languages' => 'languages/default/index', //для модуля мультиязычности
        //далее создаем обычные правила
        '/' => 'site/index',
        '<action:(contact|login|logout|language|about|signup)>' => 'site/<action>',
    ],
],
```
В начале списка правил указываем правило для работы модуля мультиязычности. Остальные правила формируются обычным образом.

(5) в шаблон **frontend\views\layouts\main.php** или нужный вид вставить вывод виджета отображающего ссылки для переключения языков:
```php

    <?= klisl\languages\widgets\ListWidget::widget() ?>

```



Использование
-------------

#### Перевод фраз.
Для перевода отдельных слов и фраз (пунктов меню например), нужно создать языковые файлы в папке common/messages. 
Если используется версия Yii2 Basic, то папка common будет отсутствовать в корне проекта, в таком случае ее нужно создать. 
Количество языковых файлов будет столько, сколько у вас дополнительных языков для перевода, не считая основного. 
Например, если используется русский, украинский и английский, то создаем папки “en” и “uk” при условии, что русский является основным языком. Метка основного языка не отображается в URL. 

Напоминаю, что основной язык задается в файле frontend\config\main.php, в массиве «return» строкой
**'sourceLanguage' => 'ru',**

Пример языкового файла common\messages\en\app.php:
```php
<?php
return [
    'Блог' => 'Blog',
    'О нас' => 'About me',
    'Контакты' => 'Contact',
];
```
то есть в массив "return" нужно вписать все слова и фразы которые нужно переводить. 
Аналогично нужно создать файл common\messages\uk\app.php для украинского языка.

В коде (обычно в шаблонах и файлах представлений), фразы которые требуют перевода заключать в вызов метода **Yii::t()**.
Согласно нашей конфигурации так: 
```php
Yii::t('app', 'Блог')
```
Русский у нас указан в качестве языка по-умолчанию, поэтому если текущий язык – русский, выведется слово «Блог», а если английский - 'Blog'.


#### Перевод статичных страниц.
Статичные страницы - это страницы, которые хранят текст в самом файле (в коде), а не берут контент из базы данных. Целые страницы содержат слишком много текста, в связи с чем нецелесообразно использовать метод Yii::t().

В нужном контроллере создаем действие для каждой такой страницы:
```php
public function actionStat()
{
    $language = Yii::$app->language; //текущий язык
    //выводим вид соответствующий текущему языку
    return $this->render('statPages/stat-'.$language);     
}
```
то есть вторая часть название файла вида берется из названия языка. 
В данном случае в папке с видами создаем отдельную папку для статичных файлов statPages (это не обязательно), а в ней файлы с контентом соответствующего языка:
- stat-ru.php
- stat-uk.php
- stat-en.php


#### Перевод статей хранящих контент в базе данных.

Для настройки базы данных и моделей выполнить действия указанные в данной статье статье: <http://klisl.com/multilingual_BD.html>.  


Подробное описание расширения (с небольшими отличиями т.к. рассмотрен ручной вариант создания модуля): <http://klisl.com/multilingual_Yii2.html>.

Мой блог: [klisl.com](http://klisl.com)  