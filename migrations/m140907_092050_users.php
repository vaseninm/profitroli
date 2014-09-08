<?php

use yii\db\Schema;
use yii\db\Migration;

class m140907_092050_users extends Migration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING,
            'email' => Schema::TYPE_STRING,
            'phone' => Schema::TYPE_STRING,
            'create_date' => Schema::TYPE_DATETIME,
        ]);
    }

    public function down()
    {
        $this->dropTable('users');
    }
}
