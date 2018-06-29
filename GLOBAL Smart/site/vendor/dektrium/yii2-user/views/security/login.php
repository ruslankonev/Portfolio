<?php
    /*
     * This file is part of the Dektrium project.
     *
     * (c) Dektrium project <http://github.com/dektrium>
     *
     * For the full copyright and license information, please view the LICENSE.md
     * file that was distributed with this source code.
     */

    use dektrium\user\widgets\Connect;
    use dektrium\user\models\LoginForm;
    use yii\helpers\Html;
    use yii\widgets\ActiveForm;

    /**
     * @var yii\web\View $this
     * @var dektrium\user\models\LoginForm $model
     * @var dektrium\user\Module $module
     */

    $this->title = Yii::t('user', 'Sign in');
    $this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('/_alert', ['module' => Yii::$app->getModule('user')]) ?>

<div class="account-content">
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        'validateOnChange' => false,
    ]) ?>

     <?= Html::a('<object class="object-svg" data="/assets/svg/content/global-wasp-logo.svg" type="image/svg+xml"></object>',
        Yii::$app->homeUrl,
        ["class" => "login-logo"]
    ) ?>

    <fieldset class="login-form__fieldset">
        <legend class="token-buy__caption"></legend>

        <?php if ($module->debug): ?>
            <?= $form->field($model, 'login', [
                'inputOptions' => [
                    'autofocus' => 'autofocus',
                    'class' => 'form-control',
                    'tabindex' => '1']])->dropDownList(LoginForm::loginList());
            ?>

        <?php else: ?>

            <?= $form->field($model, 'login')
                ->textInput([
                    'template' => '{input}{label}{error}',
                    'class' => 'form-control',
                    'tabindex' => '1'
                ])
                ->label(\Yii::t("user", "Login"), ['class' => '']);
            ?>

        <?php endif ?>

        <?php if ($module->debug): ?>

            <div class="alert alert-warning">
                <?= Yii::t('user', 'Password is not necessary because the module is in DEBUG mode.'); ?>
            </div>

        <?php else: ?>

            <?= $form->field($model, 'password')
                ->textInput([
                    'template' => '{input}{label}{error}',
                    'tabindex' => '2'
                ])
                ->passwordInput([
                    'class' => 'form-control'
                ])
                ->label(Yii::t('user', 'Password'), ['class' => '']);
            ?>
            
        <?php endif ?>

        <div class="row pt-30">
            <div class="col-xs-6 no-col-mobile">
                <?= $form->field($model, 'rememberMe', [
                    'options' => [ 'class' => 'form-group form-material switch floating '],
                    'template' => "<div class='checkbox checkbox-custom'>{input}{label}{error}</div>",
                ])->checkbox([ 'class' => '',],false) ?>
            </div>
            <div class="col-xs-6 no-col-mobile">
                <div class="form-group form-material  floating text-right">
                    <?= Html::a(Yii::t('user', 'Forgot password?'), ['/user/recovery/request'], ['tabindex' => '5']) ?>
                </div>

            </div>
        </div>
        <br/>

        <?= Html::submitButton(Yii::t('user', 'Sign in'), ['class' => 'btn btn-100', 'tabindex' => '4']) ?>

    </fieldset>

    <?php ActiveForm::end(); ?>
</div>

<div class="social-block">
    <?php if ($module->enableConfirmation): ?>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Didn\'t receive confirmation message?'), ['/user/registration/resend']) ?>
        </p>
    <?php endif ?>
    <?php if ($module->enableRegistration): ?>
        <p class="text-center">
            <?= Html::a(Yii::t('user', 'Don\'t have an account? Sign up!'), ['/user/registration/register','ref' => Yii::$app->request->get("ref")]) ?>
        </p>
    <?php endif ?>

    <?= Connect::widget([
        'baseAuthUrl' => ['/user/security/auth'],
    ]) ?>
</div>