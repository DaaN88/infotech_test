<?php

declare(strict_types=1);

interface ReportServiceInterface
{
    /**
     * @return int[]
     */
    public function getAvailableYears(): array;

    /**
     * @return array<array{id:int,name:string,books_count:int|string}>
     */
    public function getTopAuthorsByYear(int $year, int $limit = 10): array;

    public function getDefaultYear(): int;
}
