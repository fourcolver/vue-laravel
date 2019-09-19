<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class DistrictApiTest extends TestCase
{
    use MakeDistrictTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDistrict()
    {
        $district = $this->fakeDistrictData();
        $this->json('POST', '/api/v1/districts', $district);

        $this->assertApiResponse($district);
    }

    /**
     * @test
     */
    public function testReadDistrict()
    {
        $district = $this->makeDistrict();
        $this->json('GET', '/api/v1/districts/' . $district->id);

        $this->assertApiResponse($district->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDistrict()
    {
        $district = $this->makeDistrict();
        $editedDistrict = $this->fakeDistrictData();

        $this->json('PUT', '/api/v1/districts/' . $district->id, $editedDistrict);

        $this->assertApiResponse($editedDistrict);
    }

    /**
     * @test
     */
    public function testDeleteDistrict()
    {
        $district = $this->makeDistrict();
        $this->json('DELETE', '/api/v1/districts/' . $district->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/districts/' . $district->id);

        $this->assertResponseStatus(404);
    }
}
