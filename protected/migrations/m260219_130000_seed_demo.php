<?php

declare(strict_types=1);

class m260219_130000_seed_demo extends CDbMigration
{
    public function up()
    {
        $now = new CDbExpression('NOW()');

        $this->insert('authors', array('name' => 'Иван Иванов', 'created_at' => $now, 'updated_at' => $now));
        $this->insert('authors', array('name' => 'Мария Петрова', 'created_at' => $now, 'updated_at' => $now));
        $this->insert('authors', array('name' => 'Джон Смит', 'created_at' => $now, 'updated_at' => $now));

        $this->insert('books', array(
            'title' => 'Путь разработчика',
            'year' => 2024,
            'description' => 'Практические советы по построению карьеры.',
            'isbn' => '978-1-23456-789-0',
            'cover_path' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ));
        $this->insert('books', array(
            'title' => 'Архитектура систем',
            'year' => 2023,
            'description' => 'Шаблоны и подходы к проектированию сложных сервисов.',
            'isbn' => '978-1-11111-111-1',
            'cover_path' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ));

        // link authors
        $this->insert('book_author', array('book_id' => 1, 'author_id' => 1));
        $this->insert('book_author', array('book_id' => 1, 'author_id' => 2));
        $this->insert('book_author', array('book_id' => 2, 'author_id' => 3));
    }

    public function down()
    {
        $this->delete('book_author', 'book_id IN (1,2)');
        $this->delete('books', 'id IN (1,2)');
        $this->delete('authors', 'id IN (1,2,3)');
    }
}
