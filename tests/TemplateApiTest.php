<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class TemplateApiTest extends TestCase
{
    use MakeTemplateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTemplate()
    {
        $template = $this->fakeTemplateData();
        $this->json('POST', '/api/v1/templates', $template);

        $this->assertApiResponse($template);
    }

    /**
     * @test
     */
    public function testReadTemplate()
    {
        $template = $this->makeTemplate();
        $this->json('GET', '/api/v1/templates/' . $template->id);

        $this->assertApiResponse($template->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTemplate()
    {
        $template = $this->makeTemplate();
        $editedTemplate = $this->fakeTemplateData();

        $this->json('PUT', '/api/v1/templates/' . $template->id, $editedTemplate);

        $this->assertApiResponse($editedTemplate);
    }

    /**
     * @test
     */
    public function testDeleteTemplate()
    {
        $template = $this->makeTemplate();
        $this->json('DELETE', '/api/v1/templates/' . $template->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/templates/' . $template->id);

        $this->assertResponseStatus(404);
    }
}
