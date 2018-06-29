<?php
/*
 * Добавляет указатель языка в ссылки
 */
namespace klisl\languages;

use Yii;

/**
 * Class UrlManager
 * @package klisl\languages
 */
class UrlManager extends \yii\web\UrlManager {

    /**
     * @param array|string $params
     * @return string
     */
    public function createUrl($params = null)
    {
        if(Yii::$app->request->get("ref") !== null)
        {
            //означает что текущее положение - главный экшн, т.е. пользователь авторизован. Передавать дальше ref нет смысла
            //также проверяем нет ли параметра authclient - для авторизации через соц сети не получится добавлять ref
            if((Yii::$app->request->pathInfo != "") && (empty($params['authclient'])))
            {
                $params['ref'] = Yii::$app->request->get("ref");
            }
        }

        //Получаем сформированную ссылку(без идентификатора языка)
        $url = parent::createUrl($params);

        if(empty($params['lang']))
        {
            $default_language = Yii::$app->getModule('languages')->default_language;
            $show_default = Yii::$app->getModule('languages')->show_default;

            //текущий язык приложения
            $curentLang = Yii::$app->language;

            if($curentLang == $default_language && !$show_default)
            {
                return $url;
            }
            else
            {
                //Добавляем к URL префикс - буквенный идентификатор языка
                if ($url == '/')
                {
                    return '/' . $curentLang;
                }
                else
                {
                    return '/' . $curentLang . $url;
                }
            }
        };

        return $url;
    }
}