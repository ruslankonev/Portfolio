<?php

	namespace app\components;

	use Yii;
	use yii\base\Widget;

	class LoadingWidget extends Widget
	{
		public function init()
	    {
	        parent::init();
	    }

	    public function run()
	    {
	        return $this->render('loading');
	    }
	}