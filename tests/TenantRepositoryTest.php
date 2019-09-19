<?php

use App\Models\Tenant;
use App\Repositories\TenantRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantRepositoryTest extends TestCase
{
    use MakeTenantTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TenantRepository
     */
    protected $tenantRepo;

    public function setUp()
    {
        parent::setUp();
        $this->tenantRepo = App::make(TenantRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTenant()
    {
        $tenant = $this->fakeTenantData();
        $createdTenant = $this->tenantRepo->create($tenant);
        $createdTenant = $createdTenant->toArray();
        $this->assertArrayHasKey('id', $createdTenant);
        $this->assertNotNull($createdTenant['id'], 'Created Tenant must have id specified');
        $this->assertNotNull(Tenant::find($createdTenant['id']), 'Tenant with given id must be in DB');
        $this->assertModelData($tenant, $createdTenant);
    }

    /**
     * @test read
     */
    public function testReadTenant()
    {
        $tenant = $this->makeTenant();
        $dbTenant = $this->tenantRepo->find($tenant->id);
        $dbTenant = $dbTenant->toArray();
        $this->assertModelData($tenant->toArray(), $dbTenant);
    }

    /**
     * @test update
     */
    public function testUpdateTenant()
    {
        $tenant = $this->makeTenant();
        $fakeTenant = $this->fakeTenantData();
        $updatedTenant = $this->tenantRepo->update($fakeTenant, $tenant->id);
        $this->assertModelData($fakeTenant, $updatedTenant->toArray());
        $dbTenant = $this->tenantRepo->find($tenant->id);
        $this->assertModelData($fakeTenant, $dbTenant->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTenant()
    {
        $tenant = $this->makeTenant();
        $resp = $this->tenantRepo->delete($tenant->id);
        $this->assertTrue($resp);
        $this->assertNull(Tenant::find($tenant->id), 'Tenant should not exist in DB');
    }
}
