<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TranslationApiTest extends TestCase
{
    use MakeTranslationTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTranslation()
    {
        $translation = $this->fakeTranslationData();
        $this->json('POST', '/api/v1/translations', $translation);

        $this->assertApiResponse($translation);
    }

    /**
     * @test
     */
    public function testReadTranslation()
    {
        $translation = $this->makeTranslation();
        $this->json('GET', '/api/v1/translations/' . $translation->id);

        $this->assertApiResponse($translation->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTranslation()
    {
        $translation = $this->makeTranslation();
        $editedTranslation = $this->fakeTranslationData();

        $this->json('PUT', '/api/v1/translations/' . $translation->id, $editedTranslation);

        $this->assertApiResponse($editedTranslation);
    }

    /**
     * @test
     */
    public function testDeleteTranslation()
    {
        $translation = $this->makeTranslation();
        $this->json('DELETE', '/api/v1/translations/' . $translation->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/translations/' . $translation->id);

        $this->assertResponseStatus(404);
    }
}
