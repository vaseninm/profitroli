<?php

use yii\db\Schema;
use yii\db\Migration;

class m140907_092050_users extends Migration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => Schema::TYPE_PK,
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'email' => Schema::TYPE_STRING  . ' NOT NULL',
            'phone' => Schema::TYPE_STRING,
            'password' => Schema::TYPE_STRING,
            'create_date' => Schema::TYPE_DATETIME . ' NOT NULL',
        ]);

        $this->createIndex('uq_users_email', 'users', 'email', true);

        $this->insert('users', [
                'name' => 'Матвей Васенин',
                'email' => 'vaseninm@gmail.com',
                'phone' => '79150000000',
                'password' => crypt('123qwe'),
                'create_date' => new \yii\db\Expression('NOW()'),
            ]);
    }

    public function down()
    {
        $this->dropIndex('uq_users_email', 'users');
        $this->dropTable('users');
    }
}
