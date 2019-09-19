<?php

use App\Models\ServiceRequestCategory;
use App\Repositories\ServiceRequestCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceRequestCategoryRepositoryTest extends TestCase
{
    use MakeServiceRequestCategoryTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ServiceRequestCategoryRepository
     */
    protected $serviceRequestCategoryRepo;

    public function setUp()
    {
        parent::setUp();
        $this->serviceRequestCategoryRepo = App::make(ServiceRequestCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateServiceRequestCategory()
    {
        $serviceRequestCategory = $this->fakeServiceRequestCategoryData();
        $createdServiceRequestCategory = $this->serviceRequestCategoryRepo->create($serviceRequestCategory);
        $createdServiceRequestCategory = $createdServiceRequestCategory->toArray();
        $this->assertArrayHasKey('id', $createdServiceRequestCategory);
        $this->assertNotNull($createdServiceRequestCategory['id'], 'Created ServiceRequestCategory must have id specified');
        $this->assertNotNull(ServiceRequestCategory::find($createdServiceRequestCategory['id']), 'ServiceRequestCategory with given id must be in DB');
        $this->assertModelData($serviceRequestCategory, $createdServiceRequestCategory);
    }

    /**
     * @test read
     */
    public function testReadServiceRequestCategory()
    {
        $serviceRequestCategory = $this->makeServiceRequestCategory();
        $dbServiceRequestCategory = $this->serviceRequestCategoryRepo->find($serviceRequestCategory->id);
        $dbServiceRequestCategory = $dbServiceRequestCategory->toArray();
        $this->assertModelData($serviceRequestCategory->toArray(), $dbServiceRequestCategory);
    }

    /**
     * @test update
     */
    public function testUpdateServiceRequestCategory()
    {
        $serviceRequestCategory = $this->makeServiceRequestCategory();
        $fakeServiceRequestCategory = $this->fakeServiceRequestCategoryData();
        $updatedServiceRequestCategory = $this->serviceRequestCategoryRepo->update($fakeServiceRequestCategory, $serviceRequestCategory->id);
        $this->assertModelData($fakeServiceRequestCategory, $updatedServiceRequestCategory->toArray());
        $dbServiceRequestCategory = $this->serviceRequestCategoryRepo->find($serviceRequestCategory->id);
        $this->assertModelData($fakeServiceRequestCategory, $dbServiceRequestCategory->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteServiceRequestCategory()
    {
        $serviceRequestCategory = $this->makeServiceRequestCategory();
        $resp = $this->serviceRequestCategoryRepo->delete($serviceRequestCategory->id);
        $this->assertTrue($resp);
        $this->assertNull(ServiceRequestCategory::find($serviceRequestCategory->id), 'ServiceRequestCategory should not exist in DB');
    }
}
