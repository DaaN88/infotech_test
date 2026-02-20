<?php

declare(strict_types=1);

class TopAuthorsReportService extends CApplicationComponent implements ReportServiceInterface
{
    public function getAvailableYears(): array
    {
        $years = Yii::app()->db
            ->createCommand()
            ->selectDistinct('year')
            ->from('books')
            ->order('year DESC')
            ->queryColumn();

        return array_map('intval', $years);
    }

    public function getTopAuthorsByYear(int $year, int $limit = 10): array
    {
        return Yii::app()->db
            ->createCommand()
            ->select('a.id, a.name, COUNT(b.id) AS books_count')
            ->from('authors a')
            ->join('book_author ba', 'ba.author_id = a.id')
            ->join('books b', 'b.id = ba.book_id')
            ->where('b.year = :year', [':year' => $year])
            ->group('a.id, a.name')
            ->order('books_count DESC, a.name ASC')
            ->limit($limit)
            ->queryAll();
    }

    public function getDefaultYear(): int
    {
        $years = $this->getAvailableYears();
        if ($years) {
            return (int)$years[0];
        }

        return (int)date('Y');
    }
}
