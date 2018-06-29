<?php
    use yii\helpers\Html;
?>

<section class="section section--header">
    <header class="header grid wr">
        <?= Html::a('<object class="object-svg" data="/assets/svg/content/global-wasp-logo.svg" type="image/svg+xml"></object>',
            Yii::$app->homeUrl,
            ['class' => 'logo']
        ) ?>

        <button type="button" class="button-menu-mobile visible-xs visible-sm">
            <i class="mdi mdi-menu"></i>
        </button>
    </header>
</section>