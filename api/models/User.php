<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;
use yii\web\UploadedFile;
use yii\imagine\Image;
use Imagine\Image\Box;


/**
 * This is the model class for table "users".
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $create_date
 *
 * @property Comment[] $comments
 * @property Invite[] $invites
 * @property Post[] $posts
 * @property Token[] $tokens
 */

class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    public static function findIdentityByAccessToken($token, $type = null){
        return User::find()
            ->joinWith(Token::tableName())
            ->where([
                Token::tableName() . '.key' => $token
            ])
            ->one();
    }

    public static function findIdentity($id)
    {
        return User::findOne($id);
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return boolean whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'required'],
            [['create_date'], 'safe'],
            [['name', 'email', 'phone', 'password'], 'string', 'max' => 255],
            [['name', 'email', 'phone', 'password'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['file'], 'file', 'extensions' => 'jpg, png', 'mimeTypes' => 'image/jpeg, image/png',],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'create_date' => 'Create Date',
        ];
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['password']);

        $fields['avatar'] = function ($model) {
            return $this->getAvatarUrl();
        };

        return $fields;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvites()
    {
        return $this->hasMany(Invite::className(), ['inviter_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['user_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['author_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['author_id' => 'id']);
    }

    public function isCorrectPassword($password) {
        return \Yii::$app->security->validatePassword($password, $this->password);
    }

    public function init()
    {
        parent::init();

        if ($this->isNewRecord) {
            $this->create_date = new \yii\db\Expression('NOW()');
        }
    }

    public function uploadAvatar(UploadedFile $file) {

        $this->file = $file;

        if ($this->validate()) {
            Image::getImagine()
                ->open($this->file->tempName)
                ->resize(new Box(100, 100))
                ->save(\Yii::getAlias('@webroot/uploads') . '/avatar-user-' . $this->id . '.png');

            return true;
        }

        return false;
    }

    public function getAvatarUrl() {
        $url = '/avatar-user-' . $this->id . '.png';

        return file_exists(\Yii::getAlias('@webroot/uploads') . $url )
            ?
                (\Yii::getAlias('@web/uploads')  . $url)
            :
                'avatar-default.png';
    }

}
