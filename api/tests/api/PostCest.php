<?php
use \ApiTester;

class PostCest
{
    const ID = 1;
    const EMAIL = 'vaseninm@gmail.com';
    const PASSWORD = '123qwe';

    private $token;
    private $post;

    public function _before(ApiTester $I)
    {
        if (! $this->token) {
            $I->sendPOST('/users/login', [
                'email' => PostCest::EMAIL,
                'password' => PostCest::PASSWORD,
            ]);

            $this->token = trim($I->grabResponse(), '"');
        }
    }

    // tests
    public function create(ApiTester $I)
    {
        $I->sendPOST('/posts?token=' . $this->token, [
            'title' => 'Тестовый пост',
            'text' => 'Содержание тестового поста',
        ]);

        $this->post = $I->grabDataFromJsonResponse('');

        $I->seeResponseCodeIs(201);
    }

    /**
     * @depends create
     */
    public function edit(ApiTester $I)
    {
        $I->sendPUT('/posts/'. $this->post['id'] .'?token=' . $this->token, [
            'title' => 'Отредактированный тестовый пост',
            'text' => 'Содержание отредактированного тестового поста',
        ]);

        $I->seeResponseCodeIs(200);
    }

    /**
     * @depends create
     */
    public function getOne(ApiTester $I)
    {
        $I->sendGET('/posts/'. $this->post['id']);

        $I->seeResponseCodeIs(200);
    }

    public function getList(ApiTester $I)
    {
        $I->sendGET('/posts');

        $I->seeResponseCodeIs(200);
    }

    /**
     * @depends create
     */
    public function postComment(ApiTester $I) {
        $I->sendPOST('/posts/' . $this->post['id'] . '/comments?token=' . $this->token, [
            'text' => 'Привет, меня зовут Матвей и это мой тестовый комментарий. :)'
        ]);

        $I->seeResponseCodeIs(201);
    }

    /**
     * @depends create
     */
    public function getComments(ApiTester $I) {
        $I->sendGET('/posts/' . $this->post['id'] . '/comments');

        $I->seeResponseCodeIs(200);
    }
}