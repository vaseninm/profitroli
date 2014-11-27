<?php

namespace app\controllers;

use app\models\Invite;
use app\models\Token;
use app\models\User;
use yii\filters\auth\QueryParamAuth;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\UploadedFile;

class UserController extends \yii\rest\Controller
{
    public function actions() {
        return [
            'options' => [
                'class' => 'yii\rest\OptionsAction',
            ],
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'token',
            'only' => ['edit', 'me', 'upload-avatar'],
        ];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];
        return $behaviors;
    }

    public function actionLogin()
    {
        $user = User::find()
            ->where(['email' => \Yii::$app->request->post('email')])
            ->one();

        if (! $user || ! $user->isCorrectPassword(\Yii::$app->request->post('password'))) {
            throw new HttpException(401, 'Неверная пара email/пароль');
        }

        $token = new Token();
        $token->user_id = $user->id;

        $token->save();

        \Yii::$app->response->setStatusCode(201);

        return $token;
    }

    public function actionRegistration()
    {
        $user = new User();
        $invite = Invite::findOne(['key' => \Yii::$app->request->post('invite')]);

        if (! $invite) throw new HttpException(400, 'Инвайт не найден');
        if ($invite->status === Invite::STATUS_USED) throw new HttpException(400, 'Инвайт уже использован');

        $user->load(\Yii::$app->request->post(), '');
        $user->password = \Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('password'));

        if (! $user->save()) throw new BadRequestHttpException('Некорректно заполнена форма');

        $invite->setUsedBy($user)->save();

        \Yii::$app->response->setStatusCode(201);

        return $user;
    }

    public function actionEdit($id)
    {
        $id = (int) $id;

        if ($id !== \Yii::$app->user->id) throw new BadRequestHttpException('Нельзя редактировать чужой профиль', 401);

        $user = User::findOne($id);
        $user->load(\Yii::$app->request->post(), false);

        if (! $user->save()) throw new BadRequestHttpException('Некорректно заполнена форма');

        \Yii::$app->response->setStatusCode(200);

        return $user;
    }

    public function actionView($id)
    {
        return User::findOne((int) $id);
    }

    public function actionList()
    {
        $limit = \Yii::$app->request->get('limit', 10);
        $offset = \Yii::$app->request->get('offset', 0);
        return User::find()
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

    public function actionMe() {
        return \Yii::$app->user->identity;
    }

    public function actionUploadAvatar() {
        $model = \Yii::$app->user->identity;

        $model->uploadAvatar(UploadedFile::getInstanceByName('file'));

        return $model;
    }


}
