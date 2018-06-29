<?php

namespace app\components;

use Yii;
use yii\base\Widget;

class InvestmentsWidget extends Widget
{
	private $model;
    private $currencies;

	public function init()
    {
        parent::init();

        $this->model = new \app\models\Transactions();
        $this->currencies = \app\models\Currencies::find()
                                                    ->select(['`id`, `name`, `code`'])
                                                    ->where(['enabled' => 1])
                                                    ->all();
    }

    public function run()
    {
        return $this->render("investments", [
                "model" => $this->model,
	        	"currencies" => $this->currencies,
	        ]);
    }
}