<?php

namespace app\components;

use Yii;
use yii\base\Widget;

class DistributionWidget extends Widget
{
	private $contract;

	public function init()
    {
        parent::init();

        $contract = (\Yii::$app->session->has("contract") ? \Yii::$app->session->get("contract") : new \stdClass());

        if(isset($contract->timestamp) && $contract->timestamp > time())
        {
            $this->contract = \Yii::$app->session->get("contract");
        }
    }

    public function run()
    {
        return $this->render("distribution", [
	        	"contract" => $this->contract,
	        ]);
    }
}