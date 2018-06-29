<?= $appname ?>
<?php if($status == 1): ?>
        <?= \Yii::t('app', 'The whitelist addition transaction was successful') ?>

        <?= \Yii::t('app', 'Now you can buy tokens {0}',\Yii::$app->params['tokenName']) ?>
<?php elseif($status == 2): ?>
        <?= \Yii::t('app', 'The whitelist addition transaction failed') ?>

        <?= \Yii::t('app', 'Please perform the transaction again.') ?>
<?php endif; ?>
