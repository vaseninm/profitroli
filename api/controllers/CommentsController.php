<?php

namespace app\controllers;

use app\models\Comment;
use app\models\Post;
use yii\filters\auth\QueryParamAuth;
use yii\web\BadRequestHttpException;

class CommentsController extends \yii\rest\Controller
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
            'only' => ['create'],
        ];
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::className(),
        ];
        return $behaviors;
    }

    public function actionCreate($post)
    {
        $post = Post::findOne($post);

        if (! $post) throw new BadRequestHttpException('Post not found', 404);

        $comment = new Comment();

        $comment->post_id = $post->id;
        $comment->author_id = \Yii::$app->user->id;

        $comment->text = strip_tags(\Yii::$app->request->post('text'));

        if (! $comment->save()) throw new BadRequestHttpException('Error ' . json_encode($post->errors), 401);

        \Yii::$app->response->setStatusCode(201);

        return $comment;
    }

    public function actionList($post)
    {
        $post = Post::findOne($post);

        if (! $post) throw new BadRequestHttpException('Post not found', 404);

        $limit = (\Yii::$app->request->get('limit') <= 100) ? \Yii::$app->request->get('limit', 30) : 100;
        $offset = \Yii::$app->request->get('offset', 0);

        \Yii::$app->response->setStatusCode(200);

        return $post->getComments()
            ->orderBy(['create_date' => SORT_ASC])
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

}
