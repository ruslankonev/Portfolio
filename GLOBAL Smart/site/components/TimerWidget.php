<?php

namespace app\components;

use Yii;
use yii\base\Widget;

class TimerWidget extends Widget
{
	private $countdown = "2018-06-15"; // Y-m-d - Дата конца обратного отсчёта

	public function init()
    {
        parent::init();

        if(strtotime($this->countdown) <= time())
        {
        	$this->countdown = null;
        }
    }

    public function run()
    {
        return $this->render('timer', [
        	"countdown" => $this->countdown,
        ]);
    }
}