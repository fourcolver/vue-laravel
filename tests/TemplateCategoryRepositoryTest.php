<?php

use App\Models\TemplateCategory;
use App\Repositories\TemplateCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TemplateCategoryRepositoryTest extends TestCase
{
    use MakeTemplateCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TemplateCategoryRepository
     */
    protected $templateCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->templateCategoryRepo = App::make(TemplateCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTemplateCategory()
    {
        $templateCategory = $this->fakeTemplateCategoryData();
        $createdTemplateCategory = $this->templateCategoryRepo->create($templateCategory);
        $createdTemplateCategory = $createdTemplateCategory->toArray();
        $this->assertArrayHasKey('id', $createdTemplateCategory);
        $this->assertNotNull($createdTemplateCategory['id'], 'Created TemplateCategory must have id specified');
        $this->assertNotNull(TemplateCategory::find($createdTemplateCategory['id']),
            'TemplateCategory with given id must be in DB');
        $this->assertModelData($templateCategory, $createdTemplateCategory);
    }

    /**
     * @test read
     */
    public function testReadTemplateCategory()
    {
        $templateCategory = $this->makeTemplateCategory();
        $dbTemplateCategory = $this->templateCategoryRepo->find($templateCategory->id);
        $dbTemplateCategory = $dbTemplateCategory->toArray();
        $this->assertModelData($templateCategory->toArray(), $dbTemplateCategory);
    }

    /**
     * @test update
     */
    public function testUpdateTemplateCategory()
    {
        $templateCategory = $this->makeTemplateCategory();
        $fakeTemplateCategory = $this->fakeTemplateCategoryData();
        $updatedTemplateCategory = $this->templateCategoryRepo->update($fakeTemplateCategory, $templateCategory->id);
        $this->assertModelData($fakeTemplateCategory, $updatedTemplateCategory->toArray());
        $dbTemplateCategory = $this->templateCategoryRepo->find($templateCategory->id);
        $this->assertModelData($fakeTemplateCategory, $dbTemplateCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTemplateCategory()
    {
        $templateCategory = $this->makeTemplateCategory();
        $resp = $this->templateCategoryRepo->delete($templateCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(TemplateCategory::find($templateCategory->id), 'TemplateCategory should not exist in DB');
    }
}
