<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RealEstateApiTest extends TestCase
{
    use MakeRealEstateTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateRealEstate()
    {
        $realEstate = $this->fakeRealEstateData();
        $this->json('POST', '/api/v1/realEstates', $realEstate);

        $this->assertApiResponse($realEstate);
    }

    /**
     * @test
     */
    public function testReadRealEstate()
    {
        $realEstate = $this->makeRealEstate();
        $this->json('GET', '/api/v1/realEstates/'.$realEstate->id);

        $this->assertApiResponse($realEstate->toArray());
    }

    /**
     * @test
     */
    public function testUpdateRealEstate()
    {
        $realEstate = $this->makeRealEstate();
        $editedRealEstate = $this->fakeRealEstateData();

        $this->json('PUT', '/api/v1/realEstates/'.$realEstate->id, $editedRealEstate);

        $this->assertApiResponse($editedRealEstate);
    }

    /**
     * @test
     */
    public function testDeleteRealEstate()
    {
        $realEstate = $this->makeRealEstate();
        $this->json('DELETE', '/api/v1/realEstates/'.$realEstate->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/realEstates/'.$realEstate->id);

        $this->assertResponseStatus(404);
    }
}
