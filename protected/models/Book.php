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
            ['isbn', 'unique'],
            ['cover_path', 'length', 'max' => 255],
            ['year', 'numerical', 'integerOnly' => true],
            ['description', 'safe'],
        ];
    }

    /**
     * Автоматически ставим created_at / updated_at.
     */
    protected function beforeSave()
    {
        $now = date('Y-m-d H:i:s');
        if ($this->getIsNewRecord() && empty($this->created_at)) {
            $this->created_at = $now;
        }
        $this->updated_at = $now;

        return parent::beforeSave();
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

    /**
     * Сохраняет связи книга-авторы (M2M) в таблице book_author.
     *
     * @param int[] $authorIds
     */
    public function syncAuthors( $authorIds): void
    {
        $authorIds = array_values(array_unique(array_filter(array_map('intval', $authorIds))));

        // Удаляем текущие связи и пишем новые; оборачивайте транзакцией снаружи.
        Yii::app()->db->createCommand()
            ->delete('book_author', 'book_id = :id', [':id' => $this->id]);

        foreach ($authorIds as $aid) {
            Yii::app()->db->createCommand()->insert('book_author', [
                'book_id' => $this->id,
                'author_id' => $aid,
            ]);
        }

        // Обновим кеш relations.
        $this->authors = $authorIds ? Author::model()->findAllByPk($authorIds) : [];
    }
}
