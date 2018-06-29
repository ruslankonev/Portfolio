<?php
    use yii\widgets\Breadcrumbs;
    use app\widgets\Alert;
?>

<div id="page-right-content">
    <div class="container">
        <?php if ($this->title !== nuLL):  ?>
            <div class="col-sm-12">
                <div class="header__current-page">
                    <?php if ($this->title !== nuLL):  ?>
                        <p class="header__current-page-text">
                            <?= \yii\helpers\Html::encode($this->title) ?>
                        </p>
                    <?php endif; ?>

                    <?php
                        $user = \dektrium\user\models\User::findIdentity(\Yii::$app->user->id);
                    ?>
                    <p class="header__current-page-username">
                        <?= \Yii::t("app", "Welcome") ?>, <?= ($user->username) ? $user->username : $user->email ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        <div class="clearfix"></div>

        <div class="col-sm-12">
            <div class="m-b-20 m-b-20">
                <?= Alert::widget() ?>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="m-b-20 m-b-20">
                <?= $content ?>
            </div>
        </div>
    </div>
    <!-- end container -->

    <div class="footer">
        <div class="pull-right -xs">
            <?php
                #\Yii::t("app","Support is available in chat: <a href='https://t.me/evolive' target='_blank'>https://t.me/evolive</a>");
            ?>
        </div>
    </div> <!-- end footer -->

    <div class="col-sm-12">
        <div class="m-b-20 m-b-20">
            <?php $jscode = \app\components\SAWidget::widget();
                $this->registerJs($jscode);
            ?>
        </div>
    </div>
</div>