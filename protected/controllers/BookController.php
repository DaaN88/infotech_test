<?php

declare(strict_types=1);

class BookController extends Controller
{
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
        ]);
    }
}
