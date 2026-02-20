<?php

declare(strict_types=1);

class SubscriptionController extends Controller
{
    public function filters(): array
    {
        return [
            'accessControl',
        ];
    }

    public function accessRules(): array
    {
        return [
            ['allow', 'users' => ['*']],
            ['deny', 'users' => ['?'], 'actions' => []],
        ];
    }

    /**
     * @throws CHttpException
     */
    public function actionCreate(): void
    {
        $authorId = (int) Yii::app()->request->getParam('author');
        $bookId = (int) Yii::app()->request->getParam('book');

        $allowedAuthors = [];
        $book = null;

        if ($bookId) {
            $book = Book::model()->with('authors')->findByPk($bookId);

            if (! $book) {
                throw new CHttpException(404, Yii::t('app', 'subscription.book.not_found'));
            }

            $allowedAuthors = array_values($book->authors ?: []);
        }

        if ($authorId) {
            $author = Author::model()->findByPk($authorId);
            if (! $author) {
                throw new CHttpException(404, Yii::t('app', 'subscription.author.not_found'));
            }
            $allowedAuthors = [$author];
        }

        $allAuthors = Author::model()->findAll(['order' => 'name ASC']);

        // Если параметров нет или не найдено авторов для книги — даём выбрать любого автора из базы.
        if (! $allowedAuthors) {
            $allowedAuthors = $allAuthors;
        }

        $authorOptions = CHtml::listData($allAuthors, 'id', 'name');
        $allowedIds = array_values(array_map('intval', array_keys($authorOptions)));
        $model = new Subscription();
        $model->created_at = date('Y-m-d H:i:s');

        // Если автор для контекста известен — используем первого в списке.
        if (! empty($allowedAuthors)) {
            $first = reset($allowedAuthors);

            if ($first) {
                $model->author_id = (int) $first->id;
            }
        }

        if (isset($_POST['Subscription'])) {
            $model->attributes = $_POST['Subscription'];

            if (! in_array((int) $model->author_id, $allowedIds, true)) {
                $model->addError('author_id', Yii::t('app', 'subscription.author.choose'));
            }

            if ($model->save()) {
                Yii::app()->user->setFlash('success', Yii::t('app', 'subscription.flash.created'));

                $this->redirect(['/book/index']);

                return;
            }
        }

        $this->render('create', [
            'model' => $model,
            'author' => count($allowedAuthors) === 1 ? reset($allowedAuthors) : null,
            'authorOptions' => $authorOptions,
            'book' => $book,
        ]);
    }
}
