<?php

declare(strict_types=1);

class m260220_130000_seed_extended_catalog extends CDbMigration
{
    public function up()
    {
        $now = new CDbExpression('NOW()');

        // Authors (add 17 new, keep existing 3)
        $authors = [
            ['id' => 4,  'name' => 'Анна Каренина'],
            ['id' => 5,  'name' => 'Сергей Павлов'],
            ['id' => 6,  'name' => 'Алексей Смирнов'],
            ['id' => 7,  'name' => 'Елена Кузнецова'],
            ['id' => 8,  'name' => 'Дмитрий Волков'],
            ['id' => 9,  'name' => 'Наталья Орлова'],
            ['id' => 10, 'name' => 'Виктор Егоров'],
            ['id' => 11, 'name' => 'Ольга Соколова'],
            ['id' => 12, 'name' => 'Павел Лебедев'],
            ['id' => 13, 'name' => 'Ирина Морозова'],
            ['id' => 14, 'name' => 'Михаил Федоров'],
            ['id' => 15, 'name' => 'Ксения Романова'],
            ['id' => 16, 'name' => 'Георгий Никитин'],
            ['id' => 17, 'name' => 'Татьяна Васильева'],
            ['id' => 18, 'name' => 'Андрей Беляев'],
            ['id' => 19, 'name' => 'Светлана Громова'],
            ['id' => 20, 'name' => 'Роман Захаров'],
        ];

        foreach ($authors as $author) {
            $this->insert('authors', [
                'id' => $author['id'],
                'name' => $author['name'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Books (add 18 new, keep existing 2)
        // Распределяем годы так, чтобы ТОП по годам менялся:
        // 2024 — основная масса, 2023 — другой набор авторов, 2022 — ещё один набор.
        $books = [
            [
                'id' => 3,
                'title' => 'Практика DevOps',
                'year' => 2024,
                'description' => 'Культура, инструменты и пайплайны доставки.',
                'isbn' => '978-1-00000-000-3',
                'cover_path' => 'cover_03.png',
            ],
            [
                'id' => 4,
                'title' => 'Микросервисы на практике',
                'year' => 2024,
                'description' => 'Разработка и поддержка распределенных систем.',
                'isbn' => '978-1-00000-000-4',
                'cover_path' => 'cover_04.png',
            ],
            [
                'id' => 5,
                'title' => 'Data Engineering',
                'year' => 2022,
                'description' => 'Построение надежных дата-пайплайнов.',
                'isbn' => '978-1-00000-000-5',
                'cover_path' => 'cover_05.png',
            ],
            [
                'id' => 6,
                'title' => 'Искусственный интеллект',
                'year' => 2024,
                'description' => 'Прикладные кейсы и этика ИИ.',
                'isbn' => '978-1-00000-000-6',
                'cover_path' => 'cover_06.png',
            ],
            [
                'id' => 7,
                'title' => 'Cloud Native Patterns',
                'year' => 2024,
                'description' => 'Образцы проектирования облачных приложений.',
                'isbn' => '978-1-00000-000-7',
                'cover_path' => 'cover_07.png',
            ],
            [
                'id' => 8,
                'title' => 'Безопасность приложений',
                'year' => 2024,
                'description' => 'Практики безопасной разработки и DevSecOps.',
                'isbn' => '978-1-00000-000-8',
                'cover_path' => 'cover_08.png',
            ],
            [
                'id' => 9,
                'title' => 'Современный фронтенд',
                'year' => 2024,
                'description' => 'Инструменты и архитектуры интерфейсов.',
                'isbn' => '978-1-00000-000-9',
                'cover_path' => 'cover_09.png',
            ],
            [
                'id' => 10,
                'title' => 'Тестирование и качество',
                'year' => 2023,
                'description' => 'Метрики и автоматизация QA.',
                'isbn' => '978-1-00000-001-0',
                'cover_path' => 'cover_10.png',
            ],
            [
                'id' => 11,
                'title' => 'Менеджмент продукта',
                'year' => 2024,
                'description' => 'От discovery до delivery.',
                'isbn' => '978-1-00000-001-1',
                'cover_path' => 'cover_11.png',
            ],
            [
                'id' => 12,
                'title' => 'Machine Learning 101',
                'year' => 2024,
                'description' => 'Быстрый старт в ML.',
                'isbn' => '978-1-00000-001-2',
                'cover_path' => 'cover_12.png',
            ],
            [
                'id' => 13,
                'title' => 'Глубокое обучение',
                'year' => 2024,
                'description' => 'Нейронные сети и их обучение.',
                'isbn' => '978-1-00000-001-3',
                'cover_path' => 'cover_13.png',
            ],
            [
                'id' => 14,
                'title' => 'Rust для начинающих',
                'year' => 2023,
                'description' => 'Безопасное и быстрое программирование.',
                'isbn' => '978-1-00000-001-4',
                'cover_path' => 'cover_14.png',
            ],
            [
                'id' => 15,
                'title' => 'Go: высоконагруженные сервисы',
                'year' => 2024,
                'description' => 'Практика построения сервисов на Go.',
                'isbn' => '978-1-00000-001-5',
                'cover_path' => 'cover_15.png',
            ],
            [
                'id' => 16,
                'title' => 'PostgreSQL в продакшене',
                'year' => 2024,
                'description' => 'Тюнинг и эксплуатация.',
                'isbn' => '978-1-00000-001-6',
                'cover_path' => 'cover_16.png',
            ],
            [
                'id' => 17,
                'title' => 'Секреты CSS',
                'year' => 2023,
                'description' => 'Продвинутые техники вёрстки.',
                'isbn' => '978-1-00000-001-7',
                'cover_path' => 'cover_17.png',
            ],
            [
                'id' => 18,
                'title' => 'Эффективный руководитель',
                'year' => 2024,
                'description' => 'Лидерство в IT-командах.',
                'isbn' => '978-1-00000-001-8',
                'cover_path' => 'cover_18.png',
            ],
            [
                'id' => 19,
                'title' => 'Продуктовая аналитика',
                'year' => 2024,
                'description' => 'Метрики, эксперименты, выводы.',
                'isbn' => '978-1-00000-001-9',
                'cover_path' => 'cover_19.png',
            ],
            [
                'id' => 20,
                'title' => 'Agile практикум',
                'year' => 2022,
                'description' => 'Scrum, Kanban и beyond.',
                'isbn' => '978-1-00000-002-0',
                'cover_path' => 'cover_20.png',
            ],
        ];

        foreach ($books as $book) {
            $this->insert('books', [
                'id' => $book['id'],
                'title' => $book['title'],
                'year' => $book['year'],
                'description' => $book['description'],
                'isbn' => $book['isbn'],
                'cover_path' => $book['cover_path'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Book-Author links (ensure distribution rules)
        $links = [
            ['book_id' => 3, 'author_id' => 4],
            ['book_id' => 3, 'author_id' => 5],
            ['book_id' => 4, 'author_id' => 6],
            ['book_id' => 4, 'author_id' => 7],
            ['book_id' => 5, 'author_id' => 8],
            ['book_id' => 5, 'author_id' => 9],
            ['book_id' => 6, 'author_id' => 10],
            ['book_id' => 6, 'author_id' => 11],
            ['book_id' => 7, 'author_id' => 12],
            ['book_id' => 7, 'author_id' => 13],
            ['book_id' => 7, 'author_id' => 14],
            ['book_id' => 8, 'author_id' => 15],
            ['book_id' => 8, 'author_id' => 16],
            ['book_id' => 8, 'author_id' => 17],
            ['book_id' => 9, 'author_id' => 18],
            ['book_id' => 9, 'author_id' => 19],
            ['book_id' => 9, 'author_id' => 20],
            ['book_id' => 10, 'author_id' => 4],
            ['book_id' => 10, 'author_id' => 6],
            ['book_id' => 10, 'author_id' => 8],
            ['book_id' => 11, 'author_id' => 2],
            ['book_id' => 11, 'author_id' => 10],
            ['book_id' => 11, 'author_id' => 15],
            ['book_id' => 12, 'author_id' => 12],
            ['book_id' => 13, 'author_id' => 14],
            ['book_id' => 14, 'author_id' => 9],
            ['book_id' => 15, 'author_id' => 5],
            ['book_id' => 16, 'author_id' => 7],
            ['book_id' => 17, 'author_id' => 11],
            ['book_id' => 18, 'author_id' => 15],
            ['book_id' => 19, 'author_id' => 16],
            ['book_id' => 20, 'author_id' => 18],
        ];

        foreach ($links as $link) {
            $this->insert('book_author', $link);
        }

        // Photos for each book (at least one)
        $photos = [
            [
                'book_id' => 1,
                'file_name' => 'cover_01.png',
                'display_name' => 'Обложка «Путь разработчика»',
            ],
            [
                'book_id' => 2,
                'file_name' => 'cover_02.png',
                'display_name' => 'Обложка «Архитектура систем»',
            ],
            [
                'book_id' => 3,
                'file_name' => 'cover_03.png',
                'display_name' => 'Практика DevOps — обложка',
            ],
            [
                'book_id' => 3,
                'file_name' => 'cover_03_alt.png',
                'display_name' => 'Практика DevOps — диаграмма',
            ],
            [
                'book_id' => 4,
                'file_name' => 'cover_04.png',
                'display_name' => 'Микросервисы — обложка',
            ],
            [
                'book_id' => 4,
                'file_name' => 'cover_04_alt.png',
                'display_name' => 'Микросервисы — схема',
            ],
            [
                'book_id' => 5,
                'file_name' => 'cover_05.png',
                'display_name' => 'Data Engineering',
            ],
            [
                'book_id' => 6,
                'file_name' => 'cover_06.png',
                'display_name' => 'Искусственный интеллект',
            ],
            [
                'book_id' => 7,
                'file_name' => 'cover_07.png',
                'display_name' => 'Cloud Native Patterns',
            ],
            [
                'book_id' => 7,
                'file_name' => 'cover_07_alt.png',
                'display_name' => 'Cloud Native — k8s',
            ],
            [
                'book_id' => 8,
                'file_name' => 'cover_08.png',
                'display_name' => 'Безопасность приложений',
            ],
            [
                'book_id' => 9,
                'file_name' => 'cover_09.png',
                'display_name' => 'Современный фронтенд',
            ],
            [
                'book_id' => 10,
                'file_name' => 'cover_10.png',
                'display_name' => 'Тестирование и качество',
            ],
            [
                'book_id' => 11,
                'file_name' => 'cover_11.png',
                'display_name' => 'Менеджмент продукта',
            ],
            [
                'book_id' => 12,
                'file_name' => 'cover_12.png',
                'display_name' => 'Machine Learning 101',
            ],
            [
                'book_id' => 13,
                'file_name' => 'cover_13.png',
                'display_name' => 'Глубокое обучение',
            ],
            [
                'book_id' => 14,
                'file_name' => 'cover_14.png',
                'display_name' => 'Rust для начинающих',
            ],
            [
                'book_id' => 15,
                'file_name' => 'cover_15.png',
                'display_name' => 'Go: высоконагруженные сервисы',
            ],
            [
                'book_id' => 16,
                'file_name' => 'cover_16.png',
                'display_name' => 'PostgreSQL в продакшене',
            ],
            [
                'book_id' => 17,
                'file_name' => 'cover_17.png',
                'display_name' => 'Секреты CSS',
            ],
            [
                'book_id' => 18,
                'file_name' => 'cover_18.png',
                'display_name' => 'Эффективный руководитель',
            ],
            [
                'book_id' => 19,
                'file_name' => 'cover_19.png',
                'display_name' => 'Продуктовая аналитика',
            ],
            [
                'book_id' => 20,
                'file_name' => 'cover_20.png',
                'display_name' => 'Agile практикум',
            ],
        ];

        foreach ($photos as $photo) {
            $this->insert('book_photos', [
                'book_id' => $photo['book_id'],
                'file_name' => $photo['file_name'],
                'display_name' => $photo['display_name'],
                'created_at' => $now,
            ]);
        }
    }

    public function down()
    {
        // Remove photos
        $this->delete('book_photos', 'book_id BETWEEN 1 AND 20');

        // Remove links for inserted books
        $this->delete('book_author', 'book_id BETWEEN 3 AND 20');

        // Delete added books
        $this->delete('books', 'id BETWEEN 3 AND 20');

        // Delete added authors
        $this->delete('authors', 'id BETWEEN 4 AND 20');
    }
}
