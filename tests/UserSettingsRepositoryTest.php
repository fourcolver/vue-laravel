<?php

use App\Models\UserSettings;
use App\Repositories\UserSettingsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserSettingsRepositoryTest extends TestCase
{
    use MakeUserSettingsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UserSettingsRepository
     */
    protected $userSettingsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->userSettingsRepo = App::make(UserSettingsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUserSettings()
    {
        $userSettings = $this->fakeUserSettingsData();
        $createdUserSettings = $this->userSettingsRepo->create($userSettings);
        $createdUserSettings = $createdUserSettings->toArray();
        $this->assertArrayHasKey('id', $createdUserSettings);
        $this->assertNotNull($createdUserSettings['id'], 'Created UserSettings must have id specified');
        $this->assertNotNull(UserSettings::find($createdUserSettings['id']), 'UserSettings with given id must be in DB');
        $this->assertModelData($userSettings, $createdUserSettings);
    }

    /**
     * @test read
     */
    public function testReadUserSettings()
    {
        $userSettings = $this->makeUserSettings();
        $dbUserSettings = $this->userSettingsRepo->find($userSettings->id);
        $dbUserSettings = $dbUserSettings->toArray();
        $this->assertModelData($userSettings->toArray(), $dbUserSettings);
    }

    /**
     * @test update
     */
    public function testUpdateUserSettings()
    {
        $userSettings = $this->makeUserSettings();
        $fakeUserSettings = $this->fakeUserSettingsData();
        $updatedUserSettings = $this->userSettingsRepo->update($fakeUserSettings, $userSettings->id);
        $this->assertModelData($fakeUserSettings, $updatedUserSettings->toArray());
        $dbUserSettings = $this->userSettingsRepo->find($userSettings->id);
        $this->assertModelData($fakeUserSettings, $dbUserSettings->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUserSettings()
    {
        $userSettings = $this->makeUserSettings();
        $resp = $this->userSettingsRepo->delete($userSettings->id);
        $this->assertTrue($resp);
        $this->assertNull(UserSettings::find($userSettings->id), 'UserSettings should not exist in DB');
    }
}
