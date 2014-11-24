<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comments".
 *
 * @property integer $id
 * @property integer $author_id
 * @property integer $post_id
 * @property string $text
 * @property string $create_date
 *
 * @property Post $post
 * @property User $author
 */
class Comment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['author_id', 'post_id'], 'integer'],
            [['text'], 'string'],
            [['create_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_id' => 'Author ID',
            'post_id' => 'Post ID',
            'text' => 'Text',
            'create_date' => 'Create Date',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['author_id']);

        $fields['author'] = 'author';

        return $fields;
    }

    public function init()
    {
        parent::init();

        if ($this->isNewRecord) {
            $this->create_date = new \yii\db\Expression('NOW()');
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }
}
