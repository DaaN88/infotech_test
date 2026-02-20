<?php

declare(strict_types=1);

class ReportController extends Controller
{
    /**
     * ТОП-10 авторов по количеству книг за выбранный год.
     */
    public function actionTop(): void
    {
        /** @var ReportServiceInterface $reportService */
        $reportService = Yii::app()->reportService;

        $years = $reportService->getAvailableYears();
        $defaultYear = $reportService->getDefaultYear();

        $year = (int) Yii::app()->request->getQuery('year', $defaultYear);

        if ($years && !in_array($year, $years, true)) {
            $year = $defaultYear;
        }

        $topAuthors = $reportService->getTopAuthorsByYear($year, 10);

        $this->pageTitle = 'ТОП-10 авторов';
        $this->breadcrumbs = ['Отчет', 'ТОП-10'];

        $this->render('top', array(
            'topAuthors' => $topAuthors,
            'year' => $year,
            'years' => $years,
        ));
    }
}
