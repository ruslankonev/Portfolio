<?php
    use yii\helpers\Html;
    use dosamigos\chartjs\ChartJs;

    $this->title = \Yii::t("app", 'Referrals');
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-xs-12">
        <div class="box box-solid">
            <div class="box-body">
                <div class="row">
                    <div class="col-xs-6">
                        <h4 class="header-title"><?= \Yii::t("app", "Referral link") ?></h4>
                        <?= Html::input('text', 'ref_link', $ref_link, ['class' => 'form-control input-lg', 'onclick' => 'this.select();']) ?>
                    </div>
                    <div class="col-xs-6">
                        <h4 class="header-title"><?= \Yii::t("app", "Referral share") ?></h4>
                        
                        <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
                        <script src="//yastatic.net/share2/share.js"></script>
                        <div class="ya-share2" data-services="vkontakte,facebook,odnoklassniki,moimir,gplus,twitter,linkedin,lj,viber,whatsapp,telegram" data-url="<?= $ref_link ?>"></div>
                    </div>
                </div>

                <? if($statistics): ?>

                    <br/>
                    <div class="row">
                        <div class="col-xs-12">
                            <h4 class="header-title"><?= \Yii::t("app", "Referral statistics") ?></h4>

                            <div class="box-body table-responsive no-padding">
                                <?= ChartJs::widget([
                                        'type' => 'line',
                                        'options' => [
                                            'height' => 150,
                                            'width' => 500
                                        ],
                                        'data' => [
                                            'labels' => [
                                                        \Yii::t("app", "January"), 
                                                        \Yii::t("app", "February"),
                                                        \Yii::t("app", "March"),
                                                        \Yii::t("app", "April"),
                                                        \Yii::t("app", "May"),
                                                        \Yii::t("app", "June"),
                                                        \Yii::t("app", "July"),
                                                        \Yii::t("app", "August"),
                                                        \Yii::t("app", "September"),
                                                        \Yii::t("app", "October"),
                                                        \Yii::t("app", "November"),
                                                        \Yii::t("app", "December")
                                            ],
                                            'datasets' => [
                                                [
                                                    'label' => "My First dataset",
                                                    'backgroundColor' => "rgba(179,181,198,0.2)",
                                                    'borderColor' => "rgba(179,181,198,1)",
                                                    'pointBackgroundColor' => "rgba(179,181,198,1)",
                                                    'pointBorderColor' => "#fff",
                                                    'pointHoverBackgroundColor' => "#fff",
                                                    'pointHoverBorderColor' => "rgba(179,181,198,1)",
                                                    'data' => $statistics
                                                ]
                                            ]
                                        ]
                                    ]);
                                ?>

                                <?
                                    /*
                                        $referrer = \dektrium\user\models\User::findIdentity($data->referrer_id);
                                        return ($referrer->username) ? $referrer->username : $referrer->email;
                                   
                                        $referral = \dektrium\user\models\User::findIdentity($data->referral_id);
                                        return ($referral->username) ? $referral->username : $referral->email;
                                    */
                                ?>
                            </div>
                        </div>
                    </div>

                 <? endif; ?>
            </div>
        </div>
    </div>
</div>