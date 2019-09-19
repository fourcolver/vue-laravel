<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceRequestCategoryApiTest extends TestCase
{
    use MakeServiceRequestCategoryTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateServiceRequestCategory()
    {
        $serviceRequestCategory = $this->fakeServiceRequestCategoryData();
        $this->json('POST', '/api/v1/serviceRequestCategories', $serviceRequestCategory);

        $this->assertApiResponse($serviceRequestCategory);
    }

    /**
     * @test
     */
    public function testReadServiceRequestCategory()
    {
        $serviceRequestCategory = $this->makeServiceRequestCategory();
        $this->json('GET', '/api/v1/serviceRequestCategories/'.$serviceRequestCategory->id);

        $this->assertApiResponse($serviceRequestCategory->toArray());
    }

    /**
     * @test
     */
    public function testUpdateServiceRequestCategory()
    {
        $serviceRequestCategory = $this->makeServiceRequestCategory();
        $editedServiceRequestCategory = $this->fakeServiceRequestCategoryData();

        $this->json('PUT', '/api/v1/serviceRequestCategories/'.$serviceRequestCategory->id, $editedServiceRequestCategory);

        $this->assertApiResponse($editedServiceRequestCategory);
    }

    /**
     * @test
     */
    public function testDeleteServiceRequestCategory()
    {
        $serviceRequestCategory = $this->makeServiceRequestCategory();
        $this->json('DELETE', '/api/v1/serviceRequestCategories/'.$serviceRequestCategory->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/serviceRequestCategories/'.$serviceRequestCategory->id);

        $this->assertResponseStatus(404);
    }
}
