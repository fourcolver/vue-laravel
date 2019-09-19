<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TenantApiTest extends TestCase
{
    use MakeTenantTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTenant()
    {
        $tenant = $this->fakeTenantData();
        $this->json('POST', '/api/v1/tenants', $tenant);

        $this->assertApiResponse($tenant);
    }

    /**
     * @test
     */
    public function testReadTenant()
    {
        $tenant = $this->makeTenant();
        $this->json('GET', '/api/v1/tenants/'.$tenant->id);

        $this->assertApiResponse($tenant->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTenant()
    {
        $tenant = $this->makeTenant();
        $editedTenant = $this->fakeTenantData();

        $this->json('PUT', '/api/v1/tenants/'.$tenant->id, $editedTenant);

        $this->assertApiResponse($editedTenant);
    }

    /**
     * @test
     */
    public function testDeleteTenant()
    {
        $tenant = $this->makeTenant();
        $this->json('DELETE', '/api/v1/tenants/'.$tenant->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/tenants/'.$tenant->id);

        $this->assertResponseStatus(404);
    }
}
