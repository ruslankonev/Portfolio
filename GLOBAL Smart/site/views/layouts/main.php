<?php
use yii\helpers\Html;
?>

<? if(Yii::$app->controller->action->id === 'login'): ?>

    <?php
    /**
     * Do not use this code in your template. Remove it.
     * Instead, use the code  $this->layout = '//main-login'; in your controller.
     */
    echo $this->render('main-login', ['content' => $content]);
    ?>

<? else: ?>

    <?php

        if(class_exists('backend\assets\AppAsset'))
        {
            backend\assets\AppAsset::register($this);
        }
        else
        {
            app\assets\AppAsset::register($this);
        }

        app\assets\AdminPanelAsset::register($this);

        $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@app/views/admin-panel-dist');
    ?>

    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
        <head>
            <meta charset="<?= Yii::$app->charset ?>"/>
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
            <link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
            <link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
            <link rel="manifest" href="/favicon/site.webmanifest">
            <link rel="mask-icon" href="/favicon/safari-pinned-tab.svg" color="#5bbad5">
            <meta name="msapplication-TileColor" content="#da532c">
            <meta name="theme-color" content="#ffffff">

            <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css" />
            <link href="//fonts.googleapis.com/css?family=Fira+Sans:400,700" rel="stylesheet">
            <link href="//fonts.googleapis.com/css?family=Montserrat:300,600,700" rel="stylesheet">

            <?= Html::csrfMetaTags() ?>
            <title><?= Html::encode($this->title) ?></title>
            <?php $this->head() ?>
        </head>
        <body>
            <?php $this->beginBody() ?>

            <div class="wrap main-wrap">
                <?= $this->render('header.php', ['directoryAsset' => $directoryAsset]) ?>

                <div class="content">
                    <?= $this->render('left.php', ['directoryAsset' => $directoryAsset]) ?>
                    <?= $this->render('content.php', ['content' => $content, 'directoryAsset' => $directoryAsset]) ?>
                </div>
            </div>

            <?php 
                $jscode = \app\components\SAWidget::widget();
                $this->registerJs($jscode);
            ?>

            <?php $this->endBody() ?>
        </body>
    </html>
    <?php $this->endPage() ?>

<? endif;?>