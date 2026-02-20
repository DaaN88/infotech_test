<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=[];
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=[];

    /**
     * Ограничиваем действие только AJAX-запросами.
     */
    protected function requireAjax(): void
    {
        if (! Yii::app()->request->isAjaxRequest) {
            throw new CHttpException(400, Yii::t('app', 'controller.ajax_only'));
        }
    }

    /**
     * Быстрый ответ JSON и завершение запроса.
     */
    protected function renderJson( $payload, int $statusCode = 200): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
            header('Content-Type: application/json; charset=utf-8');
        }
        echo CJSON::encode($payload);
        if (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING) {
            return;
        }
        Yii::app()->end();
    }
}
