<?php
    use backend\assets\AppAsset;
    use yii\helpers\Html;

    app\assets\AdminPanelAsset::register($this);
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

        <body class="login-page">
            <?php $this->beginBody() ?>

                <div class="wrap">
                    <main class="main main-auth main--global">
                        <article class="article  box  box--2">
                            <div class="token-buy login-block">
                                <?= $content ?>
                            </div>
                        </article>
                    </main>
                </div>

            <?php $this->endBody() ?>
        </body>
    </html>
<?php $this->endPage() ?>