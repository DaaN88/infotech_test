<?php

declare(strict_types=1);

/**
 * Author model.
 */
class Author extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'authors';
    }

    public function relations()
    {
        return array(
            'books' => array(self::MANY_MANY, 'Book', 'book_author(author_id, book_id)'),
        );
    }
}
