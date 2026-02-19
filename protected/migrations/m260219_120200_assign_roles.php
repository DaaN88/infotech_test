<?php

declare(strict_types=1);

class m260219_120200_assign_roles extends CDbMigration
{
    public function up()
    {
        // admin -> user inherits user role
        $this->insert('authitemchild', array('parent' => 'admin', 'child' => 'user'));

        // assign admin role to seeded admin user (id=1)
        $this->insert('authassignment', array(
            'itemname' => 'admin',
            'userid'   => '1',
        ));
    }

    public function down()
    {
        $this->delete('authassignment', 'itemname=:role AND userid=:id', array(':role' => 'admin', ':id' => '1'));
        $this->delete('authitemchild', 'parent=:p AND child=:c', array(':p' => 'admin', ':c' => 'user'));
    }
}
