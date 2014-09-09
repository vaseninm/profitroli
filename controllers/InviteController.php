<?php

namespace app\controllers;

use app\models\Invite;
use yii\filters\auth\HttpBearerAuth;

class InviteController extends \yii\rest\Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }

    public function actionCreate()
    {
        $invite = new Invite();

        $invite->inviter_id = \Yii::$app->user->id;
        $invite->email = \Yii::$app->request->post('email');

        $invite->save();

        return $invite->key;
    }

}
