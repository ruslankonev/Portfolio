<div class="token-distribution <?= (!isset($contract)) ? 'processed' : '' ?>">

	<?php if(!isset($contract)): ?>
    	<?= \app\components\LoadingWidget::widget() ?>
    <?php endif; ?>

    <div class="token-distribution__header">
        <h1 class="token-distribution__caption">
            <?= \Yii::t("app", 'GloW Token Distribution') ?>
        </h1>

        <div class="current-period">
            <span class="current">
                <?php if(isset($contract->presale)): ?>
                    presale
                <?php else: ?>
                    mainsale
                <?php endif; ?>
            </span>
        </div>
    </div>

    <div class="token-distribution__center">
        <div class="token-distribution__progress">
            <h3 class="token-distribution__total"><?= \Yii::t("app", 'Total Distributed') ?></h3>

            <div class="progress-block">
                <?= \yii\bootstrap\Progress::widget([
                        'percent' => (isset($contract->progress)) ? $contract->progress : 10,
                        'label' => ((isset($contract->soldTokens)) ? $contract->soldTokens : 0) . " " . \Yii::$app->params["tokenName"]
                ]); ?>
            </div>

            <div class="progress-stats">
                <div>
                    <span class="start">0</span>
                    <?= \Yii::$app->params["tokenName"] ?>
                </div>
                <div>
                    <span class="end">
                        <?= (isset($contract->totalSupply)) ? number_format($contract->totalSupply, 0, '', ' ') : '' ?>
                    </span>
                    <?= \Yii::$app->params["tokenName"] ?>
                </div>
            </div>

            <h3 class="token-distribution__current-caption"><?= \Yii::t("app", 'Current Distributed') ?></h3>
            
            <div class="token-distribution__current distribution-stats">
                <div class="token-distribution__current-value  token-distribution__current-value--1">
                    <span class="tokens-distribution">
                        <?= (isset($contract->soldTokens)) ? $contract->soldTokens : '' ?>
                    </span>

                    <div class="token-name"><?= \Yii::$app->params["tokenName"] ?></div>
                </div>

                <div class="token-distribution__current-value token-distribution__current-value--2">
                    <span class="eth-distribution">
                        <?= (isset($contract->weisRaised)) ? $contract->weisRaised : '' ?>
                    </span>

                    <div class="token-name">ETH</div>
                </div>
            </div>
        </div>
    </div>

    <div class="token-distribution__footer">
        <?= \app\components\TimerWidget::widget() ?>
    </div>
</div>

<?php if(!isset($contract)): ?>

	<script type="text/javascript">
	    /*
			После того, как готов DOM - доступен jQuery
	    */
	    window.addEventListener('load', function()
	    {
	    	var token_block = $(".token-distribution");

		    if(token_block.hasClass("processed"))
		    {
		        getSmartContract();
		    }
	    });

	    /*
		    Получить смарт контракт из API
		*/
		function getSmartContract()
		{
		    var token_block = $(".token-distribution"),
		    	period_current = $(".current-period .current"),
		        progress_bar = $(".progress-block .progress-bar"),
		        progress_end = $(".progress-stats .end"),
		        tokens_distribution = $(".tokens-distribution"),
		        eth_distribution = $(".eth-distribution");

		    $.ajax({
		        url: "/ajax/getsmartcontract",
		        dataType: 'json',
		        success: function(data)
		        {
		            if(data)
		            {
                        if(data.presale)
                        {
                            period_current.html("presale");
                        }
                        else
                        {
                            period_current.html("mainsale");
                        }

                        progress_end.html(divide(data.totalSupply));

                        tokens_distribution.html(data.soldTokens);
                        eth_distribution.html(data.weisRaised);
                        
                        progress_bar.find(".sr-only").html(data.soldTokens + " " + '<?= \Yii::$app->params["tokenName"] ?>');

                        progress_bar.attr("aria-valuenow", data.progress);
                        progress_bar.css("width", data.progress + "%");
		            }
                    else
                    {
                        token_block.html("Smart Сontract server unavailable!");
                    }
		            
		            token_block.removeClass("processed");
		        },
		        errors: function(err)
		        {
		            token_block.removeClass("processed");
		            token_block.html(err);
		            return false;
		        }
		    });
		}

		/*
		    Разделяем тысячные
		*/
		function divide(val)
		{
		    return String(val).replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, '$1 ');
		}
	</script>

<?php endif; ?>