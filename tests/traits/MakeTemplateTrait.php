<?php

use App\Models\Template;
use App\Repositories\TemplateRepository;
use Faker\Factory as Faker;

trait MakeTemplateTrait
{
    /**
     * Create fake instance of Template and save it in database
     *
     * @param array $templateFields
     * @return Template
     */
    public function makeTemplate($templateFields = [])
    {
        /** @var TemplateRepository $templateRepo */
        $templateRepo = App::make(TemplateRepository::class);
        $theme = $this->fakeTemplateData($templateFields);
        return $templateRepo->create($theme);
    }

    /**
     * Get fake data of Template
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTemplateData($templateFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'category_id' => $fake->randomDigitNotNull,
            'name' => $fake->word,
            'description' => $fake->word,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $templateFields);
    }

    /**
     * Get fake instance of Template
     *
     * @param array $templateFields
     * @return Template
     */
    public function fakeTemplate($templateFields = [])
    {
        return new Template($this->fakeTemplateData($templateFields));
    }
}
