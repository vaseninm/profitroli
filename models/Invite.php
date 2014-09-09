<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invites".
 *
 * @property integer $id
 * @property integer $inviter_id
 * @property integer $user_id
 * @property string $email
 * @property integer $status
 * @property string $key
 * @property string $create_date
 * @property string $use_date
 *
 * @property User $user
 * @property User $inviter
 */
class Invite extends \yii\db\ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_USED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invites';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['inviter_id', 'key', 'create_date'], 'required'],
            [['inviter_id', 'user_id', 'status'], 'integer'],
            [['create_date', 'use_date'], 'safe'],
            [['email', 'key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'inviter_id' => 'Inviter ID',
            'user_id' => 'User ID',
            'email' => 'Email',
            'status' => 'Status',
            'create_date' => 'Create Date',
            'use_date' => 'Use Date',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInviter()
    {
        return $this->hasOne(User::className(), ['id' => 'inviter_id']);
    }

    public function init()
    {
        parent::init();

        if ($this->isNewRecord) {
            $this->key = Invite::generateKey();
            $this->status = Invite::STATUS_NEW;
            $this->create_date = new \yii\db\Expression('NOW()');
        }
    }

    /**
     * @return string
     */
    public static function generateKey() {
        return substr(md5(rand()),3,8);
    }


}
