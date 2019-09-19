<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceRequestApiTest extends TestCase
{
    use MakeServiceRequestTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateServiceRequest()
    {
        $serviceRequest = $this->fakeServiceRequestData();
        $this->json('POST', '/api/v1/serviceRequests', $serviceRequest);

        $this->assertApiResponse($serviceRequest);
    }

    /**
     * @test
     */
    public function testReadServiceRequest()
    {
        $serviceRequest = $this->makeServiceRequest();
        $this->json('GET', '/api/v1/serviceRequests/'.$serviceRequest->id);

        $this->assertApiResponse($serviceRequest->toArray());
    }

    /**
     * @test
     */
    public function testUpdateServiceRequest()
    {
        $serviceRequest = $this->makeServiceRequest();
        $editedServiceRequest = $this->fakeServiceRequestData();

        $this->json('PUT', '/api/v1/serviceRequests/'.$serviceRequest->id, $editedServiceRequest);

        $this->assertApiResponse($editedServiceRequest);
    }

    /**
     * @test
     */
    public function testDeleteServiceRequest()
    {
        $serviceRequest = $this->makeServiceRequest();
        $this->json('DELETE', '/api/v1/serviceRequests/'.$serviceRequest->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/serviceRequests/'.$serviceRequest->id);

        $this->assertResponseStatus(404);
    }
}
