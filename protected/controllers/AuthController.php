<?php

declare(strict_types=1);

class AuthController extends Controller
{
    public $defaultAction = 'login';

    public function actionLogin(): void
    {
        if (! Yii::app()->user->isGuest) {
            $this->redirect(Yii::app()->homeUrl);
        }

        $model = new LoginForm;

        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];

            if ($model->validate() && $model->login()) {
                // если returnUrl не задан, идём на каталог
                if (
                    Yii::app()->user->returnUrl === '/'
                    || Yii::app()->user->returnUrl === Yii::app()->request->scriptUrl
                ) {
                    Yii::app()->user->setReturnUrl(Yii::app()->homeUrl);
                }

                $this->redirect(Yii::app()->homeUrl);
            }
        }

        $this->render('login', array('model' => $model));
    }
}
