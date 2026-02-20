<?php

declare(strict_types=1);

/**
 * BookPhoto model.
 *
 * @property int $id
 * @property int $book_id
 * @property string $file_name
 * @property string $display_name
 * @property string $created_at
 *
 * @property Book $book
 */
class BookPhoto extends CActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 'book_photos';
    }

    public function relations()
    {
        return [
            'book' => [self::BELONGS_TO, 'Book', 'book_id'],
        ];
    }

    public function rules()
    {
        return [
            ['book_id, file_name, display_name', 'required'],
            ['book_id', 'numerical', 'integerOnly' => true],
            ['file_name, display_name', 'length', 'max' => 255],
        ];
    }
}
