<?php

declare(strict_types=1);

class BookRepository extends CApplicationComponent
{
    public function create(array $bookData, array $authorIds, ?CUploadedFile $cover): Book
    {
        $authorIds = $this->normalizeAuthors($authorIds);
        $uploadedCover = null;

        try {
            return Yii::app()->transactionManager->run(
                function () use (
                    $bookData,
                    $authorIds,
                    $cover,
                    &$uploadedCover
                ) {
                    $book = new Book();
                    $book->attributes = $bookData;

                    if ($cover) {
                        $uploadedCover = $this->storeCover($cover);
                        $book->cover_path = $uploadedCover['file_name'];
                    }

                    if (! $book->save()) {
                        throw new ValidationException(
                            Yii::t('app', 'book.fix_form'),
                            $book->getErrors()
                        );
                    }

                    $book->syncAuthors($authorIds);

                    if ($uploadedCover) {
                        $this->createBookPhoto($book, $uploadedCover);
                    }

                    Yii::app()->notificationService->enqueueNewBook($book);

                    return $book;
                }
            );
        } catch (ValidationException $e) {
            $this->cleanupUploadedFile($uploadedCover);
            throw $e;
        } catch (Throwable $e) {
            $this->cleanupUploadedFile($uploadedCover);
            throw new RuntimeException(Yii::t('app', 'book.save.error'), 0, $e);
        }
    }

    public function update(int $id, array $bookData, array $authorIds, ?CUploadedFile $cover): Book
    {
        /** @var Book|null $book */
        $book = Book::model()->findByPk($id);
        if (! $book) {
            throw new NotFoundException(Yii::t('app', 'book.not_found'));
        }

        $oldAuthors = array_map('intval', CHtml::listData($book->authors, 'id', 'id'));
        $authorIds = $this->normalizeAuthorsForUpdate($authorIds, $oldAuthors);

        $uploadedCover = null;

        try {
            return Yii::app()->transactionManager->run(
                function () use (
                    $book,
                    $bookData,
                    $authorIds,
                    $oldAuthors,
                    $cover,
                    &$uploadedCover
                ) {
                    $book->attributes = $bookData;

                    if ($cover) {
                        $uploadedCover = $this->storeCover($cover);

                        $book->cover_path = $uploadedCover['file_name'];
                    }

                    if (! $book->save()) {
                        throw new ValidationException(
                            Yii::t('app', 'book.fix_form'),
                            $book->getErrors()
                        );
                    }

                    $book->syncAuthors($authorIds);

                    if ($uploadedCover) {
                        $this->createBookPhoto($book, $uploadedCover);
                    }

                    $this->cleanupSubscriptionsIfAuthorsRemoved($oldAuthors, $authorIds);

                    return $book;
                }
            );
        } catch (ValidationException $e) {
            $this->cleanupUploadedFile($uploadedCover);
            throw $e;
        } catch (Throwable $e) {
            $this->cleanupUploadedFile($uploadedCover);
            throw new RuntimeException(Yii::t('app', 'book.update.error'), 0, $e);
        }
    }

    public function delete(int $id): void
    {
        /** @var Book|null $book */
        $book = Book::model()->findByPk($id);

        if (! $book) {
            throw new NotFoundException(Yii::t('app', 'book.not_found'));
        }

        $authors = array_map('intval', CHtml::listData($book->authors, 'id', 'id'));

        Yii::app()->transactionManager->run(function () use ($book, $authors) {
            $book->delete();

            $this->cleanupSubscriptions($authors);
        });
    }

    public function load(int $id): Book
    {
        /** @var Book|null $book */
        $book = Book::model()->with('authors', 'photos')->findByPk($id);

        if (! $book) {
            throw new NotFoundException(Yii::t('app', 'book.not_found'));
        }

        return $book;
    }

    protected function normalizeAuthors(array $authorIds): array
    {
        $authorIds = array_values(array_unique(array_filter(array_map('intval', $authorIds))));

        if (! $authorIds) {
            $msg = Yii::t('app', 'book.authors.required');
            throw new ValidationException($msg, ['authors' => [$msg]]);
        }

        return $authorIds;
    }

    protected function normalizeAuthorsForUpdate(array $authorIds, array $fallbackExisting): array
    {
        $normalized = array_values(array_unique(array_filter(array_map('intval', $authorIds))));

        if (! $normalized && $fallbackExisting) {
            return $fallbackExisting;
        }

        if (! $normalized) {
            $msg = Yii::t('app', 'book.authors.required');
            throw new ValidationException($msg, ['authors' => [$msg]]);
        }

        return $normalized;
    }

    protected function storeCover(CUploadedFile $file): array
    {
        $ext = $file->getExtensionName();
        $safeName = uniqid('cover_', true) . ($ext ? '.' . $ext : '');

        $targetDir = Yii::getPathOfAlias('webroot') . '/images';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (! $file->saveAs($targetDir . '/' . $safeName)) {
            throw new RuntimeException(Yii::t('app', 'book.cover.save_error'));
        }

        return [
            'file_name' => $safeName,
            'display_name' => $file->getName(),
        ];
    }

    protected function createBookPhoto(Book $book, array $uploadedCover): void
    {
        $photo = new BookPhoto();
        $photo->book_id = $book->id;
        $photo->file_name = $uploadedCover['file_name'];
        $photo->display_name = $uploadedCover['display_name'];
        $photo->created_at = date('Y-m-d H:i:s');
        $photo->save();

        $currentPhotos = $book->photos ?: array();
        $currentPhotos[] = $photo;
        $book->photos = $currentPhotos;
    }

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

    protected function cleanupSubscriptionsIfAuthorsRemoved(array $oldAuthors, array $newAuthors): void
    {
        $removed = array_diff($oldAuthors, $newAuthors);
        $this->cleanupSubscriptions($removed);
    }

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
