<?php
    use yii\helpers\Html;
    use yii\bootstrap\ActiveForm;
?>

<?php if(isset($currencies)): ?>

    <?php $form = ActiveForm::begin(['id' => 'invest-form', 'options' => ['class' => 'processed']]); ?>

        <fieldset class="token-buy__fieldset">

            <?= \app\components\LoadingWidget::widget() ?>

            <h1 class="token-distribution__caption"><?= \Yii::t("app", "Buy Tokens") ?></h1>
            <br>
            <div class="row">
                <div class="col-lg-6 col-xs-6">
                    <label  ><?= \Yii::t("app", "The choice of currencies") ?> </label>

                    <?php
                        $first = reset($currencies);
                        $model->currency_id = (int) $first->id;
                    ?>
                    
                    <?php foreach($currencies as $currency): ?>

                        <?= $form->field($model, 'currency_id')
                            ->radio([
                                'template' => '{input}{beginLabel}<span class="radio-currency">'.mb_strtolower($currency["code"]).'</span>{endLabel}{error}',
                                'id' => 'currency-'.$currency["id"],
                                'value' => $currency["id"],
                                'data-code' => $currency["code"]
                            ])
                            ->label($currency["name"], ['class' => 'token-buy__label', 'for' => 'currency-'.$currency["id"]]);
                        ?>
                    <?php endforeach; ?>

                    <?= $form->field($model, 'investments')
                        ->textInput([
                            'template' => '{input}{label}{error}',
                            'onkeypress' => 'return (event.charCode >= 48 && event.charCode <= 57) || event.charCode == 46 || event.charCode == 0',
                            'class' => 'form-control',
                            'type' => 'tel',
                            'id' => 'investments-count'
                        ])
                        ->label(\Yii::t("app", "Purchase amount"));
                    ?>
                </div>
                <div class="col-lg-6 col-xs-6 disabled-inputs">
                    <h3 class="token-distribution__total receive-text"><?=\Yii::t("app", "You will receive when buying")?></h3>
                    <?/*= $form->field($model, 'tokens')
                        ->textInput([
                            'template' => '{input}{label}',
                            'class' => 'form-control',
                            'id' => 'token-count',
                            'disabled' => true,
                            'readonly' => 'readonly'
                        ])
                        ->label(\Yii::t("app", "The number of received tokens"));
                    */?>

                    <?/*= $form->field($model, 'bonus')
                        ->textInput([
                            'template' => '{input}{label}',
                            'class' => 'form-control',
                            'id' => 'bonus-count',
                            'disabled' => true,
                            'readonly' => 'readonly'
                        ])
                        ->label(\Yii::t("app", "The number of bonuses received"));
                    */?>

                    <?= $form->field($model, 'total_tokens')
                        ->textInput([
                            'template' => '{input}{label}',
                            'class' => 'form-control',
                            'id' => 'total-tokens-count',
                            'disabled' => true,
                            'readonly' => 'readonly'
                        ])
                        ->label(\Yii::t("app", "The total amount of tokens"));
                    ?>
                </div>
            </div>

            <p>
                <svg class="svg-icon  svg-icon--danger">
                    <use xlink:href="/assets/svg-sprites/i-sprite.svg#i-danger-no-opt"></use>
                </svg>
                <?= \Yii::t("app", 'The number of tokens may change due to the difference in exchange rates') ?>
            </p>

            <div class="form-group">
                <?= Html::submitButton(Yii::t('app', 'Buy Tokens'), ['class' => 'token-buy__button', 'id' => 'transferFunds']) ?>
            </div>

        </fieldset>

    <?php ActiveForm::end(); ?>

    <?=  $this->registerJsFile('js/invest-form.js', ['depends' => [\yii\web\JqueryAsset::className()]]); ?>

<?php endif; ?>