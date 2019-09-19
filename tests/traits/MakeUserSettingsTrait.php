<?php

use Faker\Factory as Faker;
use App\Models\UserSettings;
use App\Repositories\UserSettingsRepository;

trait MakeUserSettingsTrait
{
    /**
     * Create fake instance of UserSettings and save it in database
     *
     * @param array $userSettingsFields
     * @return UserSettings
     */
    public function makeUserSettings($userSettingsFields = [])
    {
        /** @var UserSettingsRepository $userSettingsRepo */
        $userSettingsRepo = App::make(UserSettingsRepository::class);
        $theme = $this->fakeUserSettingsData($userSettingsFields);
        return $userSettingsRepo->create($theme);
    }

    /**
     * Get fake instance of UserSettings
     *
     * @param array $userSettingsFields
     * @return UserSettings
     */
    public function fakeUserSettings($userSettingsFields = [])
    {
        return new UserSettings($this->fakeUserSettingsData($userSettingsFields));
    }

    /**
     * Get fake data of UserSettings
     *
     * @param array $postFields
     * @return array
     */
    public function fakeUserSettingsData($userSettingsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'user_id' => $fake->randomDigitNotNull,
            'language' => $fake->word,
            'summary' => $fake->word,
            'admin_notification' => $fake->randomDigitNotNull,
            'news_notification' => $fake->randomDigitNotNull,
            'service_notification' => $fake->randomDigitNotNull,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $userSettingsFields);
    }
}
