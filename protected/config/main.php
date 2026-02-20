<?php

declare(strict_types=1);

require_once dirname(__FILE__) . '/../components/Env.php';

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return [
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Каталог книг',
    'sourceLanguage'=>'en',
    'language'=>'ru',
	'defaultController'=>'auth',
	'homeUrl'=>['/book/index'],

	// preloading 'log' component
	'preload'=>['log'],

	// autoloading model and component classes
    'import'=>[
        'application.models.*',
        'application.components.*',
        'application.components.notifications.*',
        'application.components.queue.*',
        'application.components.queue.SyncInMemoryQueue',
        'application.components.jobs.*',
        'application.components.exceptions.*',
    ],

	'modules'=>[
		// uncomment the following to enable the Gii tool
		/*
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'Enter Your Password Here',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		*/
	],

	// application components
	'components'=>[

		'user'=>[
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'loginUrl'=>['/auth/login'],
		],

		'authManager'=>[
			'class'=>'CDbAuthManager',
			'connectionID'=>'db',
			'defaultRoles'=>['guest'],
		],

		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/

		// database settings are configured in database.php
		'db'=>require(dirname(__FILE__).'/database.php'),

		'errorHandler'=>[
			// use 'site/error' action to display errors
			'errorAction'=>YII_DEBUG ? null : 'site/error',
		],

        'log'=>[
            'class'=>'CLogRouter',
            'routes'=>[
                [
                    'class'=>'CFileLogRoute',
                    'levels'=>'error, warning',
                ],
                [
                    'class'=>'CFileLogRoute',
                    'levels'=>'info, error, warning',
                    'categories'=>'sms,queue',
                    'logFile'=>'app-info.log',
                ],
                // uncomment the following to show log messages on web pages
                /*
                array(
                    'class'=>'CWebLogRoute',
                ),
				*/
			],
		],

		// Отчёты/статистика
        'reportService'=>[
            'class'=>'TopAuthorsReportService',
        ],

        // Уведомления/очередь
        'notificationFactory'=>[
            'class'=>'NotificationFactory',
            'smsApiKey'=>Env::get('SMS_API_KEY', 'XXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZXXXXXXXXXXXXYYYYYYYYYYYYZZZZZZZZ'),
        ],
        'notifier'=>[
            'class'=>'Notifier',
        ],
        'notificationService'=>[
            'class'=>'NotificationService',
        ],
        'queue'=>[
            'class'=>'QueueComponent',
            'driver'=>Env::get('QUEUE_DRIVER', 'redis'),
            'redisHost'=>Env::get('REDIS_HOST', 'redis'),
            'redisPort'=> (int)(Env::get('REDIS_PORT', 6379)),
        ],
        'bookRepository'=>[
            'class'=>'BookRepository',
        ],
        'transactionManager'=>[
            'class'=>'ModelTransactionManager',
        ],

        // Cache is required for rate limiting.
        'cache'=>[
            'class'=>'CFileCache',
        ],

    ],

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>[
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	],
];
