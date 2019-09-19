<?php

use App\Models\PropertyManager;
use App\Repositories\PropertyManagerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PropertyManagerRepositoryTest extends TestCase
{
    use MakePropertyManagerTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PropertyManagerRepository
     */
    protected $propertyManagerRepo;

    public function setUp()
    {
        parent::setUp();
        $this->propertyManagerRepo = App::make(PropertyManagerRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePropertyManager()
    {
        $propertyManager = $this->fakePropertyManagerData();
        $createdPropertyManager = $this->propertyManagerRepo->create($propertyManager);
        $createdPropertyManager = $createdPropertyManager->toArray();
        $this->assertArrayHasKey('id', $createdPropertyManager);
        $this->assertNotNull($createdPropertyManager['id'], 'Created PropertyManager must have id specified');
        $this->assertNotNull(PropertyManager::find($createdPropertyManager['id']), 'PropertyManager with given id must be in DB');
        $this->assertModelData($propertyManager, $createdPropertyManager);
    }

    /**
     * @test read
     */
    public function testReadPropertyManager()
    {
        $propertyManager = $this->makePropertyManager();
        $dbPropertyManager = $this->propertyManagerRepo->find($propertyManager->id);
        $dbPropertyManager = $dbPropertyManager->toArray();
        $this->assertModelData($propertyManager->toArray(), $dbPropertyManager);
    }

    /**
     * @test update
     */
    public function testUpdatePropertyManager()
    {
        $propertyManager = $this->makePropertyManager();
        $fakePropertyManager = $this->fakePropertyManagerData();
        $updatedPropertyManager = $this->propertyManagerRepo->update($fakePropertyManager, $propertyManager->id);
        $this->assertModelData($fakePropertyManager, $updatedPropertyManager->toArray());
        $dbPropertyManager = $this->propertyManagerRepo->find($propertyManager->id);
        $this->assertModelData($fakePropertyManager, $dbPropertyManager->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePropertyManager()
    {
        $propertyManager = $this->makePropertyManager();
        $resp = $this->propertyManagerRepo->delete($propertyManager->id);
        $this->assertTrue($resp);
        $this->assertNull(PropertyManager::find($propertyManager->id), 'PropertyManager should not exist in DB');
    }
}
