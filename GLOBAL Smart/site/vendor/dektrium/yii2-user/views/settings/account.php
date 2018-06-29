<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this
 * @var yii\widgets\ActiveForm $form
 * @var dektrium\user\models\SettingsForm $model
 */

$this->title = Yii::t('user', 'Account settings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper-content">
    <div class="row">
        <div class="col-md-12">
            <h4 class="header-title"><?= Html::encode($this->title) ?></h4>

            <?php $form = ActiveForm::begin([
                'id' => 'account-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => false,
            ]); ?>

                <div class="row m-b-20 ">
                    <div class="col-xs-12">
                        <?= $form->field($model, 'email')->widget(\yii\widgets\MaskedInput::className(), ['clientOptions' => ['alias' =>  'email']]); ?>
                        <?= $form->field($model, 'username') ?>
                        
                        <?php
                            $disabled = (\Yii::$app->user->identity->is_whitelisted || !\Yii::$app->user->identity->eth_address) ? false : true;
                        ?>

                        <?= $form->field($model, 'eth_address')->textInput(['placeholder' => 'ex. 0xd4685c7f6798F4C93C28470df61553042Bd0646B', 'disabled' => $disabled]) ?>

                        <div id="password_fields" style="display: none;">
                            <?= $form->field($model, 'new_password')->passwordInput() ?>
                            <?= $form->field($model, 'current_password')->passwordInput() ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <?= Html::submitButton(Yii::t('user', 'Save'), ['class' => 'btn btn-success']) ?><br>
                    </div>

                    <div class="col-sm-6" style="text-align: right;">
                        <button type="button" class="btn btn-primary" onclick="$(this).hide();$('#password_fields').slideDown(); return false; ">
                            <?= Yii::t('app', 'Change password') ?>
                        </button>
                    </div>
                </div>

            <?php ActiveForm::end(); ?>
        </div>

        <?php if ($model->module->enableAccountDelete): ?>

            <div class="panel panel-danger">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= Yii::t('user', 'Delete account') ?></h3>
                </div>

                <div class="panel-body">
                    <p>
                        <?= Yii::t('user', 'Once you delete your account, there is no going back') ?>.
                        <?= Yii::t('user', 'It will be deleted forever') ?>.
                        <?= Yii::t('user', 'Please be certain') ?>.
                    </p>

                    <?= Html::a(Yii::t('user', 'Delete account'), ['delete'], [
                        'class' => 'btn btn-danger',
                        'data-method' => 'post',
                        'data-confirm' => Yii::t('user', 'Are you sure? There is no going back'),
                    ]) ?>
                </div>
            </div>

        <?php endif ?>
    </div>
</div>