<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

function pre($v, $var_dump = null)
{
	print_r("<pre>");

	if($var_dump)
	{
		var_dump($v);
	}
	else
	{
		print_r($v);
	}

	print_r("</pre>");
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();