<?php

$paramsDb = [
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=localhost;dbname=postim_db',
    'username' => 'postim_user',
    'password' => 'mc2447382@',
    'charset' => 'utf8',
    'enableSchemaCache'=>true,
    'schemaCacheDuration' => 1000,
    'schemaCache' => 'cache',
];

if(YII_ENV == 'prod'){
    $params['dsn'] = 'pgsql:host=docker49872-postimdb.mycloud.by;dbname=postim_db';
}

return $paramsDb;