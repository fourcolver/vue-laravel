<?php

namespace App\Traits;

use App\Models\Translation;
use Config;
use Validator;

/**
 * Trait Translatable
 * @package App\Traits
 */
trait Translatable
{
    /**
     *
     */
    public static function bootTranslatable()
    {
        // Delete associated images if they exist.
        static::saved(function ($model) {
            $model->saveTranslations();
        });
    }

    /**
     * @return array
     */
    abstract public function translatable(): array;

    /**
     * Attach translations to this model.
     *
     * @return bool
     */
    public function saveTranslations()
    {
        $fields = $this->translatable();
        if (!count($fields)) {
            return null;
        }

        $translations = request()->get('translations');

        $languages = Config::get('app.locales');
        $langShort = array_keys($languages);

        foreach ($langShort as $lang) {
            if (!isset($translations[$lang])) {
                continue;
            }

            foreach ($fields as $field) {
                if (!isset($translations[$lang][$field])) {
                    continue;
                }

                $this->saveTranslation($translations[$lang][$field], $field, $lang);
            }
        }

        return true;
    }

    /**
     * Attach translation to this model.
     *
     * @param string $translation
     * @param string $field
     * @param string $lang
     *
     */
    public function saveTranslation($translation, $field, $lang)
    {
        $input = [
            'object_type' => $this->table,
            'object_id' => $this->id,
            'language' => $lang,
            'name' => $field,
            'value' => $translation
        ];

        $condition = [
            'object_type' => $this->table,
            'object_id' => $this->id,
            'language' => $lang,
            'name' => $field,
        ];

        (new Translation)->updateOrCreate($condition, $input);
    }

    /**
     * @param $request
     * @return  array
     */
    public function applyTranslation($request)
    {
        $result = [];
        if (!$translations = $request->get('translations')) {
            return $result;
        }

        $appLanguage = Config::get('app.locale');
        if (!isset($translations[$appLanguage])) {
            return $result;
        }

        $fields = $this->translatable();
        foreach ($fields as $field) {
            if (isset($translations[$appLanguage][$field])) {
                $result[$field] = $translations[$appLanguage][$field];
            }
        }

        return $result;
    }

    /**
     * @return mixed
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'object')->orderBy('language');
    }
}
