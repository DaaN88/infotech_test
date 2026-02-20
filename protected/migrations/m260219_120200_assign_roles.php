<?php

declare(strict_types=1);

class m260219_120200_assign_roles extends CDbMigration
{
    public function up()
    {
        // admin -> user inherits user role
        $this->insert('authitemchild', ['parent' => 'admin', 'child' => 'user']);

        // assign admin role to seeded admin user (id=1)
        $this->insert('authassignment', [
            'itemname' => 'admin',
            'userid'   => '1',
        ]);
    }

    public function down()
    {
        $this->delete('authassignment', 'itemname=:role AND userid=:id', [':role' => 'admin', ':id' => '1']);
        $this->delete('authitemchild', 'parent=:p AND child=:c', [':p' => 'admin', ':c' => 'user']);
    }
}
