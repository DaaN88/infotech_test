<?php

declare(strict_types=1);

/**
 * Исключение валидации с передачей ошибок модели.
 */
class ValidationException extends RuntimeException
{
    private array $errors;

    public function __construct(string $message, array $errors = array(), int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

/**
 * Исключение «не найдено».
 */
class NotFoundException extends RuntimeException
{
}

/**
 * Сервис работы с книгами: бизнес-логика и операции с БД.
 */
class BookService extends CApplicationComponent
{
    /**
     * Создание книги.
     */
    public function create(array $bookData, array $authorIds, ?CUploadedFile $cover): Book
    {
        $authorIds = $this->normalizeAuthors($authorIds);
        $uploadedCover = null;

        $tx = Yii::app()->db->beginTransaction();
        try {
            $book = new Book();
            $book->attributes = $bookData;

            if ($cover) {
                $uploadedCover = $this->storeCover($cover);
                $book->cover_path = $uploadedCover['file_name'];
            }

            if (! $book->save()) {
                throw new ValidationException('Исправьте ошибки в форме.', $book->getErrors());
            }

            $book->syncAuthors($authorIds);

            if ($uploadedCover) {
                $this->createBookPhoto($book, $uploadedCover);
            }

            Yii::app()->notificationService->enqueueNewBook($book);

            $tx->commit();
            return $book;
        } catch (ValidationException $e) {
            $tx->rollback();
            $this->cleanupUploadedFile($uploadedCover);
            throw $e;
        } catch (Throwable $e) {
            $tx->rollback();
            $this->cleanupUploadedFile($uploadedCover);
            throw new RuntimeException('Не удалось сохранить книгу.', 0, $e);
        }
    }

    /**
     * Обновление книги.
     */
    public function update(int $id, array $bookData, array $authorIds, ?CUploadedFile $cover): Book
    {
        /** @var Book|null $book */
        $book = Book::model()->findByPk($id);
        if (! $book) {
            throw new NotFoundException('Данной книги нет.');
        }

        $oldAuthors = array_map('intval', CHtml::listData($book->authors, 'id', 'id'));
        $authorIds = $this->normalizeAuthors($authorIds);

        $uploadedCover = null;
        $tx = Yii::app()->db->beginTransaction();

        try {
            $book->attributes = $bookData;

            if ($cover) {
                $uploadedCover = $this->storeCover($cover);
                $book->cover_path = $uploadedCover['file_name'];
            }

            if (! $book->save()) {
                throw new ValidationException('Исправьте ошибки в форме.', $book->getErrors());
            }

            $book->syncAuthors($authorIds);

            if ($uploadedCover) {
                $this->createBookPhoto($book, $uploadedCover);
            }

            $this->cleanupSubscriptionsIfAuthorsRemoved($oldAuthors, $authorIds);

            $tx->commit();
            return $book;
        } catch (ValidationException $e) {
            $tx->rollback();
            $this->cleanupUploadedFile($uploadedCover);
            throw $e;
        } catch (Throwable $e) {
            $tx->rollback();
            $this->cleanupUploadedFile($uploadedCover);
            throw new RuntimeException('Не удалось обновить книгу.', 0, $e);
        }
    }

    /**
     * Удаление книги.
     */
    public function delete(int $id): void
    {
        /** @var Book|null $book */
        $book = Book::model()->findByPk($id);
        if (! $book) {
            throw new NotFoundException('Данной книги нет.');
        }

        $authors = array_map('intval', CHtml::listData($book->authors, 'id', 'id'));

        $tx = Yii::app()->db->beginTransaction();
        try {
            $book->delete();
            $this->cleanupSubscriptions($authors);
            $tx->commit();
        } catch (Throwable $e) {
            $tx->rollback();
            throw new RuntimeException('Не удалось удалить книгу.', 0, $e);
        }
    }

    /**
     * Загрузка книги с авторами/фото.
     */
    public function load(int $id): Book
    {
        /** @var Book|null $book */
        $book = Book::model()->with('authors', 'photos')->findByPk($id);
        if (! $book) {
            throw new NotFoundException('Данной книги нет.');
        }
        return $book;
    }

    /**
     * Подготовка списка авторов (валидируем наличие).
     *
     * @param mixed[] $authorIds
     * @return int[]
     */
    protected function normalizeAuthors(array $authorIds): array
    {
        $authorIds = array_values(array_unique(array_filter(array_map('intval', $authorIds))));
        if (! $authorIds) {
            throw new ValidationException('Укажите хотя бы одного автора.', ['authors' => ['Укажите хотя бы одного автора.']]);
        }
        return $authorIds;
    }

    /**
     * Сохраняет файл обложки на диск.
     */
    protected function storeCover(CUploadedFile $file): array
    {
        $ext = $file->getExtensionName();
        $safeName = uniqid('cover_', true) . ($ext ? '.' . $ext : '');

        $targetDir = Yii::getPathOfAlias('webroot') . '/images';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (! $file->saveAs($targetDir . '/' . $safeName)) {
            throw new RuntimeException('Не удалось сохранить файл обложки.');
        }

        return [
            'file_name' => $safeName,
            'display_name' => $file->getName(),
        ];
    }

    /**
     * Создаёт запись о фото книги.
     */
    protected function createBookPhoto(Book $book, array $uploadedCover): void
    {
        $photo = new BookPhoto();
        $photo->book_id = $book->id;
        $photo->file_name = $uploadedCover['file_name'];
        $photo->display_name = $uploadedCover['display_name'];
        $photo->created_at = date('Y-m-d H:i:s');
        $photo->save();

        // обновим кеш relation, чтобы новое фото было доступно сразу
        $currentPhotos = $book->photos ?: array();
        $currentPhotos[] = $photo;
        $book->photos = $currentPhotos;
    }

    /**
     * Очищает подписки для авторов, у которых не осталось книг.
     *
     * @param int[] $authorIds
     */
    protected function cleanupSubscriptions(array $authorIds): void
    {
        if (! $authorIds) {
            return;
        }

        foreach ($authorIds as $aid) {
            $booksLeft = Yii::app()->db->createCommand()
                ->select('COUNT(*)')
                ->from('book_author')
                ->where('author_id = :a', [':a' => $aid])
                ->queryScalar();

            if ((int) $booksLeft === 0) {
                Subscription::model()->deleteAllByAttributes(['author_id' => $aid]);
            }
        }
    }

    /**
     * Чистит подписки для авторов, которые были убраны из книги.
     *
     * @param int[] $oldAuthors
     * @param int[] $newAuthors
     */
    protected function cleanupSubscriptionsIfAuthorsRemoved(array $oldAuthors, array $newAuthors): void
    {
        $removed = array_diff($oldAuthors, $newAuthors);
        $this->cleanupSubscriptions($removed);
    }

    /**
     * Удаляет файл обложки, если операция не состоялась.
     */
    protected function cleanupUploadedFile(?array $uploadedCover): void
    {
        if (! $uploadedCover) {
            return;
        }

        $path = Yii::getPathOfAlias('webroot') . '/images/' . $uploadedCover['file_name'];
        if (is_file($path)) {
            @unlink($path);
        }
    }
}
