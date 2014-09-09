<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tokens".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $key
 * @property string $create_date
 * @property string $drop_date
 * @property integer $status
 *
 * @property User $user
 */
class Token extends \yii\db\ActiveRecord
{
    const STATUS_GOOD = 0;
    const STATUS_EXPIRE = 1;

    const EXPIRE_PERIOD = '1 week';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tokens';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'key'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['create_date', 'drop_date'], 'safe'],
            [['key'], 'string', 'max' => 255],
            [['key'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'key' => 'Key',
            'create_date' => 'Create Date',
            'drop_date' => 'Drop Date',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function init()
    {
        parent::init();

        if ($this->isNewRecord) {
            $this->key = Token::generateKey();
            $this->status = Token::STATUS_GOOD;
            $this->create_date = new \yii\db\Expression('NOW()');
            $this->drop_date = new \yii\db\Expression('DATE_ADD(NOW(), INTERVAL ' . Token::EXPIRE_PERIOD . ')');
        }
    }

    /**
     * @return string
     */
    public static function generateKey() {
        return md5(rand());
    }


}
