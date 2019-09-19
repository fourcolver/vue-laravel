<?php

use App\Models\ServiceProvider;
use App\Repositories\ServiceProviderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceProviderRepositoryTest extends TestCase
{
    use MakeServiceProviderTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ServiceProviderRepository
     */
    protected $serviceProviderRepo;

    public function setUp()
    {
        parent::setUp();
        $this->serviceProviderRepo = App::make(ServiceProviderRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateServiceProvider()
    {
        $serviceProvider = $this->fakeServiceProviderData();
        $createdServiceProvider = $this->serviceProviderRepo->create($serviceProvider);
        $createdServiceProvider = $createdServiceProvider->toArray();
        $this->assertArrayHasKey('id', $createdServiceProvider);
        $this->assertNotNull($createdServiceProvider['id'], 'Created ServiceProvider must have id specified');
        $this->assertNotNull(ServiceProvider::find($createdServiceProvider['id']), 'ServiceProvider with given id must be in DB');
        $this->assertModelData($serviceProvider, $createdServiceProvider);
    }

    /**
     * @test read
     */
    public function testReadServiceProvider()
    {
        $serviceProvider = $this->makeServiceProvider();
        $dbServiceProvider = $this->serviceProviderRepo->find($serviceProvider->id);
        $dbServiceProvider = $dbServiceProvider->toArray();
        $this->assertModelData($serviceProvider->toArray(), $dbServiceProvider);
    }

    /**
     * @test update
     */
    public function testUpdateServiceProvider()
    {
        $serviceProvider = $this->makeServiceProvider();
        $fakeServiceProvider = $this->fakeServiceProviderData();
        $updatedServiceProvider = $this->serviceProviderRepo->update($fakeServiceProvider, $serviceProvider->id);
        $this->assertModelData($fakeServiceProvider, $updatedServiceProvider->toArray());
        $dbServiceProvider = $this->serviceProviderRepo->find($serviceProvider->id);
        $this->assertModelData($fakeServiceProvider, $dbServiceProvider->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteServiceProvider()
    {
        $serviceProvider = $this->makeServiceProvider();
        $resp = $this->serviceProviderRepo->delete($serviceProvider->id);
        $this->assertTrue($resp);
        $this->assertNull(ServiceProvider::find($serviceProvider->id), 'ServiceProvider should not exist in DB');
    }
}
