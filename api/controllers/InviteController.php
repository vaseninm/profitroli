<?php

namespace app\controllers;

use app\models\Invite;
use yii\filters\auth\QueryParamAuth;

class InviteController extends \yii\rest\Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'token',
        ];
        return $behaviors;
    }

    public function actionCreate()
    {
        $invite = new Invite();

        $invite->inviter_id = \Yii::$app->user->id;
        $invite->email = \Yii::$app->request->post('email');

        $invite->save();

        \Yii::$app->response->setStatusCode(201);

        return $invite->key;
    }

}
