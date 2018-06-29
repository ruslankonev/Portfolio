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
 * @var dektrium\user\models\User $model
 * @var dektrium\user\models\Account $account
 */

$this->title = Yii::t('user', 'Sign in');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="account-content">


    <fieldset class="login-form__fieldset">

        <legend class="token-buy__caption"></legend>
        <h4 class="header-title text-center"><?= Html::encode($this->title) ?></h4>

        <div class="alert alert-info">

            <p>
                <?= Yii::t(
                    'user',
                    'After registration you will receive an email with the generated password.'
                ) ?>
            </p>
        </div>
        <div class="alert alert-info">

            <p>
                <?= Yii::t(
                    'user',
                    'In order to finish your registration, we need you to enter following fields'
                ) ?>:
            </p>
        </div>
        <?php $form = ActiveForm::begin([
            'id' => 'connect-account-form',
        ]); ?>
        <?= $form->field($model, 'email')->textInput([
            'template' => '{input}{label}{error}',
            'class' => 'form-control'
        ]);
        ?>


        <?= $form->field($model, 'username')->textInput([
            'template' => '{input}{label}{error}',
            'class' => 'form-control'
        ]);
        ?>


        <?= Html::submitButton(Yii::t('user', 'Continue'), ['class' => 'btn btn-100']) ?>

        <?php ActiveForm::end(); ?>

        <p class="text-center">
            <?= Html::a(
                Yii::t(
                    'user',
                    'If you already registered, sign in and connect this account on settings page'
                ),
                ['/user/settings/networks']
            ) ?>.
        </p>


    </fieldset>


</div>


