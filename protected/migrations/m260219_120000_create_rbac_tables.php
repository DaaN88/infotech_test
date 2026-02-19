<?php

declare(strict_types=1);

/**
 * RBAC tables for CDbAuthManager.
 */
class m260219_120000_create_rbac_tables extends CDbMigration
{
    public function up()
    {
        $options = 'ENGINE=InnoDB DEFAULT CHARSET=utf8mb4';

        $this->createTable('authitem', array(
            'name'        => 'varchar(64) NOT NULL',
            'type'        => 'int(11) NOT NULL',
            'description' => 'text',
            'bizrule'     => 'text',
            'data'        => 'text',
            'PRIMARY KEY (`name`)',
        ), $options);

        $this->createTable('authitemchild', array(
            'parent' => 'varchar(64) NOT NULL',
            'child'  => 'varchar(64) NOT NULL',
            'PRIMARY KEY (`parent`,`child`)',
        ), $options);

        $this->createTable('authassignment', array(
            'itemname' => 'varchar(64) NOT NULL',
            'userid'   => 'varchar(64) NOT NULL',
            'bizrule'  => 'text',
            'data'     => 'text',
            'PRIMARY KEY (`itemname`,`userid`)',
        ), $options);

        // FKs
        $this->addForeignKey('fk_authchild_parent', 'authitemchild', 'parent', 'authitem', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_authchild_child', 'authitemchild', 'child', 'authitem', 'name', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_authassign_item', 'authassignment', 'itemname', 'authitem', 'name', 'CASCADE', 'CASCADE');

        // Roles
        $this->insert('authitem', array('name' => 'guest', 'type' => 2, 'description' => 'Гость'));
        $this->insert('authitem', array('name' => 'user', 'type' => 2, 'description' => 'Пользователь'));
        $this->insert('authitem', array('name' => 'admin', 'type' => 2, 'description' => 'Администратор'));
    }

    public function down()
    {
        $this->dropForeignKey('fk_authassign_item', 'authassignment');
        $this->dropForeignKey('fk_authchild_child', 'authitemchild');
        $this->dropForeignKey('fk_authchild_parent', 'authitemchild');

        $this->dropTable('authassignment');
        $this->dropTable('authitemchild');
        $this->dropTable('authitem');
    }
}
