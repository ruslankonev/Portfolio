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
 * @var dektrium\user\models\User $model
 * @var dektrium\user\Module $module
 */

$this->title = Yii::t('user', 'Sign up');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-content">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnChange' => false,
    ]) ?>

    <fieldset class="login-form__fieldset">

        <legend class="token-buy__caption"></legend>
        <h4 class="header-title text-center"><?= Html::encode($this->title) ?></h4>

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


        <?php if ($module->enableGeneratingPassword == false): ?>
            <?= $form->field($model, 'password')->textInput([
                'template' => '{input}{label}{error}',
                'class' => 'form-control'
            ])->passwordInput() ?>


        <?php endif ?>

        <?= Html::submitButton(Yii::t('user', 'Sign up'), ['class' => 'btn btn-100']) ?>

        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Already registered? Sign in!'), ['/user/security/login']) ?>
        </p>


    </fieldset>

    <?php ActiveForm::end(); ?>
</div>




