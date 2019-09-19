<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserSettingsApiTest extends TestCase
{
    use MakeUserSettingsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUserSettings()
    {
        $userSettings = $this->fakeUserSettingsData();
        $this->json('POST', '/api/v1/userSettings', $userSettings);

        $this->assertApiResponse($userSettings);
    }

    /**
     * @test
     */
    public function testReadUserSettings()
    {
        $userSettings = $this->makeUserSettings();
        $this->json('GET', '/api/v1/userSettings/'.$userSettings->id);

        $this->assertApiResponse($userSettings->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUserSettings()
    {
        $userSettings = $this->makeUserSettings();
        $editedUserSettings = $this->fakeUserSettingsData();

        $this->json('PUT', '/api/v1/userSettings/'.$userSettings->id, $editedUserSettings);

        $this->assertApiResponse($editedUserSettings);
    }

    /**
     * @test
     */
    public function testDeleteUserSettings()
    {
        $userSettings = $this->makeUserSettings();
        $this->json('DELETE', '/api/v1/userSettings/'.$userSettings->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/userSettings/'.$userSettings->id);

        $this->assertResponseStatus(404);
    }
}
