<aside class="sidebar-navigation">
    <div class="scrollbar-wrapper">
        <div>
            <button type="button" class="button-menu-mobile btn-mobile-view visible-xs visible-sm">
                <i class="mdi mdi-close"></i>
            </button>

            <div class="user-details active" style="padding-bottom: 0;">

                <?php
                    $id = Yii::$app->user->id;
                    $profile = \dektrium\user\models\Profile::find()->where(['user_id' => $id])->one();
                ?>

                <div class="header__ballance">
                    <?php
                        $profile = \dektrium\user\models\Profile::find()->where(['user_id' => \Yii::$app->user->id])->one();
                        $balance = round($profile->balance + $profile->bonus + $profile->ref_bonus, 2);
                    ?>

                    <p class="header__ballance-text"><?= \Yii::t("app", "Balance") ?></p>
                    <p class="header__ballance-value"><?= $balance ?> <?= \Yii::$app->params["tokenName"] ?></p>
                </div>
            </div>

            <?php echo yii\widgets\Menu::widget([
                'items' => [
                    ['label' => \Yii::t("app","Dashboard"),'template' => '<a href="{url}" ><i class="ti-home"></i>{label}</a>', 'url' => ['/site/index']],
                    ['label' => \Yii::t("app",'Instructions'), 'template' => '<a href="{url}" ><i class="ti-help-alt"></i>{label}</a>', 'url' => ['/site/instuction']],
                    ['label' => \Yii::t("app",'Documents'), 'template' => '<a href="{url}" > <i class="ti-files"></i>{label}</a>', 'url' => ['/site/docs']],
                    ['label' => \Yii::t("app",'Profile'), 'template' => '<a href="{url}" > <i class="ti-user"></i>{label}</a>','url' => ['/user/settings/account']],
                    ['label' => \Yii::t("app",'Transactions'),'template' => '<a href="{url}" > <i class="ti-money"></i>{label}</a>', 'url' => ['/site/transactions']],
                    ['label' => \Yii::t("app",'KYC'), 'template' => '<a href="{url}" > <i class="ti-check-box"></i>{label}</a>', 'url' => ['/site/kyc']],
                    /*
                    ['label' => \Yii::t("app",'Referrals'), 'template' => '<a href="{url}" > <i class="ti-direction-alt"></i>{label}</a>', 'url' => ['/site/referrals']],
                    */
                    ['label' => \Yii::t("user",'Logout'), 'template' => '<a href="{url}" data-method="post"> <i class="mdi mdi-power"></i>{label}</a>', 'url' => ['//user/security/logout']],
                ],
                'options' => ['class' => 'metisMenu nav','id'=>'side-menu'],
                'activeCssClass'=>'active'
            ]);?>

        </div>
    </div>
</aside>



