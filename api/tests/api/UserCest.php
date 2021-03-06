<?php
use \ApiTester;

class UserCest
{
    const ID = 1;
    const EMAIL = 'vaseninm@gmail.com';
    const PASSWORD = '123qwe';

    /**
     * @var string
     */
    private $token = null;
    /**
     * @var array
     */
    private $invite = null;
    /**
     * @var array
     */
    private $user = null;

    public function _before(ApiTester $I)
    {
    }

    public function _after(ApiTester $I)
    {
    }

    public function successLogin(ApiTester $I)
    {
        $I->wantToTest('Успешную авторизацию уже созданого пользователя.');
        $I->sendPOST('/users/login', [
            'email' => UserCest::EMAIL,
            'password' => UserCest::PASSWORD,
        ]);
        $I->seeResponseCodeIs(201);
        $this->token = trim($I->grabResponse(), '"');
    }

    /**
     * @depends successLogin
     * @param ApiTester $I
     */
    public function successSendInvite(ApiTester $I)
    {
        $I->wantToTest('Успешную отправку нового инвайта для пользователя');
        $I->sendPost('/invites?token=' . $this->token, [
            'email' => UserCest::EMAIL,
        ]);
        $I->seeResponseCodeIs(201);
        $this->invite = $I->grabDataFromJsonResponse('');
    }

    /**
     * @depends successSendInvite
     * @param ApiTester $I
     */
    public function successReg(ApiTester $I)
    {
        $username = substr(uniqid(rand(),1), 3, 14);
        $I->wantToTest('Успешную регистрацию нового пользователя');
        $I->sendPOST('/users', [
            'invite' => $this->invite,
            'email' => $username . '@profitroli',
            'name' => $username,
            'phone' => rand(7915000001, 79159999999),
            'password' => base64_encode($username),
        ]);
        $I->seeResponseCodeIs(201);
        $this->user = $I->grabDataFromJsonResponse('');
    }

    /**
     * @depends successReg
     * @param ApiTester $I
     */
    public function successEdit(ApiTester $I)
    {
        $I->wantToTest('Смену телефона у пользователя');
        $I->sendPUT('/users/' . UserCest::ID . '?token=' . $this->token, [
            'phone' => rand(7915000001, 79159999999),
        ]);
        $I->seeResponseCodeIs(200);
        $user = $I->grabDataFromJsonResponse('');
    }

    /**
     * @depends successReg
     * @param ApiTester $I
     */
    public function successGetInfo(ApiTester $I)
    {
        $I->wantToTest('Получение информации о пользователе');
        $I->sendGet('/users/' . $this->user['id'] . '?token=' . $this->token, []);
        $I->seeResponseCodeIs(200);
        $user = $I->grabDataFromJsonResponse('');
    }

    /**
     * @depends successReg
     * @param ApiTester $I
     */
    public function successGetList(ApiTester $I)
    {
        $I->wantToTest('Получение списка пользователей');
        $I->sendGet('/users?token=' . $this->token, []);
        $I->seeResponseCodeIs(200);
        $user = $I->grabDataFromJsonResponse('');
    }
}