<?php

use App\Models\Template;
use App\Repositories\TemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplateRepositoryTest extends TestCase
{
    use MakeTemplateTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TemplateRepository
     */
    protected $templateRepo;

    public function setUp()
    {
        parent::setUp();
        $this->templateRepo = App::make(TemplateRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTemplate()
    {
        $template = $this->fakeTemplateData();
        $createdTemplate = $this->templateRepo->create($template);
        $createdTemplate = $createdTemplate->toArray();
        $this->assertArrayHasKey('id', $createdTemplate);
        $this->assertNotNull($createdTemplate['id'], 'Created Template must have id specified');
        $this->assertNotNull(Template::find($createdTemplate['id']), 'Template with given id must be in DB');
        $this->assertModelData($template, $createdTemplate);
    }

    /**
     * @test read
     */
    public function testReadTemplate()
    {
        $template = $this->makeTemplate();
        $dbTemplate = $this->templateRepo->find($template->id);
        $dbTemplate = $dbTemplate->toArray();
        $this->assertModelData($template->toArray(), $dbTemplate);
    }

    /**
     * @test update
     */
    public function testUpdateTemplate()
    {
        $template = $this->makeTemplate();
        $fakeTemplate = $this->fakeTemplateData();
        $updatedTemplate = $this->templateRepo->update($fakeTemplate, $template->id);
        $this->assertModelData($fakeTemplate, $updatedTemplate->toArray());
        $dbTemplate = $this->templateRepo->find($template->id);
        $this->assertModelData($fakeTemplate, $dbTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTemplate()
    {
        $template = $this->makeTemplate();
        $resp = $this->templateRepo->delete($template->id);
        $this->assertTrue($resp);
        $this->assertNull(Template::find($template->id), 'Template should not exist in DB');
    }
}
