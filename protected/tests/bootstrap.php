<?php

$_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_NAME'] ?? '/index-test.php';
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../vendor/yiisoft/yii/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);

Yii::setPathOfAlias('application', dirname(__FILE__) . '/..');
Yii::setPathOfAlias('webroot', dirname(__FILE__) . '/../../');
Yii::setPathOfAlias('application.tests', dirname(__FILE__));
Yii::setPathOfAlias('application.tests.fixtures', dirname(__FILE__) . '/fixtures');
Yii::setPathOfAlias('application.tests.components', dirname(__FILE__) . '/components');
Yii::setPathOfAlias('application.tests.helpers', dirname(__FILE__) . '/helpers');

Yii::import('application.tests.components.*');
Yii::import('application.tests.helpers.*');

if (!class_exists('PHP_Invoker', false)) {
	class PHP_Invoker {}
}

if (!class_exists('PHPUnit_Extensions_Database_TestCase', false)) {
	class PHPUnit_Extensions_Database_TestCase extends PHPUnit_Framework_TestCase {}
}

Yii::createWebApplication($config);
