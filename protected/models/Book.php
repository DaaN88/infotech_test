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
        return [
            'authors' => [self::MANY_MANY, 'Author', 'book_author(book_id, author_id)'],
            'photos' => [self::HAS_MANY, 'BookPhoto', 'book_id', 'order' => 'photos.id ASC'],
        ];
    }

    public function rules()
    {
        return [
            ['title, year, isbn', 'required'],
            ['title', 'length', 'max' => 255],
            ['isbn', 'length', 'max' => 32],
            ['cover_path', 'length', 'max' => 255],
            ['year', 'numerical', 'integerOnly' => true],
        ];
    }

    /**
     * Returns first photo for the book or null if missing.
     */
    public function getPrimaryPhoto(): ?BookPhoto
    {
        if ($this->photos && isset($this->photos[0])) {
            return $this->photos[0];
        }

        return null;
    }

    protected function afterSave()
    {
        $isInsert = $this->isNewRecord;
        parent::afterSave();

        // Уведомляем подписчиков только при создании новой книги.
        if ($isInsert) {
            /** @var NotificationService $notificationService */
            $notificationService = Yii::app()->notificationService;
            $notificationService->enqueueNewBook($this);
        }
    }
}
