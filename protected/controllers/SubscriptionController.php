<?php

declare(strict_types=1);

class SubscriptionController extends Controller
{
    public function filters()
    {
        return [
            'accessControl',
        ];
    }

    public function accessRules()
    {
        return [
            ['allow', 'users' => ['*']],
            ['deny', 'users' => ['?'], 'actions' => []],
        ];
    }

    public function actionCreate(): void
    {
        $authorId = (int) Yii::app()->request->getParam('author');

        $author = Author::model()->findByPk($authorId);
        if (! $author) {
            throw new CHttpException(404, 'Автор не найден');
        }

        $model = new Subscription();
        $model->author_id = $authorId;
        $model->created_at = date('Y-m-d H:i:s');

        if (isset($_POST['Subscription'])) {
            $model->attributes = $_POST['Subscription'];
            $model->author_id = $authorId; // защищаем author_id от подмены

            if ($model->save()) {
                Yii::app()->user->setFlash('success', 'Подписка оформлена. Мы сообщим о новых книгах автора.');
                $this->redirect(array('/book/index'));
                return;
            }
        }

        $this->render('create', [
            'model' => $model,
            'author' => $author,
        ]);
    }
}
