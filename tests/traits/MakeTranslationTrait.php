<?php

use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Faker\Factory as Faker;

trait MakeTranslationTrait
{
    /**
     * Create fake instance of Translation and save it in database
     *
     * @param array $translationFields
     * @return Translation
     */
    public function makeTranslation($translationFields = [])
    {
        /** @var TranslationRepository $translationRepo */
        $translationRepo = App::make(TranslationRepository::class);
        $theme = $this->fakeTranslationData($translationFields);
        return $translationRepo->create($theme);
    }

    /**
     * Get fake data of Translation
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTranslationData($translationFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'object_type' => $fake->word,
            'object_id' => $fake->randomDigitNotNull,
            'language' => $fake->word,
            'name' => $fake->word,
            'value' => $fake->text,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $translationFields);
    }

    /**
     * Get fake instance of Translation
     *
     * @param array $translationFields
     * @return Translation
     */
    public function fakeTranslation($translationFields = [])
    {
        return new Translation($this->fakeTranslationData($translationFields));
    }
}
