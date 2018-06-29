<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;

    $this->title = \Yii::t("app", 'Dashboard');
?>
<div class="site-index main-site-index">
<article class="article  box  box--1 col-lg-6 col-xs-12">
    <?= \app\components\DistributionWidget::widget() ?>
</article>

<article class="article  box  box--2 col-lg-6 col-xs-12">
    <div class="token-buy">
        <?php if (!Yii::$app->user->identity->confirmed_at): ?>

           <div class="panel panel-color panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= \Yii::t("app", "Warning!") ?></h3>
                </div>

                <div class="panel-body">
                    <p><?= \Yii::t("app", "You have not activated your email. Please follow the link from the letter delivered to your email")?></p>
                </div>
            </div>

        <?php else: ?>

            <?php if(!Yii::$app->user->identity->profile->id_number): ?>

                <div class="panel panel-color panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= \Yii::t("app", "Warning!")?></h3>
                    </div>

                    <div class="panel-body">
                        <p><?= \Yii::t("app", "Submit the KYC form for investment opportunity")?></p>
                        <p>
                            <a href="/kyc" class="btn btn-success"><?= \Yii::t("app", "Go to the form")?></a>
                        </p>
                    </div>
                </div>

            <?php elseif (!Yii::$app->user->identity->eth_address): ?>

                <div class="panel panel-color panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= \Yii::t("app", "Warning!")?></h3>
                    </div>

                    <div class="panel-body">
                        <?= \Yii::t("app", "<p>Your profile does not contain the address of your ethereum wallet.</p><p>For further work You need to specify the address of the wallet in the line below - at this address you will receive GloW tokens.</p><p>How to create a wallet you can read in <b>instructions.</b></p>") ?>

                        <?php $form = ActiveForm::begin([
                            'id' => 'account-form',
                        ]); ?>

                        <?= $form->field($model, 'eth_address')->textInput(['class' => 'form-control input-lg',])->label(Yii::t('app', 'Ethereum Address')) ?>

                        <div class="form-group">
                            <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-success']) ?>
                        </div>

                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

            <?php elseif (!Yii::$app->user->identity->is_whitelisted): ?>

                <div class="panel panel-color panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><?= \Yii::t("app", "Warning!")?></h3>
                    </div>

                    <div class="panel-body">
                        <?= \Yii::t("app", "Your address will be added to WhiteList within 15 minutes") ?>
                    </div>
                </div>

            <?php else: ?>

                <?= \app\components\InvestmentsWidget::widget() ?>

            <?php endif; ?>
        <?php endif; ?>
    </div>
</article>