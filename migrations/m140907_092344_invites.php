<?php

use yii\db\Schema;
use yii\db\Migration;

class m140907_092344_invites extends Migration
{
    public function up()
    {
        $this->createTable('invites', [
            'id' => Schema::TYPE_PK,
            'inviter_id' => Schema::TYPE_INTEGER,
            'user_id' => Schema::TYPE_INTEGER,
            'email' => Schema::TYPE_STRING,
            'status' => Schema::TYPE_SMALLINT,
            'create_date' => Schema::TYPE_DATETIME,
            'use_date' => Schema::TYPE_DATETIME,
        ]);

        $this->addForeignKey('fk_user_invite_from', 'invites', 'inviter_id', 'users', 'id');
        $this->addForeignKey('fk_user_invite_to', 'invites', 'user_id', 'users', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_invite_from', 'invites');
        $this->dropForeignKey('fk_user_invite_to', 'invites');
        $this->dropTable('invites');
    }
}
