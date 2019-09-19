<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceProviderApiTest extends TestCase
{
    use MakeServiceProviderTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateServiceProvider()
    {
        $serviceProvider = $this->fakeServiceProviderData();
        $this->json('POST', '/api/v1/serviceProviders', $serviceProvider);

        $this->assertApiResponse($serviceProvider);
    }

    /**
     * @test
     */
    public function testReadServiceProvider()
    {
        $serviceProvider = $this->makeServiceProvider();
        $this->json('GET', '/api/v1/serviceProviders/'.$serviceProvider->id);

        $this->assertApiResponse($serviceProvider->toArray());
    }

    /**
     * @test
     */
    public function testUpdateServiceProvider()
    {
        $serviceProvider = $this->makeServiceProvider();
        $editedServiceProvider = $this->fakeServiceProviderData();

        $this->json('PUT', '/api/v1/serviceProviders/'.$serviceProvider->id, $editedServiceProvider);

        $this->assertApiResponse($editedServiceProvider);
    }

    /**
     * @test
     */
    public function testDeleteServiceProvider()
    {
        $serviceProvider = $this->makeServiceProvider();
        $this->json('DELETE', '/api/v1/serviceProviders/'.$serviceProvider->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/serviceProviders/'.$serviceProvider->id);

        $this->assertResponseStatus(404);
    }
}
