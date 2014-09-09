<?php

namespace app\controllers;

use app\models\Token;
use app\models\User;
use yii\filters\auth\QueryParamAuth;
use yii\web\BadRequestHttpException;

class UserController extends \yii\rest\Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
//        $behaviors['authenticator'] = [
//            'class' => QueryParamAuth::className(),
//            'tokenParam' => 'token',
//        ];
        return $behaviors;
    }

    public function actionLogin()
    {
        $user = User::find()
            ->where(['email' => \Yii::$app->request->post('email')])
            ->one();

        if (! $user || ! $user->isCorrectPassword(\Yii::$app->request->post('password'))) {
            throw new BadRequestHttpException(401);
        }

        $token = new Token();
        $token->user_id = $user->id;

        if (! $token->save()) {
            throw new BadRequestHttpException(401);
        }

        \Yii::$app->response->setStatusCode(201);

        return $token->key;
    }

    public function actionRegistration()
    {
        return $this->render('registration');
    }

    public function actionEdit()
    {
        return $this->render('edit');
    }

    public function actionView()
    {
        return $this->render('view');
    }

    public function actionList()
    {
        return $this->render('list');
    }



}
