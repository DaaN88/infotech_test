<?php

declare(strict_types=1);

class m260220_170000_update_subscriptions extends CDbMigration
{
    public function up()
    {
        $this->addColumn('subscriptions', 'name', "varchar(255) NOT NULL DEFAULT '' AFTER phone");

        // ensure a phone can subscribe to many authors, but not duplicate the same author
        $this->createIndex('uq_subscriptions_author_phone', 'subscriptions', 'author_id, phone', true);
        $this->createIndex('idx_subscriptions_phone', 'subscriptions', 'phone');
    }

    public function down()
    {
        $this->dropIndex('idx_subscriptions_phone', 'subscriptions');
        $this->dropIndex('uq_subscriptions_author_phone', 'subscriptions');
        $this->dropColumn('subscriptions', 'name');
    }
}
