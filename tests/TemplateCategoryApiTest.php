<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TemplateCategoryApiTest extends TestCase
{
    use MakeTemplateCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTemplateCategory()
    {
        $templateCategory = $this->fakeTemplateCategoryData();
        $this->json('POST', '/api/v1/templateCategories', $templateCategory);

        $this->assertApiResponse($templateCategory);
    }

    /**
     * @test
     */
    public function testReadTemplateCategory()
    {
        $templateCategory = $this->makeTemplateCategory();
        $this->json('GET', '/api/v1/templateCategories/' . $templateCategory->id);

        $this->assertApiResponse($templateCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTemplateCategory()
    {
        $templateCategory = $this->makeTemplateCategory();
        $editedTemplateCategory = $this->fakeTemplateCategoryData();

        $this->json('PUT', '/api/v1/templateCategories/' . $templateCategory->id, $editedTemplateCategory);

        $this->assertApiResponse($editedTemplateCategory);
    }

    /**
     * @test
     */
    public function testDeleteTemplateCategory()
    {
        $templateCategory = $this->makeTemplateCategory();
        $this->json('DELETE', '/api/v1/templateCategories/' . $templateCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/templateCategories/' . $templateCategory->id);

        $this->assertResponseStatus(404);
    }
}
