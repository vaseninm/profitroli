<?php

use yii\db\Schema;
use yii\db\Migration;

class m140909_120529_tokens extends Migration
{
    public function up()
    {
        $this->createTable('tokens', [
                'id' => Schema::TYPE_PK,
                'user_id' => Schema::TYPE_INTEGER . ' NOT NULL',
                'key' => Schema::TYPE_STRING . ' NOT NULL',
                'create_date' => Schema::TYPE_DATETIME,
                'drop_date' => Schema::TYPE_DATETIME,
                'status' => Schema::TYPE_SMALLINT,
            ]);

        $this->createIndex('uq_tokens_key', 'tokens', 'key', true);
        $this->addForeignKey('fk_user_token', 'tokens', 'user_id', 'users', 'id');
    }

    public function down()
    {
        $this->dropIndex('uq_tokens_key', 'tokens');
        $this->dropForeignKey('fk_user_token', 'tokens');
        $this->dropTable('tokens');
    }
}
