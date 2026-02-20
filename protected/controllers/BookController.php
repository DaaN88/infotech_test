<?php

declare(strict_types=1);

class BookController extends Controller
{
    public function filters(): array
    {
        return ['accessControl'];
    }

    public function accessRules(): array
    {
        return [
            ['allow', 'actions' => ['index'], 'users' => ['*']],
            ['allow', 'actions' => ['create', 'update', 'delete', 'load'], 'users' => ['@']],
            ['deny', 'users' => ['*']],
        ];
    }

    protected function beforeAction($action): bool
    {
        // Для AJAX-запросов возвращаем JSON 401 вместо редиректа на логин.
        if (Yii::app()->user->isGuest && Yii::app()->request->isAjaxRequest && $action->id !== 'index') {
            $this->renderJson(
                [
                    'success' => false,
                    'message' => Yii::t('app', 'book.auth.required')
                ],
                401
            );

            return false;
        }

        return parent::beforeAction($action);
    }

    public function actionIndex(): void
    {
        $criteria = new CDbCriteria();
        $criteria->with = ['authors'];
        $criteria->order = 't.updated_at DESC, t.id ASC';

        $dataProvider = new CActiveDataProvider(
            'Book',
            [
                'criteria' => $criteria,
                'pagination' => ['pageSize' => 12],
            ]
        );

        $this->render('index', [
            'dataProvider' => $dataProvider,
            'authors' => Author::model()->findAll(['order' => 'name ASC']),
        ]);
    }

    /**
     * @throws CException
     * @throws CHttpException
     */
    public function actionCreate(): void
    {
        $this->requireAjax();

        try {
            $book = Yii::app()->bookRepository->create(
                $_POST['Book'] ?? [],
                $_POST['authors'] ?? [],
                CUploadedFile::getInstanceByName('cover')
            );

            $this->renderJson([
                'success' => true,
                'row' => $this->renderPartial('_row', ['book' => $book], true),
                'id' => $book->id,
            ]);
        } catch (ValidationException $e) {
            $this->renderJson([
                'success' => false,
                'errors' => $e->getErrors(),
                'message' => $e->getMessage(),
            ]);
        } catch (NotFoundException $e) {
            $this->renderJson(['success' => false, 'message' => $e->getMessage()]);
        } catch (RuntimeException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

            $this->renderJson(['success' => false, 'message' => Yii::t('app', 'book.save.error')]);
        }
    }

    /**
     * @throws CException
     * @throws CHttpException
     */
    public function actionUpdate(int $id): void
    {
        $this->requireAjax();

        try {
            $book = Yii::app()->bookRepository->update(
                $id,
                $_POST['Book'] ?? [],
                $_POST['authors'] ?? [],
                CUploadedFile::getInstanceByName('cover')
            );

            $this->renderJson([
                'success' => true,
                'row' => $this->renderPartial('_row', ['book' => $book], true),
                'id' => $book->id,
            ]);
        } catch (ValidationException $e) {
            $this->renderJson([
                'success' => false,
                'errors' => $e->getErrors(),
                'message' => $e->getMessage(),
            ]);
        } catch (NotFoundException $e) {
            $this->renderJson(['success' => false, 'message' => $e->getMessage()]);
        } catch (RuntimeException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);

            $this->renderJson(['success' => false, 'message' => Yii::t('app', 'book.update.error')]);
        }
    }

    /**
     * @throws CHttpException
     */
    public function actionDelete(int $id): void
    {
        $this->requireAjax();

        try {
            Yii::app()->bookRepository->delete($id);
            $this->renderJson(['success' => true, 'id' => $id]);
        } catch (NotFoundException $e) {
            $this->renderJson(['success' => false, 'message' => $e->getMessage()]);
        } catch (RuntimeException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR);
            $this->renderJson(['success' => false, 'message' => Yii::t('app', 'book.delete.error')]);
        }
    }

    /**
     * @throws CHttpException
     */
    public function actionLoad(int $id): void
    {
        $this->requireAjax();

        try {
            $book = Yii::app()->bookRepository->load($id);

            $this->renderJson([
                'success' => true,
                'book' => [
                    'id' => $book->id,
                    'title' => $book->title,
                    'year' => (int) $book->year,
                    'isbn' => $book->isbn,
                    'description' => $book->description,
                    'authors' => array_map('intval', CHtml::listData($book->authors, 'id', 'id')),
                    'photo' => $book->primaryPhoto ? $book->primaryPhoto->file_name : null,
                ],
            ]);
        } catch (NotFoundException $e) {
            $this->renderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

}
