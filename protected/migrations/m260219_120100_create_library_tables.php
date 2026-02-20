<?php

declare(strict_types=1);

class m260219_120100_create_library_tables extends CDbMigration
{
    public function up()
    {
        $options = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

        // Users
        $this->createTable('users', array(
            'id'            => 'pk',
            'username'      => 'varchar(64) NOT NULL',
            'password_hash' => 'varchar(255) NOT NULL',
            'role'          => "varchar(32) NOT NULL DEFAULT 'user'",
            'created_at'    => 'datetime NOT NULL',
            'updated_at'    => 'datetime NOT NULL',
        ), $options);
        $this->createIndex('uq_users_username', 'users', 'username', true);

        // Authors
        $this->createTable('authors', array(
            'id'         => 'pk',
            'name'       => 'varchar(255) NOT NULL',
            'created_at' => 'datetime NOT NULL',
            'updated_at' => 'datetime NOT NULL',
        ), $options);

        // Books
        $this->createTable('books', array(
            'id'          => 'pk',
            'title'       => 'varchar(255) NOT NULL',
            'year'        => 'int(4) NOT NULL',
            'description' => 'text',
            'isbn'        => 'varchar(32) NOT NULL',
            'cover_path'  => 'varchar(255)',
            'created_at'  => 'datetime NOT NULL',
            'updated_at'  => 'datetime NOT NULL',
        ), $options);
        $this->createIndex('uq_books_isbn', 'books', 'isbn', true);

        // Junction book-author
        $this->createTable('book_author', array(
            'book_id'   => 'int(11) NOT NULL',
            'author_id' => 'int(11) NOT NULL',
            'PRIMARY KEY (`book_id`,`author_id`)',
        ), $options);

        // Subscriptions to authors
        $this->createTable('subscriptions', array(
            'id'         => 'pk',
            'author_id'  => 'int(11) NOT NULL',
            'user_id'    => 'int(11)',
            'phone'      => 'varchar(32) NOT NULL',
            'name'       => "varchar(255) NOT NULL",
            'created_at' => 'datetime NOT NULL',
        ), $options);

        // Foreign keys
        $this->addForeignKey(
            'fk_book_author_book',
            'book_author',
            'book_id',
            'books',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_book_author_author',
            'book_author',
            'author_id',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk_subscriptions_author',
            'subscriptions',
            'author_id',
            'authors',
            'id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk_subscriptions_user',
            'subscriptions',
            'user_id',
            'users',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // Seed admin user
        $hash = CPasswordHelper::hashPassword('admin');
        $now  = new CDbExpression('NOW()');

        $this->insert('users', array(
            'username'      => 'admin',
            'password_hash' => $hash,
            'role'          => 'admin',
            'created_at'    => $now,
            'updated_at'    => $now,
        ));
    }

    public function down()
    {
        $this->delete('users', 'username=:u', array(':u' => 'admin'));

        $this->dropForeignKey('fk_subscriptions_user', 'subscriptions');
        $this->dropForeignKey('fk_subscriptions_author', 'subscriptions');
        $this->dropForeignKey('fk_book_author_author', 'book_author');
        $this->dropForeignKey('fk_book_author_book', 'book_author');

        $this->dropTable('subscriptions');
        $this->dropTable('book_author');
        $this->dropTable('books');
        $this->dropTable('authors');
        $this->dropTable('users');
    }
}
