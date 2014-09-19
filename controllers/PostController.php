<?php

namespace app\controllers;

use app\models\Post;
use yii\filters\auth\QueryParamAuth;
use yii\web\BadRequestHttpException;

class PostController extends \yii\rest\Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => QueryParamAuth::className(),
            'tokenParam' => 'token',
            'except' => ['list', 'view'],
        ];
        return $behaviors;
    }

    public function actionCreate()
    {
        $post = new Post();

        $post->author_id = \Yii::$app->user->id;
        $post->title = \Yii::$app->request->post('title');
        $post->text = strip_tags(\Yii::$app->request->post('text'));

        if (! $post->save()) throw new BadRequestHttpException('Error ' . json_encode($post->errors), 401);

        \Yii::$app->response->setStatusCode(201);

        return $post;
    }

    public function actionEdit($id)
    {
        $post = Post::findOne($id);

        if ($post->author_id !== \Yii::$app->user->id) throw new BadRequestHttpException('User not post owner', 403);

        if (! $post) throw new BadRequestHttpException('Post not found', 404);

        $post->title = \Yii::$app->request->post('title');
        $post->text = strip_tags(\Yii::$app->request->post('text'));

        if (! $post->save()) throw new BadRequestHttpException('Error ' . json_encode($post->errors), 401);

        \Yii::$app->response->setStatusCode(200);

        return [];
    }

    public function actionView($id)
    {
        $post = Post::findOne($id);

        if (! $post) throw new BadRequestHttpException('Post not found', 404);

        \Yii::$app->response->setStatusCode(200);

        return $post;
    }

    public function actionList()
    {
        $limit = (\Yii::$app->request->get('limit') <= 100) ? \Yii::$app->request->get('limit', 30) : 100;
        $offset = \Yii::$app->request->get('offset', 0);

        \Yii::$app->response->setStatusCode(200);

        return Post::find()
            ->orderBy('id')
            ->limit($limit)
            ->offset($offset)
            ->all();
    }

}
