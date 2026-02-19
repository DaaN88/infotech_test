<?php

declare(strict_types=1);

/**
 * Book model.
 */
class Book extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'books';
    }

    public function relations()
    {
        return array(
            'authors' => array(self::MANY_MANY, 'Author', 'book_author(book_id, author_id)'),
        );
    }

    public function rules()
    {
        return array(
            array('title, year, isbn', 'required'),
            array('title', 'length', 'max' => 255),
            array('isbn', 'length', 'max' => 32),
            array('cover_path', 'length', 'max' => 255),
            array('year', 'numerical', 'integerOnly' => true),
        );
    }
}
