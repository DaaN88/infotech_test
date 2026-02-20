<?php

declare(strict_types=1);

use Yiisoft\Queue\Message\Message;

/**
 * Сервис для постановки уведомлений о новых книгах.
 */
class NotificationService extends CApplicationComponent
{
    /**
     * Ставит задачи в очередь для всех подписчиков авторов книги.
     */
    public function enqueueNewBook(Book $book): void
    {
        foreach ($this->getAuthors($book) as $author) {
            $subs = Subscription::model()->findAllByAttributes(['author_id' => $author->id]);
            foreach ($subs as $sub) {
                $text = sprintf('Новая книга "%s" у автора %s', $book->title, $author->name);
                Yii::app()->queue->push(
                    Message::fromData(SendSmsHandler::class, [
                        'phone' => $sub->phone,
                        'text' => $text,
                    ])
                );
            }
        }
    }

    /**
     * Получаем авторов из связи или через прямой запрос (на случай, если relation не загружен).
     *
     * @return Author[]
     */
    private function getAuthors(Book $book): array
    {
        if ($book->authors) {
            return $book->authors;
        }

        $authorIds = Yii::app()->db
            ->createCommand()
            ->select('author_id')
            ->from('book_author')
            ->where('book_id = :id', [':id' => $book->id])
            ->queryColumn();

        if (!$authorIds) {
            return [];
        }

        return Author::model()->findAllByPk($authorIds);
    }
}
