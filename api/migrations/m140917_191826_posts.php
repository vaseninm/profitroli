<?php

use yii\db\Schema;

class m140917_191826_posts extends \yii\db\Migration
{
    public function up()
    {
        $this->createTable('posts', [
            'id' => Schema::TYPE_PK,
            'title' => Schema::TYPE_STRING . ' NOT NULL',
            'text' => Schema::TYPE_TEXT . ' NOT NULL',
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'create_date' => Schema::TYPE_TIMESTAMP . ' NOT NULL',
        ]);

        $this->addForeignKey('fk_posts_users', 'posts', 'author_id', 'users', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_posts_users', 'posts');
        $this->dropTable('posts');
    }
}
