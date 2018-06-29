<?php

$vendorDir = dirname(__DIR__);

return array (
  'yiisoft/yii2-swiftmailer' => 
  array (
    'name' => 'yiisoft/yii2-swiftmailer',
    'version' => '2.0.7.0',
    'alias' => 
    array (
      '@yii/swiftmailer' => $vendorDir . '/yiisoft/yii2-swiftmailer',
    ),
  ),
  'yiisoft/yii2-bootstrap' => 
  array (
    'name' => 'yiisoft/yii2-bootstrap',
    'version' => '2.0.8.0',
    'alias' => 
    array (
      '@yii/bootstrap' => $vendorDir . '/yiisoft/yii2-bootstrap/src',
    ),
  ),
  'yiisoft/yii2-debug' => 
  array (
    'name' => 'yiisoft/yii2-debug',
    'version' => '2.0.13.0',
    'alias' => 
    array (
      '@yii/debug' => $vendorDir . '/yiisoft/yii2-debug',
    ),
  ),
  'yiisoft/yii2-gii' => 
  array (
    'name' => 'yiisoft/yii2-gii',
    'version' => '2.0.6.0',
    'alias' => 
    array (
      '@yii/gii' => $vendorDir . '/yiisoft/yii2-gii',
    ),
  ),
  'yiisoft/yii2-faker' => 
  array (
    'name' => 'yiisoft/yii2-faker',
    'version' => '2.0.4.0',
    'alias' => 
    array (
      '@yii/faker' => $vendorDir . '/yiisoft/yii2-faker',
    ),
  ),
  'yiisoft/yii2-httpclient' => 
  array (
    'name' => 'yiisoft/yii2-httpclient',
    'version' => '2.0.6.0',
    'alias' => 
    array (
      '@yii/httpclient' => $vendorDir . '/yiisoft/yii2-httpclient/src',
    ),
  ),
  'yiisoft/yii2-authclient' => 
  array (
    'name' => 'yiisoft/yii2-authclient',
    'version' => '2.1.5.0',
    'alias' => 
    array (
      '@yii/authclient' => $vendorDir . '/yiisoft/yii2-authclient/src',
    ),
  ),
  'dektrium/yii2-user' => 
  array (
    'name' => 'dektrium/yii2-user',
    'version' => '0.9.13.0',
    'alias' => 
    array (
      '@dektrium/user' => $vendorDir . '/dektrium/yii2-user',
    ),
    'bootstrap' => 'dektrium\\user\\Bootstrap',
  ),
  'klisl/yii2-languages' => 
  array (
    'name' => 'klisl/yii2-languages',
    'version' => '2.3.0.0',
    'alias' => 
    array (
      '@klisl/languages' => $vendorDir . '/klisl/yii2-languages/src',
    ),
    'bootstrap' => 'klisl\\languages\\Bootstrap',
  ),
  'yii2tech/csv-grid' => 
  array (
    'name' => 'yii2tech/csv-grid',
    'version' => '1.0.3.0',
    'alias' => 
    array (
      '@yii2tech/csvgrid' => $vendorDir . '/yii2tech/csv-grid/src',
    ),
  ),
  '2amigos/yii2-chartjs-widget' => 
  array (
    'name' => '2amigos/yii2-chartjs-widget',
    'version' => '2.1.2.0',
    'alias' => 
    array (
      '@dosamigos/chartjs' => $vendorDir . '/2amigos/yii2-chartjs-widget/src',
    ),
  ),
);
