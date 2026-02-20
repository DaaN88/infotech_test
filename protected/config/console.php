<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
require_once dirname(__FILE__) . '/../components/Env.php';

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),

    'import'=>array(
        'application.models.*',
        'application.components.*',
        'application.components.notifications.*',
        'application.components.queue.*',
        'application.components.jobs.*',
        'application.components.exceptions.*',
    ),

	// application components
	'components'=>array(

		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		// общий сервис отчетов (можно дернуть из консольных команд)
        'reportService'=>array(
            'class'=>'TopAuthorsReportService',
        ),

        'notificationFactory'=>array(
            'class'=>'NotificationFactory',
            'smsApiKey'=>Env::get('SMS_API_KEY', 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ'),
        ),
        'notifier'=>array(
            'class'=>'Notifier',
        ),
        'notificationService'=>array(
            'class'=>'NotificationService',
        ),
        'queue'=>array(
            'class'=>'QueueComponent',
            'driver'=>Env::get('QUEUE_DRIVER', 'redis'),
            'redisHost'=>Env::get('REDIS_HOST', 'redis'),
            'redisPort'=> (int)(Env::get('REDIS_PORT', 6379)),
        ),
        'bookRepository'=>array(
            'class'=>'BookRepository',
        ),
        'transactionManager'=>array(
            'class'=>'ModelTransactionManager',
        ),

        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ),
                array(
                    'class'=>'CFileLogRoute',
                    'levels'=>'info, error, warning',
                    'categories'=>'sms,queue',
                    'logFile'=>'app-info.log',
                ),
            ),
        ),

    ),
);
