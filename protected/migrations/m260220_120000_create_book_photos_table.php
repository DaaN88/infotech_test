<?php

declare(strict_types=1);

class m260220_120000_create_book_photos_table extends CDbMigration
{
    public function up()
    {
        $options = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

        $this->createTable('book_photos', [
            'id'           => 'pk',
            'book_id'      => 'int(11) NOT NULL',
            'file_name'    => 'varchar(255) NOT NULL', // техническое имя файла на диске
            'display_name' => 'varchar(255) NOT NULL', // человеко-понятное название
            'created_at'   => 'datetime NOT NULL',
        ], $options);

        $this->createIndex('idx_book_photos_book', 'book_photos', 'book_id');

        $this->addForeignKey(
            'fk_book_photos_book',
            'book_photos',
            'book_id',
            'books',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_book_photos_book', 'book_photos');
        $this->dropTable('book_photos');
    }
}
