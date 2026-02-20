<?php
declare(strict_types=1);

class ReportPageTest extends FunctionalTestCase
{
    /**
     * Проверяет, что отчет ТОП-10 доступен гостю и отображает авторов за 2024 год.
     */
    public function testTopReportLoadsForGuest()
    {
        $response2024 = $this->get('report/top');
        $response2023 = $this->get('report/top', ['year' => 2023]);

        $this->assertContains('ТОП-10 авторов', $response2024['content']);
        $this->assertContains('Год', $response2024['content']);
        $this->assertContains('2024', $response2024['content']);
        $this->assertContains('Книг за 2024', $response2024['content']);

        // Для 2024 в топе есть автор из «2024» набора
        $this->assertContains('Ксения Романова', $response2024['content']);

        // Для 2023 список другой: нет Ксении, но есть автор из 2023 года.
        $this->assertNotContains('Ксения Романова', $response2023['content']);
        $this->assertContains('Джон Смит', $response2023['content']);
    }

    /**
     * Убеждаемся, что набор авторов отличается по выбранным годам (данные не совпадают).
     */
    public function testAuthorsDifferBetweenYears()
    {
        $response2024 = $this->get('report/top', ['year' => 2024]);
        $response2023 = $this->get('report/top', ['year' => 2023]);

        $this->assertNotEquals(
            $response2024['content'],
            $response2023['content'],
            'Отчёт по разным годам должен содержать разные данные'
        );
    }
}
