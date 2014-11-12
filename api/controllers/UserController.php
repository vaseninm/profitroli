<?php

namespace app\controllers;

use app\models\Invite;
use app\models\Token;
use app\models\User;
use yii\filters\auth\QueryParamAuth;
use yii\web\BadRequestHttpException;

class UserController extends \yii\rest\Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'token',
            'except' => ['login', 'registration'],
        ];
        return $behaviors;
    }

    public function actionLogin()
    {
        $user = User::find()
            ->where(['email' => \Yii::$app->request->post('email')])
            ->one();

        if (! $user || ! $user->isCorrectPassword(\Yii::$app->request->post('password'))) {
            throw new BadRequestHttpException('Password not correct', 401);
        }

        $token = new Token();
        $token->user_id = $user->id;

        if (! $token->save()) {
            throw new BadRequestHttpException('Error ' . json_encode($token->errors), 401);
        }

        \Yii::$app->response->setStatusCode(201);

        return $token->key;
    }

    public function actionRegistration()
    {
        $user = new User();
        $invite = Invite::findOne(['key' => \Yii::$app->request->post('invite')]);

        if (! $invite) throw new BadRequestHttpException('Invite not found', 401);
        if ($invite->status === Invite::STATUS_USED) throw new BadRequestHttpException('Invite is used', 401);

        $user->load(\Yii::$app->request->post(), '');
        $user->password = \Yii::$app->security->generatePasswordHash(\Yii::$app->request->post('password'));

        if (! $user->save()) throw new BadRequestHttpException('Error ' . json_encode($user->errors), 401);

        $invite->setUsedBy($user)->save();

        \Yii::$app->response->setStatusCode(201);

        return $user;
    }

    public function actionEdit($id)
    {
        $id = (int) $id;

        if ($id !== \Yii::$app->user->id) throw new BadRequestHttpException('Not own profile', 401);

        $user = User::findOne($id);
        $user->load(\Yii::$app->request->post(), false);

        if (! $user->save()) throw new BadRequestHttpException('Error ' . json_encode($user->errors), 401);

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



}
