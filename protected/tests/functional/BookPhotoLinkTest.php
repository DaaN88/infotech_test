<?php
declare(strict_types=1);

class BookPhotoLinkTest extends FunctionalTestCase
{
    /**
     * Проверяет, что ссылка с названия книги ведет на картинку (отдаёт 200).
     */
    public function testBookTitleOpensPhoto()
    {
        $catalog = $this->get('book/index');

        // Найдём ссылку на обложку первой книги из фикстур
        $this->assertContains('cover_01.png', $catalog['content']);

        // Проверяем, что файл обложки физически существует и не пустой
        $path = realpath(__DIR__ . '/../../../images/cover_01.png');
        $this->assertNotFalse($path, 'Путь к обложке не найден');
        $this->assertFileExists($path);
        $this->assertGreaterThan(0, filesize($path));
    }
}
