<?php

use yii\db\Schema;
use yii\db\Migration;

class m140919_102735_comments extends Migration
{
    public function up()
    {
        $this->createTable('comments', [
            'id' => Schema::TYPE_PK,
            'author_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'post_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'text' => Schema::TYPE_TEXT . ' NOT NULL',
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);

        $this->addForeignKey('fk_comments_users', 'comments', 'author_id', 'users', 'id');
        $this->addForeignKey('fk_comments_posts', 'comments', 'post_id', 'posts', 'id');
    }

    public function down()
    {
        $this->dropForeignKey('fk_comments_users', 'comments');
        $this->dropForeignKey('fk_comments_posts', 'comments');

        $this->dropTable('comments');
    }
}
