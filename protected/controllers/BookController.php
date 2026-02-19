<?php

declare(strict_types=1);

class BookController extends Controller
{
    public function actionIndex()
    {
        $criteria = new CDbCriteria();
        $criteria->with = array('authors');
        $criteria->order = 't.updated_at DESC';

        $dataProvider = new CActiveDataProvider('Book', array(
            'criteria' => $criteria,
            'pagination' => array('pageSize' => 12),
        ));

        $this->render('index', array(
            'dataProvider' => $dataProvider,
        ));
    }
}
