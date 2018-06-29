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
 * @var dektrium\user\models\RecoveryForm $model
 */

$this->title = Yii::t('user', 'Reset your password');
$this->params['breadcrumbs'][] = $this->title;
?>


<fieldset class="login-form__fieldset">

    <legend class="token-buy__caption"></legend>
    <h4 class="header-title text-center"><?= Html::encode($this->title) ?></h4>

    <?php $form = ActiveForm::begin([
        'id' => 'password-recovery-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]); ?>

    <?= $form->field($model, 'password')->textInput([
        'template' => '{input}{label}{error}',
        'class' => 'form-control'
    ])->passwordInput()
    ?>


    <?= Html::submitButton(Yii::t('user', 'Finish'), ['class' => 'btn btn-100']) ?><br>

    <?php ActiveForm::end(); ?>






</fieldset>