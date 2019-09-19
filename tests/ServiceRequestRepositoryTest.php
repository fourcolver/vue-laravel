<?php

use App\Models\ServiceRequest;
use App\Repositories\ServiceRequestRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ServiceRequestRepositoryTest extends TestCase
{
    use MakeServiceRequestTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ServiceRequestRepository
     */
    protected $serviceRequestRepo;

    public function setUp()
    {
        parent::setUp();
        $this->serviceRequestRepo = App::make(ServiceRequestRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateServiceRequest()
    {
        $serviceRequest = $this->fakeServiceRequestData();
        $createdServiceRequest = $this->serviceRequestRepo->create($serviceRequest);
        $createdServiceRequest = $createdServiceRequest->toArray();
        $this->assertArrayHasKey('id', $createdServiceRequest);
        $this->assertNotNull($createdServiceRequest['id'], 'Created ServiceRequest must have id specified');
        $this->assertNotNull(ServiceRequest::find($createdServiceRequest['id']), 'ServiceRequest with given id must be in DB');
        $this->assertModelData($serviceRequest, $createdServiceRequest);
    }

    /**
     * @test read
     */
    public function testReadServiceRequest()
    {
        $serviceRequest = $this->makeServiceRequest();
        $dbServiceRequest = $this->serviceRequestRepo->find($serviceRequest->id);
        $dbServiceRequest = $dbServiceRequest->toArray();
        $this->assertModelData($serviceRequest->toArray(), $dbServiceRequest);
    }

    /**
     * @test update
     */
    public function testUpdateServiceRequest()
    {
        $serviceRequest = $this->makeServiceRequest();
        $fakeServiceRequest = $this->fakeServiceRequestData();
        $updatedServiceRequest = $this->serviceRequestRepo->update($fakeServiceRequest, $serviceRequest->id);
        $this->assertModelData($fakeServiceRequest, $updatedServiceRequest->toArray());
        $dbServiceRequest = $this->serviceRequestRepo->find($serviceRequest->id);
        $this->assertModelData($fakeServiceRequest, $dbServiceRequest->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteServiceRequest()
    {
        $serviceRequest = $this->makeServiceRequest();
        $resp = $this->serviceRequestRepo->delete($serviceRequest->id);
        $this->assertTrue($resp);
        $this->assertNull(ServiceRequest::find($serviceRequest->id), 'ServiceRequest should not exist in DB');
    }
}
