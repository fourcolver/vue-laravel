<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BuildingApiTest extends TestCase
{
    use MakeBuildingTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBuilding()
    {
        $building = $this->fakeBuildingData();
        $this->json('POST', '/api/v1/buildings', $building);

        $this->assertApiResponse($building);
    }

    /**
     * @test
     */
    public function testReadBuilding()
    {
        $building = $this->makeBuilding();
        $this->json('GET', '/api/v1/buildings/'.$building->id);

        $this->assertApiResponse($building->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBuilding()
    {
        $building = $this->makeBuilding();
        $editedBuilding = $this->fakeBuildingData();

        $this->json('PUT', '/api/v1/buildings/'.$building->id, $editedBuilding);

        $this->assertApiResponse($editedBuilding);
    }

    /**
     * @test
     */
    public function testDeleteBuilding()
    {
        $building = $this->makeBuilding();
        $this->json('DELETE', '/api/v1/buildings/'.$building->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/buildings/'.$building->id);

        $this->assertResponseStatus(404);
    }
}
