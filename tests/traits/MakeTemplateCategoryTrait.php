<?php

use App\Models\TemplateCategory;
use App\Repositories\TemplateCategoryRepository;
use Faker\Factory as Faker;

trait MakeTemplateCategoryTrait
{
    /**
     * Create fake instance of TemplateCategory and save it in database
     *
     * @param array $templateCategoryFields
     * @return TemplateCategory
     */
    public function makeTemplateCategory($templateCategoryFields = [])
    {
        /** @var TemplateCategoryRepository $templateCategoryRepo */
        $templateCategoryRepo = App::make(TemplateCategoryRepository::class);
        $theme = $this->fakeTemplateCategoryData($templateCategoryFields);
        return $templateCategoryRepo->create($theme);
    }

    /**
     * Get fake data of TemplateCategory
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTemplateCategoryData($templateCategoryFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'parent_id' => $fake->randomDigitNotNull,
            'name' => $fake->word,
            'description' => $fake->text,
            'tags' => $fake->text,
            'created_at' => $fake->word,
            'updated_at' => $fake->word
        ], $templateCategoryFields);
    }

    /**
     * Get fake instance of TemplateCategory
     *
     * @param array $templateCategoryFields
     * @return TemplateCategory
     */
    public function fakeTemplateCategory($templateCategoryFields = [])
    {
        return new TemplateCategory($this->fakeTemplateCategoryData($templateCategoryFields));
    }
}
