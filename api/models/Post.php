<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "posts".
 *
 * @property integer $id
 * @property string $title
 * @property string $text
 * @property integer $author_id
 * @property string $create_date
 *
 * @property User $author
 * @property Comment[] $comments
 */
class Post extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'posts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['author_id'], 'integer'],
            [['create_date'], 'safe'],
            [['title'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'text' => 'Text',
            'author_id' => 'Author ID',
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
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['post_id' => 'id']);
    }
}
