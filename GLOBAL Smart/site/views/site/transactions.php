<?php
    use yii\helpers\Html;
    use yii\grid\GridView;

    $this->title = \Yii::t("app",'Transactions');
    $this->params['breadcrumbs'][] = $this->title;
?>

<div class="transactions-page">
    <div class="row">
        <div class="col-xs-12">
            <div class="box box-solid">
                <h4 class="header-title"><?= \Yii::t("app","Table with the last transaction") ?></h4>

                <? if($provider): ?>

                    <div class="box-body table-responsive no-padding">
                        <?= GridView::widget([
                            'dataProvider' => $provider,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                [
                                    'attribute' => 'user_id',
                                    'content' => function($data)
                                    {
                                        $user = \dektrium\user\models\User::findIdentity($data->user_id);
                                        return ($user->username) ? $user->username : $user->email;
                                    }
                                ],
                                [
                                    'attribute' => 'currency_id',
                                    'content'=>function($data)
                                    {
                                        $currency = \app\models\Currencies::findCurrency($data->currency_id);
                                        return ($currency->code) ? $currency->code : $currency->name;
                                    }
                                ],
                                'investments',
                                [
                                    'attribute' => 'tokens',
                                    'content' => function($data){
                                        return round($data->tokens, 3);
                                    }
                                ],
                                [
                                    'attribute' => 'txhash',
                                    'content'=>function($data){
                                        $currency = \app\models\Currencies::findCurrency($data->currency_id);
                                        return "<a target='_blank' href='".$currency->platform."/tx/".$data->txhash."'>".$data->txhash."</a>";
                                    }
                                ],
                                [
                                    'attribute' => 'updatedate',
                                    'content'=>function($data){
                                        return date("Y-m-d H:i:s", $data->updatedate);
                                    }
                                ],
                                [
                                    'attribute' => 'status',
                                    'content' => function($data){
                                        return \app\models\Transactions::getStatusView($data->status);
                                    }
                                ]
                            ],
                          ]);
                        ?>
                    </div>

                    <p>
                        <svg class="svg-icon  svg-icon--danger">
                            <use xlink:href="/assets/svg-sprites/i-sprite.svg#i-danger-no-opt"></use>
                        </svg>
                        <?= \Yii::t("app", 'The number of tokens may change due to the difference in exchange rates') ?>
                    </p>

                    <!-- 
                        <p><?= Html::a(\Yii::t("app", "Export"), ['/ajax/csvexport?action=export'], ['class' => 'csv-export', 'target' => "_blank"]) ?></p> 
                    -->

                <? else: ?>
                    <p><?= \Yii::t("app", "There are no transactions") ?></p>
                <? endif; ?>
            </div>
        </div>
    </div>
</div>